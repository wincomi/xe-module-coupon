<?php
	/**
	 * @class  couponAdminView
	 * @author SMaker (dowon2308@paran.com)
	 * @brief coupon module의 admin view class
	 **/

	class couponAdminView extends coupon {

		var $config = null;

		/**
		 * @brief 초기화
		 **/
		function init() {
			// 설정 정보를 받아옴 (module model 객체를 이용)
			$oModel = &getModel('coupon');
			$this->config = $oModel->getModuleConfig();
			Context::set('config', $this->config);

			//$this->setTemplatePath($this->module_path.'/tpl/');
			$template_path = sprintf("%stpl/",$this->module_path);
			$this->setTemplatePath($template_path);
		}
		
		/**
		 * @brief 기본 설정
		 */
		function dispCouponAdminSetup() {
			// 스킨 목록을 구함
			$oModuleModel = &getModel('module');
			$skin_list = $oModuleModel->getSkins($this->module_path);

			Context::set('skin_list', $skin_list);

			$this->setTemplateFile('setup');
		}

		/**
		 * @brief 쿠폰 목록
		 **/
		function dispCouponAdminList() {
			$args = new stdClass();
			$args->page = Context::get('page');
			$args->list_count = 20;

			// 쿠폰 목록을 구해옴
			$oModel = &getModel('coupon');
			$output = $oModel->getCouponList($args);

			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page', $output->page);
			Context::set('coupon_list', $output->data);
			Context::set('page_navigation', $output->page_navigation);

			$this->setTemplateFile('coupon_list');
		}

		function dispCouponAdminInsertCoupon() {
			// content는 다른 모듈에서 call by reference로 받아오기에 미리 변수 선언만 해 놓음
			$addition_content = '';

			// 다른 모듈과의 연동을 위한 트리거 호출 
			$output = ModuleHandler::triggerCall('coupon.dispCouponInsert', 'before', $addition_content);
			$output = ModuleHandler::triggerCall('coupon.dispCouponInsert', 'after', $addition_content);

			Context::set('addition_content', $addition_content);

			$coupon_srl = Context::get('coupon_srl');

			$oCouponModel = &getModel('coupon');
			$oCoupon = $oCouponModel->getCoupon($coupon_srl);

			if($oCoupon->isExists()) {
				list($code, $code2, $code3) = explode('-', $oCoupon->getCode());
				Context::set('code', $code);
				Context::set('code2', $code2);
				Context::set('code3', $code3);
			} else {
			}

			Context::set('oCoupon', $oCoupon);

			$this->setTemplateFile('insert_coupon');
		}

		/**
		 * @brief 스킨 설정
		 **/
		function dispCouponAdminSkinInfo() {
			$oModuleModel = &getModel('module');
			$skin_info = $oModuleModel->loadSkinInfo($this->module_path, $this->config->skin);
			$skin_vars = unserialize($this->config->skin_vars);

			// skin_info에 extra_vars 값을 지정
			if(count($skin_info->extra_vars)) {
				foreach($skin_info->extra_vars as $key => $val) {
					$name = $val->name;
					$type = $val->type;
					$value = $skin_vars->{$name};
					if($type=="checkbox"&&!$value) $value = array();
					$skin_info->extra_vars[$key]->value= $value;
				}
			}

			Context::set('skin_info', $skin_info);
			Context::set('skin_vars', $skin_vars);
			Context::set('skin_content', $skin_content);

			$this->setTemplateFile("skin_info");
		}

		function dispCouponAdminCouponLogList() {
			$coupon_srl = Context::get('coupon_srl');
			if(!$coupon_srl) return $this->makeObject(-1, 'msg_invalid_request');

			$oModel = &getModel('coupon');
			$oCoupon = $oModel->getCoupon($coupon_srl);
			if(!$oCoupon->isExists()) return $this->makeObject(-1, 'msg_invalid_request');
			Context::set('oCoupon' ,$oCoupon);

			$output = $oCoupon->getUsedLogs();
			Context::set('page', $output->page);
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('log_list', $output->data);
			Context::set('page_navigation', $output->page_navigation);

			$this->setTemplateFile('coupon_log_list');
		}

		function dispCouponAdminFindMember() {
			$oMemberAdminModel = &getAdminModel('member');

			$search_target = Context::get('search_target');
			$search_keyword = Context::get('search_keyword');
			if($search_target && $search_keyword) {
				$output = $oMemberAdminModel->getMemberList();

				Context::set('total_count', $output->total_count);
				Context::set('total_page', $output->total_page);
				Context::set('member_list', $output->data);
				Context::set('page_navigation', $output->page_navigation);
			}

			$this->setTemplateFile('find_member');

			// 레이아웃을 팝업으로 지정
			$this->setLayoutFile('popup_layout');
		}
		
		/**
		 * @brief 쿠폰 샘플 코드
		 **/
		function dispCouponAdminSampleCode() {
			// 샘플 코드			
			$sample_code = sprintf(Context::getLang('coupon_sample_code'), getFullUrl('', 'module', 'coupon'), Context::getLang('cmd_use_coupon'));
			Context::set('sample_code', $sample_code);
			
			$this->setTemplateFile("coupon_sample");
		}
	}
?>
