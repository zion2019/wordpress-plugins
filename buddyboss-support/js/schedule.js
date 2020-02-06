jQuery(document).ready(function($){

    /* 展开收起小组排行榜成员列表S */
    $(".list-board").on('click','.list-icon',function(event){
        $(this).toggleClass('open');
        $(this).siblings('.members-list').toggle();
        $(this).parent().siblings().children('.list-icon').removeClass('open');
        $(this).parent().siblings().children('.members-list').hide();
        event.stopPropagation()
    })
    $(".list-board").on('click','.list-table-body',function(){
        var list = $(this).children('.list-icon');
        $(list).toggleClass('open');
        $(list).siblings('.members-list').toggle();
        $(this).siblings().children('.list-icon').removeClass('open');
        $(this).siblings().children('.members-list').hide();
    })
    $(".list-table-body").on('click','.members-list',function(event){
        event.stopPropagation();
    })
    /* 展开收起小组排行榜成员列表E */

    /* 查看案例发送完成请求S */
    $("body").on('click','.list-case a',function(){
        var caseId = $(this).parents('.content').siblings('span').text();
        var postData = {
            action: 'case_view',
            case_id: caseId
        }
        var url = document.domain + '/wp-admin/admin-ajax.php';
        $.post(url,{
            action: 'case_view',
            data: postData
        })
    })
    /* 查看案例发送完成请求E */

    /* 显示隐藏积分规则弹窗S */
    $("body").on('click','#rules',function(){
        $(".integral-wrap").toggle();
    })
    $("body").on('click','.integral-mask',function(){
        $('.integral-wrap').hide();
    })
    /* 显示隐藏积分规则弹窗E */

    /* 显示隐藏个人积分记录S */
    $("body").on('click','.close-btn',function(){
        $('.integral-record').hide();
    })
    $("body").on('click','.record-mask',function(){
        $('.integral-record').hide();
    })
    $("body").on('click','.total-score p',function(){
        $('.integral-record').show();
    })
    /* 显示隐藏个人积分记录E */
})