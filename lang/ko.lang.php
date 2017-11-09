<?php
    /**
     * @file ko.lang.php
     * @author SMaker (dowon2308@paran.com)
     * @brief  쿠폰(coupon) 모듈의 기본 언어팩
     **/
    $lang->coupon = '쿠폰';
    $lang->ticket = '응모권';
	$lang->point = '포인트';

    $lang->coupon_title = '쿠폰 제목';
    $lang->coupon_code = $lang->coupon_code2 = $lang->coupon_code3 = '쿠폰 코드';
    $lang->coupon_number = '쿠폰 번호';
    $lang->coupon_limit = '사용 제한 횟수';
    $lang->coupon_expire_date = '쿠폰 만료일';
    $lang->coupon_used_count = '사용 횟수';
    $lang->coupon_used_log = '사용 기록';
    $lang->expire_date = '만료일';
    $lang->save_point = '적립 포인트';
    $lang->unlimited = '무제한';
    $lang->own_member = '소유 회원';
    $lang->about_point = "숫자만 입력하여 포인트 증가, 0을 입력하여 '꽝', 마이너스(-)를 이용하여 감소시킬 수 있습니다.";
    $lang->about_coupon_limit = "회원이 쿠폰을 사용 할 수 있는 횟수를 지정할 수 있습니다. (단, 한 회원은 한번만 사용 할 수 있습니다.)";
    $lang->about_own_member = '쿠폰을 소유하게 될 회원을 입력해 주세요. 입력하지 않을 경우 모든 회원이 사용할 수 있습니다.';

    $lang->cmd_generate = '랜덤 생성';
    $lang->cmd_coupon_registration = '쿠폰 발급';

    $lang->cmd_coupon_setup = '기본 설정';
    $lang->cmd_coupon_list = '쿠폰 목록';

    $lang->cmd_use_coupon = '쿠폰 사용';
    $lang->cmd_coupon_use = '사용';

    $lang->cmd_my_coupon_box = '내 쿠폰함';

    $lang->msg_input_coupon_code = '쿠폰 코드를 입력해 주세요.';
    $lang->msg_exists_coupon_code = '이미 발급된 쿠폰 코드입니다. 다른 코드를 입력해 주세요.';
    $lang->msg_not_exists_coupon_code = '존재하지 않는 쿠폰 코드입니다.';
    $lang->msg_not_owned_coupon = '회원님께 발급된 쿠폰이 아닙니다.';
    $lang->msg_not_available_coupon = '사용할 수 없는 쿠폰입니다.';
    $lang->msg_not_exists_coupon_used_logs = '아직 쿠폰이 사용된 기록이 없습니다.';

    $lang->msg_coupon_is_blank = '꽝입니다. ';
    $lang->msg_coupon_encourage = '쿠폰을 사용하여 %s 포인트를 잃으셨습니다.';
    $lang->msg_coupon_congratulation = '축하합니다. 쿠폰을 사용하여 %s 포인트를 얻으셨습니다.';
    $lang->msg_coupon_congratulation_custom = '축합니다. 쿠폰을 사용하여 %s';
    $lang->msg_cannot_use_coupon = '만료일(%s)이 지나 사용하실 수 없습니다.';
    $lang->msg_cannot_use_limited_coupon = '최대 (%s)';
    $lang->msg_already_used_coupon = '이미 사용된 쿠폰입니다.';
    $lang->msg_not_exists_coupons = '등록된 쿠폰이 없습니다.';

    $lang->success_coupon_inserted = '쿠폰이 발급되었습니다.';

    $lang->sample_code = '샘플 코드';
    $lang->about_coupon_sample_code = '원하시는 곳에 위 코드를 붙어넣으시면 쿠폰을 사용할 수 있는 링크가 생성됩니다.';

    $lang->description_use_coupon = '하이픈(-)을 포함한 쿠폰 코드를 입력해주세요.';

    $lang->msg_need_login = '로그인이 필요합니다.';

    $lang->specific_coupon_info = '[%s] 사용 기록';

    $lang->unit__time = '번';
    $lang->cmd_find_member = '회원 찾기';
    $lang->msg_no_result = '일치하는 검색 결과가 없습니다.';
    $lang->msg_no_keyword = '먼저 찾고자 하는 회원을 검색을 해주세요.';
?>