/*video.js*/
var video = {
    currCateId: 0,
    totalVideo: 0,
    totalPage: 0,
    strEx: '',
    totalNew: 0,
    currPage: 0,
    listVideoIds: [],
    baseUrl: '',
    listVideoEleId: '',
    loadingEleId: '',
    __gotoPage: function(page, cateId, strEx, totalNew) {
        var data = null;
        this.currPage = page;
        if (typeof cateId != 'undefined') {
            this.currCateId = cateId;
        }
        data = {
            cateId: this.currCateId,
            currPage: this.currPage,
            totalNew: this.totalNew,
            strEx: this.strEx
        }
        var keyCache = 'video-worldcup' + this.currCateId + '-' + this.currPage;
        var cachedContent = null;
        /* Get cache by jcache */
        try {
            cachedContent = $.jCache.getItem(keyCache);
        }
        catch (ex) {
            cachedContent = null;
        }
        /* If miss cache */
        if (cachedContent == null || typeof cachedContent == 'undefined')
        {
            $.ajax({
                type: 'get',
                url: this.baseUrl + '/video/ajaxlistvideo',
                data: data,
                dataType: 'json',
                async: false,
                beforeSend: function() {
                    //$('#' + video.listVideoEleId).html($('#' + video.loadingEleId).html());
                },
                success: function(response)
                {
                    cachedContent = response;
                    $.jCache.setItem(keyCache, response);
                }
            });
        }

        if (!cachedContent.error) {
            var listVideoID = $('#' + this.listVideoEleId);
            listVideoID.fadeOut('fast', function() {
                listVideoID.html(cachedContent.html).fadeIn('fast', function() {
                    if ('undefined' != typeof CmtWidget) {
                        CmtWidget.mix();
                    }
                });
            });
            if (cachedContent.ext != null) {
                this.totalVideo = cachedContent.ext.totalVideo;
                this.totalPage = cachedContent.ext.totalPage;
                this.listVideoIds = cachedContent.ext.listVideoIds;
                this.currPage = cachedContent.ext.currPage;
            }
        }
    },
    init: function(options) {
        this.currCateId = options.currCateId;
        this.totalVideo = options.totalVideo;
        this.totalPage = options.totalPage;
        this.strEx = options.strEx;
        this.totalNew = options.totalNew;
        this.currPage = options.currPage;
        this.baseUrl = options.baseUrl;
        this.listVideoEleId = options.listVideoEleId;
        this.loadingEleId = options.loadingEleId;
    },
    numPage: function(page) {
        this.__gotoPage(page);
    },
    prevPage: function() {
        this.__gotoPage(this.currPage - 1);
    },
    nextPage: function() {
        this.__gotoPage(this.currPage + 1);
    }
};
/*video_feedback*/
/*var worldCupVideoFeedback = {
 timeout: 0,
 loadCaptcha: function() {
 $.ajax({
 type: 'GET',
 url: base_url + '/captcha/show',
 data: ({}),
 dataType: 'json',
 success: function(response) {
 $('#feedback_box #feedback_lightbox').find('#showCaptcha').html(response.html);
 return false;
 }
 });
 },
 init: function() {
 //show Lightbox
 //Object Feedback_Lightbox khoi tao trong file "video_feedback_lightbox.js"
 Feedback_Lightbox.show('feedback_popup', 'Góp ý để chúng tôi hoàn thiện hơn', 473);
 //focus code captcha
 $('#feedback_box #feedback_lightbox #txtCode').focus();
 worldCupVideoFeedback.loadCaptcha();
 
 },
 feedback: function(videoid) {
 this.videoid = videoid;
 var type = [];
 
 $('[name=chkFeedBack]:checked').each(function() {
 type.push($(this).val());
 });
 var content = $('#feedback_box #feedback_lightbox').find('#content').val();
 if (type.length == 0)
 {
 alert('Vui lòng lựa chọn ý kiến Phản hồi.');
 return false;
 }
 if (content.length > 1000)
 {
 alert('Vui lòng nhập ý kiến Phản hồi dưới 1000 ký tự.');
 return false;
 }
 
 var captchaID = $.trim($('#feedback_box #feedback_lightbox').find('#captchaID').val());
 var txtCode = $.trim($('#feedback_box #feedback_lightbox').find('#txtCode').val());
 if (txtCode == '')
 {
 alert('Vui lòng nhập mã xác nhận');
 $('#feedback_box #feedback_lightbox #txtCode').focus();
 return false;
 }
 $.ajax({
 type: 'GET',
 url: base_url + '/feedback/video',
 data: ({
 videoid: this.videoid,
 type: type,
 content: content,
 captchaID: captchaID,
 txtCode: txtCode
 }),
 dataType: 'json',
 beforeSend: function() {
 },
 success: function(response) {
 if (response.isCaptchaError == 0)
 {
 if (response.isSuccess == 1)
 {
 //Cảm ơn bạn đã Góp ý
 Feedback_Lightbox.show('feedback_msg_finish', 'Thông báo', 400);
 worldCupVideoFeedback.timeout = setTimeout(function() {
 Feedback_Lightbox.hide();
 clearTimeout(worldCupVideoFeedback.timeout);
 }, 5000);
 return false;
 }
 else
 {
 //Góp ý thất bại.
 Feedback_Lightbox.show('feedback_error_popup', 'Thông báo', 400);
 worldCupVideoFeedback.timeout = setTimeout(function() {
 Feedback_Lightbox.hide();
 clearTimeout(worldCupVideoFeedback.timeout);
 }, 5000);
 return false;
 }
 }
 else
 {
 alert('Mã xác nhận không chính xác. Vui lòng nhập lại.');
 $('#feedback_box #feedback_lightbox #txtCode').val('');
 $('#feedback_box #feedback_lightbox #txtCode').focus();
 worldCupVideoFeedback.loadCaptcha();
 }
 }
 });
 }
 };*/
/*video_feedback_lightbox*/
/*Light box Feedback*/
/*var Feedback_Lightbox = {
 domain_image: domain_image,
 selector: null,
 isInit: false,
 delay: null,
 type: "html", // default type
 init: function() {
 if ($('#feedback_box').length > 0) {
 $('#feedback_box').appendTo('body');
 } else {
 Feedback_Lightbox.generate();
 }
 
 if ($("#feedback_box").css("position") == "absolute") {
 $(window).scroll(function() {
 $("#feedback_box").css("top", $(document).scrollTop() + "px");
 });
 }
 this.isInit = true;
 },
 setConfig: function(data) {
 if (typeof (data.selector) != 'undefined') {
 this.selector = data.selector;
 }
 
 if (data.type) {
 this.type = data.type;
 }
 },
 generate: function() {
 $('body').append('<div class="feedback_bigbox" id="feedback_box">'
 + '<iframe frameborder="0" class="windowmask"></iframe><div class="windowmask"></div>'
 + '<div class="contentbox lightbox contLightbox01" id="feedback_lightbox">'
 + '<div class="lbcont" id="lbcontent">'
 + '<div class="lightbox-border">'
 + '<table width="100%" border="0" cellspacing="0" cellpadding="0">'
 + '<tr><td class="tl"></td><td class="b"></td><td class="tr"></td></tr>'
 + '<tr><td class="b"></td><td class="bbody"><div class="feedback-lightbox-boxhead"><div class="tlc"><div class="trc"><div class="boxhead-content"><div class="txt_title_right_logo"><img src="' + Feedback_Lightbox.domain_image + '/graphics/img_logo_vne.gif" alt=""/></div><div class="boxhead-content-main title_left"></div><div class="clear">&nbsp;</div></div></div></div></div><div class="lightbox-boxbody"></div></td><td class="b"></td></tr>'
 + '<tr><td class="bl"></td><td class="b"></td><td class="br"></td></tr>'
 + '</table>'
 + '</div>'
 + '</div>'
 + '</div>'
 );
 },
 setWidth: function(lbgWidth) {
 $('#feedback_lightbox').css('width', lbgWidth ? lbgWidth : 'auto');
 },
 setHeight: function(lbgHeight) {
 $('.lightbox-boxbody').css('height', lbgHeight ? lbgHeight : 'auto');
 },
 show: function(boxId, title, msgWidth, msgHeight) {
 $('.boxhead-content-main').html(title);
 
 if (this.type == 'iframe') {
 var url = boxId;
 if (!url) {
 return false;
 } else {
 var width = (msgWidth) ? parseInt(msgWidth) - 20 : 430;
 var height = (msgHeight) ? parseInt(msgHeight) : 300;
 var html = "<iframe width='" + width + "' height='" + height + "' src='" + url + "' style='border: none' frameborder='0'></iframe>";
 $('.lightbox-boxbody').html(html);
 }
 } else {
 
 if (msgHeight) {
 this.setHeight(parseInt(msgHeight));
 }
 if (this.type == 'html') {
 $('.lightbox-boxbody').html($("#" + boxId).html());
 } else if (this.type == 'text') {
 $('.lightbox-boxbody').text($("#" + boxId).html());
 }// define more type here
 }
 
 
 Feedback_Lightbox.setWidth(parseInt(msgWidth) + 18 || 300);
 var elem = $('#feedback_lightbox');
 
 elem.parent(this).fadeIn(200);
 $(window).bind("resize", function() {
 Feedback_Lightbox.setPos(elem);
 });
 
 Feedback_Lightbox.setPos(elem);
 
 if ($('.btngreen:first', elem).focus()[0] == undefined) {
 if ($('span.btngray:first > input', elem).focus()[0] == undefined) {
 $('input.cancel:first', elem).focus();
 }
 }
 },
 hide: function() {
 $('#feedback_lightbox').parent(this).fadeOut(200);
 },
 setPos: function(elem) {
 
 var top = (document.documentElement.clientHeight - elem.height()) / 2;
 var left = (document.documentElement.clientWidth - elem.width()) / 2;
 if (top < 0)
 top = 0;
 if (left < 0)
 left = 0;
 elem.css({
 'left': left + 'px',
 'top': top + 'px'
 });
 },
 showemsg: function(title, content, namebtn) {
 var errbox =
 '<div class="reg">'
 + '<p>' + content + '</p>'
 + '<table width="28%" border="0" cellspacing="0" cellpadding="0" style="margin:0px auto;" class="list-button">'
 + '<tr>'
 + '<td><div class="sidebutton"><div class="btn-left-02"><input name="huy" type="button" value="' + namebtn + '" class="nbutton" onclick="Feedback_Lightbox.hide()"  /></div></div></td>'
 + '</tr>'
 + '</table>'
 + '</div>';
 
 $('.boxhead-content-main').html(title);
 $('.lightbox-boxbody').html(errbox);
 
 Rating.setWidth(300 + 18);
 var elem = $('#feedback_lightbox');
 elem.parent(this).fadeIn(200);
 Feedback_Lightbox.setPos(elem);
 }
 };
 
 $(document).ready(function() {
 if (!Feedback_Lightbox.isInit) {
 Feedback_Lightbox.init();
 }
 $("[feedback_lightbox]").click(function() {
 if (!$(this).attr("onclick")) {
 Feedback_Lightbox.setConfig({
 selector: $(this),
 type: $(this).attr("rel") || "html"
 });
 var params = $.map($(this).attr("feedback_lightbox").split(","), $.trim);
 Feedback_Lightbox.show(params[0], params[1], params[2]);
 }
 });
 });*/