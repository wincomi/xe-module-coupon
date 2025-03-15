<?php
    /**
     * @class  couponAdminController
     * @author zero (zero@nzeo.com)
     * @brief  coupon module의 admin controller class
     **/

    class couponAdminController extends coupon {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 설정 저장
         */
        function procCouponAdminInsertConfig() {
            $config = Context::getRequestVars();
            if(!$config->skin) $config->skin = 'default';

            $oModuleController = getController('module');
            $oModuleController->insertModuleConfig('coupon', $config);

            $this->setMessage('success_saved');
        }

        /**
         * @brief 쿠폰 발급
         **/
        function procCouponAdminInsertCoupon() {
            // 필수 정보들을 미리 추출
            $args = Context::gets('coupon_code','coupon_code2','coupon_code3','title','point','member_srl','coupon_limit','expire_date');

            // 쿠폰 코드가 정상적으로 넘어왔는지 확인
            if(!$args->coupon_code || !$args->coupon_code2 || !$args->coupon_code3) return $this->makeObject(-1, 'msg_input_coupon_code');

            $args->code = sprintf('%s-%s-%s', $args->coupon_code, $args->coupon_code2, $args->coupon_code3);

            // 발급된 쿠폰 코드인지 확인
            $oModel = getModel('coupon');
            $oCoupon = $oModel->getCouponByCode($args->code);

            // 확장 변수만 골라냄
            $extra_vars = Context::getRequestVars();
            unset($extra_vars->_filter);
            unset($extra_vars->module);
            unset($extra_vars->act);
            unset($extra_vars->body);
            unset($extra_vars->code);
            unset($extra_vars->title);
            unset($extra_vars->point);
            unset($extra_vars->member_srl);
            unset($extra_vars->coupon_code);
            unset($extra_vars->coupon_code2);
            unset($extra_vars->coupon_code3);
            unset($extra_vars->coupon_limit);
            unset($extra_vars->expire_date);

            $oCouponController = getController('coupon');

            // 발급된 쿠폰 코드일 경우 수정
            if($oCoupon->isExists()) {
                $args->coupon_srl = $oCoupon->get('coupon_srl');
                $output = $oCouponController->updateCoupon($args, $extra_vars);
            } else {
                // 아니면 새로 발급
                $output = $oCouponController->insertCoupon($args, $extra_vars);
            }

            // 에러 발생 시 에러 출력
            if(!$output->toBool()) return $output;

            // 메시지 지정
            $this->setMessage('success_coupon_inserted');
        }

        /**
         * @brief 쿠폰 코드 생성
         */
        function procCouponAdminGenerateCode() {
            // 비정상적인 접근 방지를 위해 XMLRPC로 요청된 것만 받아들임
            if(Context::getResponseMethod() != 'XMLRPC') return $this->makeObject(-1, 'msg_invalid_request');

            // 코드 생성
            $oModel = getModel('coupon');
            $code = $oModel->generateCode();

            // 생성된 코드를 분리
            list($code1, $code2, $code3) = explode('-', $code);

            // 각각의 분리된 코드를 넘겨줌
            $this->add('code', $code1);
            $this->add('code2', $code2);
            $this->add('code3', $code3);
        }

        /**
         * @brief 선택된 쿠폰을 삭제
         */
        function procCouponAdminDeleteChecked() {
            $cart = Context::get('cart');
            if(!$cart) return $this->makeObject(-1, 'msg_invalid_request');

            // DB instance 생성
            $oDB = DB::getInstance();

            // 트렌잭션 시작
            $oDB->begin();

            // 선택된 쿠폰 삭제
            $args->coupon_srl = str_replace('|@|', ',', $cart);
            $output = executeQuery('coupon.deleteCoupons', $args);

            // 에러 발생 시 에러 출력
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // 쿠폰 사용 기록 삭제
            $output = executeQuery('coupon.deleteCouponUsedLogs', $args);

            // 에러 발생 시 에러 출력
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // 메시지 지정
            $this->setMessage('success_deleted');
        }
    }
?>
