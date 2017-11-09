<?php
    /**
     * @class  couponItem
     * @author SMaker (dowon2308@paran.com)
     * @brief  coupon 객체
     **/

    class couponItem extends Object {

        var $coupon_srl = 0;

        function couponItem($coupon_srl = 0) {
            $this->coupon_srl = $coupon_srl;

            $this->_loadFromDB();
        }

        function setCoupon($coupon_srl) {
            $this->coupon_srl = $coupon_srl;
            $this->_loadFromDB();
        }

        function setCode($code) {
            $this->code = $code;
            $this->_loadFromDB();
        }

        function _loadFromDB() {
            if(!$this->coupon_srl && !$this->code) return;

            if($this->coupon_srl) $args->coupon_srl = $this->coupon_srl;
            $args->code = $this->code;
            $output = executeQuery('coupon.getCoupon', $args);

            $this->setAttribute($output->data);
        }

        function setAttribute($attribute) {
            if(!$attribute->coupon_srl) {
                $this->coupon_srl = null;
                return;
            }
            $this->coupon_srl = $attribute->coupon_srl;
            $this->code = $attribute->code;
            $this->adds($attribute);

            $GLOBALS['XE_COUPON_LIST'][$this->coupon_srl] = $this;
            $GLOBALS['XE_COUPON_LIST'][$this->code] = $this;
        }

        /**
         * @brief 쿠폰이 존재하는 지 확인하는 method
         */
        function isExists() {
            return $this->coupon_srl ? true : false;
        }

        /**
         * @brief 쿠폰을 사용한 적이 있는 지 확인하는 method
         **/
        function isUsed() {
            $logged_info = Context::get('logged_info');
            if(!$logged_info) return;

            //쿠폰 사용 기록이 있는지 확인
            $args->coupon_srl = $this->coupon_srl;
            $args->member_srl = $logged_info->member_srl;
            $output = executeQuery('coupon.getCouponUsedLogCount', $args);
            if($output->data->count) return true;

            // 사용 제한이 있다면
            if($this->get('limit') > 0) {
                $args->coupon_srl = $this->coupon_srl;
                unset($args->member_srl);
                $output = executeQuery('coupon.getCouponUsedLogCount', $args);
                if($output->data->count>=$this->get('limit')) return ;
            }

            return false;
        }

        /**
         * @brief 쿠폰을 사용할 수 있는지 확인하는 method
         */
        function isUseable() {
            // 존재하지 않는 쿠폰이면 return false
            if(!$this->isExists()) return false;

            // 사용 권한이 없는 쿠폰이면 return false
            if(!$this->isGranted()) return false;

            // 쿠폰을 사용한 적이 있다면 return false
            if($this->isUsed()) return false;

            // 만료일이 지났으면 return false
            if($this->isExpired()) return false;

            return true;
        }

        /**
         * @brief 쿠폰 소유자인지 확인하는 method
         */
        function isOwner() {
            // 로그인 정보를 구함
            $logged_info = Context::get('logged_info');

            // 로그인 정보가 없으면 return false
            if(!$logged_info) return false;

            // 소유자가 없으면 return true
            if(!$this->get('member_srl')) return true;

            // 소유자이면 return false
            if($this->get('member_srl') == $logged_info->member_srl) return true;

            // 그 외에는 return false
            return false;
        }

        /**
         * @brief isOwner()의 alias
         */
        function isGranted() {
            return $this->isOwner();
        }

        /**
         * @brief 쿠폰이 만료되었는지 확인하는 method
         */
        function isExpired() {
            return $this->get('expire_date') && date('Ymd') > $this->get('expire_date') ? true : false;
        }

        /**
         * @brief 쿠폰 코드를 구함
         */
        function getCode() {
            return $this->get('code');
        }

        /**
         * @brief 쿠폰의 적립 포인트를 구함
         */
        function getPoint() {
            return $this->get('point');
        }

        /**
         * @brief 쿠폰 사용 제한을 구함
         */
        function getLimit() {
            return $this->get('limit');
        }

        /**
         * @brief 만료일을 특정한 format으로 출력
         */
        function getExpireDate($format = 'Y-m-d') {
            return zdate($this->get('expire_date'), $format);
        }

        /**
         * @brief 등록일을 특정한 format으로 출력
         */
        function getRegdate($format = 'Y-m-d') {
            return zdate($this->get('regdate_date'), $format);
        }

        /**
         * @brief 쿠폰을 사용한 횟수를 구함
         */
        function getUsedCount() {
            $args->coupon_srl = $this->coupon_srl;
            $output = executeQuery('coupon.getCouponUsedLogCount', $args);
            return $output->data->count;
        }

        /**
         * @brief 사용 기록을 구함
         */
        function getUsedLogs($search_keyword = null, $search_target = null) {
            $args->coupon_srl = $this->coupon_srl;
            $args->order_type = 'desc';
            $output = executeQueryArray('coupon.getCouponUsedLogList', $args);
            return $output;
        }

        /**
         * @brief 쿠폰 제목을 구함
         */
        function getTitle() {
            return htmlspecialchars($this->get('title'));
        }
    }
?>
