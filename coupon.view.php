<?php
	/**
	 * @class  couponView
	 * @author zero (zero@nzeo.com)
	 * @brief  coupon module의 view class
	 **/

	class couponView extends coupon {

		/**
		 * @brief 초기화
		 **/
		function init() {
			$oModel = getModel('coupon');
			$this->config = $oModel->getModuleConfig();
			$this->skin = $this->config->skin;

			$this->setTemplatePath($this->module_path.'/skins/'.$this->skin.'/');
		}

		/**
		 * @brief 쿠폰 사용
		 **/
		function coupon() {
			// 권한 체크
			if(!Context::get('is_logged')) return $this->dispCouponMessage('msg_need_login');

			$code = htmlspecialchars(Context::get('code'));
			if($code && strlen($code) != 17) return $this->makeObject(-1, 'msg_invalid_request');

			Context::setBrowserTitle(Context::getLang('cmd_use_coupon'));

			// 레이아웃을 팝업으로 지정
			$this->setLayoutFile('popup_layout');

			// 템플릿 파일 지정
			$this->setTemplateFile('coupon');
		}

		function dispCouponBox() {
			// 권한 체크
			if(!Context::get('is_logged')) return $this->dispCouponMessage('msg_need_login');

			$oModel = getModel('coupon');

			$logged_info = Context::get('logged_info');

			$args = new stdClass();
			$args->member_srl = $logged_info->member_srl;
			$args->sort_index = 'regdate';
			$args->order_type = 'desc';
			$args->page = Context::get('page');
			$args->page_count = 10;
			$args->list_count = 20;
			$output = $oModel->getMemberCouponList($args);

			Context::set('coupon_list', $output->data);

			// 템플릿 파일 지정
			$this->setTemplateFile('coupon_box');
		}

		function dispCouponMessage($message) {
			// 에러 지정
			$this->stop($message);

			$oMessageView = &getView('message');
			$oMessageView->dispMessage();

			// message 모듈의 에러를 컴파일 하여 출력
			$oTemplate = &TemplateHandler::getInstance();
			$popup_content = $oTemplate->compile($oMessageView->getTemplatePath(), $oMessageView->getTemplateFile());
			Context::set('popup_content', $popup_content);

			// 템플리 경로 지정
			$this->setTemplatePath($this->module_path.'tpl');

			// 템플릿 파일 지정
			$this->setTemplateFile('popup_layout');
		}
	}
?>
