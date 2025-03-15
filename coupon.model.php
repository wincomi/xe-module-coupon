<?php
    /**
     * @class  couponModel
     * @author SMaker (dowon2308@paran.com)
     * @brief 쿠폰(coupon) 모듈의 model class
     **/

    require_once(_XE_PATH_.'modules/coupon/coupon.item.php');
    require_once(_XE_PATH_.'modules/coupon/code.item.php');

    class couponModel extends coupon {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 모듈 설정을 구함
         */
        function getModuleConfig() {
            static $config = null;
            if(is_null($config)) {
                $oModuleModel = getModel('module');
                $config = $oModuleModel->getModuleConfig('coupon') ?: new stdClass;
                if(!$config->skin) $config->skin = 'default';
            }
            return $config;
        }

        function getCoupon($coupon_srl) {
            if(!$coupon_srl) return new couponItem();

            if(!isset($GLOBALS['XE_COUPON_LIST'][$coupon_srl])) {
                $oCoupon = new couponItem($coupon_srl);
                $GLOBALS['XE_COUPON_LIST'][$coupon_srl] = $oCoupon;
            }

            return $GLOBALS['XE_COUPON_LIST'][$coupon_srl];
        }

        function getCouponByCode($code) {
            if(!$code) return new couponItem();

            if(!isset($GLOBALS['XE_COUPON_LIST'][$code])) {
                $oCoupon = new couponItem();
                $oCoupon->setCode($code);
                $GLOBALS['XE_COUPON_LIST'][$code] = $oCoupon;
            }

            return $GLOBALS['XE_COUPON_LIST'][$code];
        }

        function getCouponList($obj) {
			$args = new stdClass();
            $args->sort_index = $obj->sort_index ? $obj->sort_index : 'regdate';
            $args->order_type = $obj->order_type ? $obj->order_type : 'desc';
            $args->list_count = $obj->list_count ? $obj->list_count : 20;
            $args->page_count = $obj->page_count ? $obj->page_count : 10;
            $args->page = $obj->page;

            $output = executeQueryArray('coupon.getCouponList', $args);

            // 결과가 없거나 오류 발생시 그냥 return
            if(!$output->toBool()||!count($output->data)) return $output;

            $data = $output->data;
            unset($output->data);

            foreach($data as $key => $attribute) {
                $coupon_srl = $attribute->coupon_srl;
                if(!$GLOBALS['XE_COUPON_LIST'][$coupon_srl]) {
                    $oCoupon = null;
                    $oCoupon = new couponItem();
                    $oCoupon->setAttribute($attribute);
                    $GLOBALS['XE_COUPON_LIST'][$coupon_srl] = $oCoupon;
                }

                $output->data[] = $GLOBALS['XE_COUPON_LIST'][$coupon_srl];
            }

            return $output;
        }

        function getTicket($args) {
            if(!$args->member_srl) return;

            $output = executeQuery('coupon.getTicket',$args);
            return $output->data;
        }

        function getTicketCount($args) {
            if(!$args->member_srl || !$args->ticket_type) return;

            $output = executeQuery('coupon.getTicketCount',$args);
            return $output->data->count;
        }

        /**
         * @brief 쿠폰 코드 생성 method
         * @remakrs 17자리로 구성된 쿠폰 코드를 생성
         * @return 생성된 쿠폰 코드
         */
        function generateCode($length = 17, $split_length = 5) {
            $oCode = new codeItem($length, $split_length);
            $oCode->generate();

            return $oCode->getCode();
        }

        function getMemberCouponList($obj = null) {
	        $args = new stdClass();
            $args->member_srl = $obj->member_srl;
            $args->sort_index = $obj->sort_index ? $obj->sort_index : 'regdate';
            $args->order_type = $obj->order_type ? $obj->order_type : 'desc';
            $args->page = $obj->page;
            $args->page_count = $obj->page_count ? $obj->page_count : 10;
            $args->list_count = $obj->list_count ? $obj->list_count : 20;

            $output = executeQuery('coupon.getMemberCouponList', $args);

            // 결과가 없거나 오류 발생시 그냥 return
            if(!$output->toBool() || !isset($output->data)) return $output;

            $data = $output->data;
            unset($output->data);

            foreach($data as $key => $attribute) {
                $coupon_srl = $attribute->coupon_srl;
                if(!$GLOBALS['XE_COUPON_LIST'][$coupon_srl]) {
                    $oCoupon = null;
                    $oCoupon = new couponItem();
                    $oCoupon->setAttribute($attribute);
                    $GLOBALS['XE_COUPON_LIST'][$coupon_srl] = $oCoupon;
                }

                $output->data[] = $GLOBALS['XE_COUPON_LIST'][$coupon_srl];
            }

            return $output;
        }
    }
?>
