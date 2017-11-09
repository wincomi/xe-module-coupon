function doGenerateCode() {
    exec_xml('coupon','procCouponAdminGenerateCode', new Array(), completeGenerateCode, ['error', 'message', 'code', 'code2', 'code3']);
}

function completeGenerateCode(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var code = ret_obj['code'];
    var code2 = ret_obj['code2'];
    var code3 = ret_obj['code3'];

    if(error == 0) {
        jQuery('#code').val(code);
        jQuery('#code2').val(code2);
        jQuery('#code3').val(code3);
    } else {
        alert(message);
        return;
    }
}

function completeInsertCoupon(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    var url = current_url.setQuery('act', 'dispCouponAdminList');
    location.href = url;
}

function completeInsertConfig(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    location.reload();
}

function completeDeleteChecked(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    location.reload();
}

function insertSelectedMember(id, member_srl) {
    jQuery('#'+id).val(member_srl);
}