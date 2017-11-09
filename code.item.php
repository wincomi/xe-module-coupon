<?php
    /**
     * @class  codeItem
     * @author SMaker (dowon2308@paran.com)
     * @brief  code 객체
     **/

    class codeItem extends Object {

        var $code = ''; ///<< 쿠폰 코드
        var $length = 17; ///< 쿠폰 코드 길이
        var $split_length = 5; ///< 쿠폰 코드를 나눌 길이
        var $alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'); ///< 알파벳 목록

        function codeItem($length, $split_length) {
            $this->length = $length;
            $this->split_length = $split_length;
        }

        function setCode($code) {
            $this->code = $code;
        }

        function setLength($length) {
            $this->length = $length;
        }

        function setSplitLength($length) {
            $this->split_length = $length;
        }

        function getLength() {
            return $this->length;
        }

        function getSplitLength() {
            return $this->split_length;
        }

        function getCode() {
            return $this->code;
        }

        function generate() {
            /**
             * @TODO: 자유로운 코드 생성을 위한 개선 필요
             */
            $length = $this->getLength();
            $split_length = $this->getSplitLength() + 1;
            $alpha = $this->alpha;

            for($i=0; $i<$length; $i++) {
                if(($i+1) % $split_length === 0) {
                    $code[] = '-';
                } else {
                    $type = rand(1,6);
                    switch($type % 3) {
                        case 0:
                            $code[] = rand(0, 9);
                            break;
                        default:
                            $code[] = $alpha[rand(0, 25)];
                            break;
                    }
                }
            }

            $this->setCode(join('',$code));
        }
    }
?>
