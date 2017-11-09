/* 모듈 선택기에서 선택된 모듈의 입력 */
function insertMember(id, member_srl) {
    if(!window.opener) window.close();

    if(typeof(opener.insertSelectedMember)=='undefined') return;

    opener.insertSelectedMember(id, member_srl);
    window.close();
}