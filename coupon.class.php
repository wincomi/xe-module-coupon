<?php
    /**
     * @class  쿠폰(coupon)
     * @author SMaker (dowon2308@paran.com)
     * @brief  쿠폰(coupon) 모듈의 high class
     **/

    class coupon extends ModuleObject {

        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            // action forward에 등록 
            $oModuleController = &getController('module');
            $oModuleController->insertActionForward('coupon', 'view', 'coupon');
            $oModuleController->insertTrigger('moduleHandler.init', 'coupon', 'controller', 'triggerAddMemberMenu', 'after');
        }

        /**
         * @brief 제거시 추가 작업이 필요할시 구현
         */
        function moduleUninstall() {
            // action forward 삭제
            $oModuleController = &getController('module');
            $oModuleController->deleteActionForward('coupon', 'view', 'coupon');
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');

            // coupon 테이블에 title 칼럼 추가 (2010/07/10)
            if(!$oDB->isColumnExists('coupon', 'title')) return true;

            // 회원 메뉴 트리거 추가 (2010/07/11)
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'coupon', 'controller', 'triggerAddMemberMenu', 'after')) return true;

            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            // coupon 테이블에 title 칼럼 추가 (2010/07/10)
            if(!$oDB->isColumnExists('coupon', 'title'))
                $oDB->addColumn('coupon', 'title', 'varchar', 250 , '', true);

            // 회원 메뉴 트리거 추가 (2010/07/11)
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'coupon', 'controller', 'triggerAddMemberMenu', 'after'))
                $oModuleController->insertTrigger('moduleHandler.init', 'coupon', 'controller', 'triggerAddMemberMenu', 'after');

            return $this->makeObject(0, 'success_updated');
        }

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
        }
        
        public function makeObject($code, $msg)
		{
		    return class_exists('BaseObject') ? new BaseObject($code, $msg) : new Object($code, $msg);
		}
    }
?>
