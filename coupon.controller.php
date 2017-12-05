<?php
    /**
     * @class  쿠폰(coupon)
     * @author SMaker (dowon2308@paran.com)
     * @brief  쿠폰(coupon) 모듈의 controller class
     **/

    class couponController extends coupon {
        function init() {
        }

        function procCoupon() {
            if(Context::getResponseMethod() != 'XMLRPC') return $this->makeObject(-1, 'msg_invalid_request');

            // 로그인을 하지 않았을 경우
            if(!Context::get('is_logged')) return $this->makeObject(-1, 'msg_need_login');

            // 쿠폰 코드를 입력했는지 확인
            $code = Context::get('code');
            if(!$code) return $this->makeObject(-1, 'msg_invalid_request');

            // 쿠폰이 존재하는지 확인
            $oModel = &getModel('coupon');
            $oCoupon = $oModel->getCouponByCode($code);

            // 쿠폰이 존재하지 않으면 에러
            if(!$oCoupon->isExists()) return $this->makeObject(-1, 'msg_not_exists_coupon_code');

            // 쿠폰 사용 권한이 없으면 에러
            if(!$oCoupon->isGranted()) return $this->makeObject(-1, 'msg_not_owned_coupon');

            // 이미 쿠폰을 사용했으면 에러
            if($oCoupon->isUsed()) return $this->makeObject(-1, 'msg_already_used_coupon');

            // 만료된 쿠폰이면 에러
            if($oCoupon->isExpired()) return $this->makeObject(-1, sprintf(Context::getLang('msg_cannot_use_coupon'), $oCoupon->getExpireDate()));

            // 쿠폰의 포인트를 구함
            $point = $oCoupon->getPoint();

            // 로그인 정보 구함
            $logged_info = Context::get('logged_info');

            // 쿠폰 정보를 정리
            $obj->coupon_srl = $oCoupon->get('coupon_srl');
            $obj->member_srl = $oCoupon->get('member_srl');
            $obj->code = $oCoupon->getCode();
            $obj->point = $oCoupon->getPoint();
            $obj->limit = $oCoupon->getLimit();
            $obj->regdate = $oCoupon->get('regdate');
            $obj->expire_date = $oCoupon->get('expire_date');
            $obj->extra_vars = $oCoupon->get('extra_vars');

            /**
             * 트리거 호출 (coupon.procCoupon - before)
             */
            $trigger_output = ModuleHandler::triggerCall('coupon.procCoupon', 'before', $obj);
            if(!$trigger_output->toBool()) return $trigger_output;

            // 트랜잭션 시작
            $oDB = &DB::getInstance();
            $oDB->begin();

            // 포인트 증감
            if($point) {
                $oPointController = &getController('point');
                $oPointController->setPoint($logged_info->member_srl, $point, 'add');
            }

            // 쿠폰 사용 기록 남김
            $log_args->log_srl = getNextSequence();
            $log_args->coupon_srl = $oCoupon->get('coupon_srl');
            $log_args->member_srl = $logged_info->member_srl;
            $output = executeQuery('coupon.insertCouponUsedLog', $log_args);

            // 에러 발생 시 롤백
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // 커밋
            $oDB->commit();

            /**
             * 트리거 호출 (coupon.procCoupon - after)
             */
            $trigger_output = ModuleHandler::triggerCall('coupon.procCoupon', 'after', $obj);
            if(!$trigger_output->toBool()) return $trigger_output;

            // 포인트에 따른 메시지 지정
            if($point > 0) {
                $msg_code = sprintf(Context::getLang('msg_coupon_congratulation'), $point);
            } elseif($point < 0) {
                $msg_code = sprintf(Context::getLang('msg_coupon_encourage'), $point);
            } else {
                $msg_code = 'msg_coupon_is_blank';
            }

            // 메시지 지정
            $this->setMessage($msg_code);
        }

        /**
         * @brief 응모권 추가
         */
        function addTicket($ticket_type = '', $member_srl, $count) {
            if(!$ticket_type || !$member_srl || $count<1) return;

            $oCouponModel = &getModel('coupon');

            $args->ticket_type = $ticket_type;
            $args->member_srl = $member_srl;
            $args->count = $count;

            // 등록된 응모권이 있는지 확인
            $ticket = $oCouponModel->getTicket($args);

            $insert_args->ticket_srl = $ticket->ticket_srl;
            $insert_args->ticket_type = $ticket_type;
            $insert_args->member_srl = $member_srl;
            $insert_args->count = $ticket->count + $count;

            // 응모권이 있으면 갯수를 더하고 없으면 새로 추가
            if($ticket) {
                $this->updateTicket($insert_args);
            } else {
                $this->insertTicket($insert_args);
            }
        }

        /**
         * @brief 응모권 차감
         */
        function subtractTicket($ticket_type = '', $member_srl, $count) {
            if(!$ticket_type || !$member_srl) return;

            $oCouponModel = &getModel('coupon');

            $args->ticket_type = $ticket_type;
            $args->member_srl = $member_srl;
            $args->count = $count;

            // 등록된 응모권이 있는지 확인
            $ticket = $oCouponModel->getTicket($args);
            if(!$ticket) return;

            $insert_args->ticket_srl = $ticket->ticket_srl;
            $insert_args->ticket_type = $ticket_type;
            $insert_args->member_srl = $member_srl;
            $insert_args->count = $ticket->count - $count;

            // 응모권 갯수가 음수가 되면 0으로 만들기
            if($insert_args->count<0) $insert_args->count = 0;

            $this->updateTicket($insert_args);
        }

        /**
         * @brief 응모권 추가
         */
        function insertTicket($obj) {
            $args->ticket_srl = getNextSequence();
            $args->ticket_type = $obj->ticket_type;
            $args->member_srl = $obj->member_srl;
            $args->count = $obj->count;
            return executeQuery('coupon.insertTicket', $args);
        }

        /**
         * @brief 응모권 업데이트
         */
        function updateTicket($obj) {
            $args->ticket_srl = $obj->ticket_srl;
            $args->count = $obj->count;
            return executeQuery('coupon.updateTicket', $args);
        }

        /**
         * @brief 쿠폰 추가
         */
        function insertCoupon($obj, $extra_vars) {
            if(!$obj) return;

            $args->coupon_srl = getNextSequence();
            $args->member_srl = $obj->member_srl;
            $args->title = $obj->title;
            $args->code = $obj->code;
            $args->point = $obj->point;
            $args->limit = $obj->coupon_limit;
            $args->expire_date = $obj->expire_date;
            $args->extra_vars = serialize($extra_vars);
            return executeQuery('coupon.insertCoupon', $args);
        }

        /**
         * @brief 쿠폰 수정
         */
        function updateCoupon($obj, $extra_vars) {
            if(!$obj) return;

            $args->coupon_srl = $obj->coupon_srl;
            $args->member_srl = $obj->member_srl;
            $args->title = $obj->title;
            $args->code = $obj->code;
            $args->point = $obj->point;
            $args->limit = $obj->coupon_limit;
            $args->expire_date = $obj->expire_date;
            $args->extra_vars = serialize($extra_vars);
            return executeQuery('coupon.updateCoupon', $args);
        }

        function triggerAddMemberMenu(&$obj) {
            if(!Context::get('is_logged')) return $this->makeObject();

            $oMemberController = &getController('member');
            $oMemberController->addMemberMenu('dispCouponBox', 'cmd_my_coupon_box');

            return $this->makeObject();
        }
    }
?>