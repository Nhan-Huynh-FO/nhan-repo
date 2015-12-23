if (typeof El != "function") {
    var Ed = function (a) {
        a.preventDefault ? a.preventDefault() : a.returnValue = false;
        return false;
    };
    var Ev = function () {
        var d = function (e) {
            /* Catch exception such as "SyntaxError: missing ) in parenthetical" */
            try{
                var data = e.data && eval('(' + e.data + ')'),
                    func = data.func || '';
                func && window['messeger_' + func](e);
            }catch(e){}
        }
        return d;
    };
    var El = window.addEventListener ? function (a, b, c) {
        var d = function (a) {
            var b = c.call(this, a);
            false === b && Ed(a);
            return b;
        }
        a.addEventListener(b, d, false);
        return d;
    } : window.attachEvent ? function (a, b, c) {
        var d = function () {
            var b = window.event,
                d = c.call(a, b);
            false === d && Ed(b);
            return d;
        };
        a.attachEvent("on" + b, d);
        return d;
    } : void 0;
    El(window, "message", Ev())
}
(function (window, document) {
    'use strict';
    window.VNE = window.VNE || {};
    // Interaction server
    var server_url = interactions_url || 'http://interactions.vnexpress.net';
    var _domain = window.location.host.split('.').slice(-2).join('.');
    var DELAY_TIME = 5000;

    window.VNE.Comment = {};

    // Options
    var options = {offset:0, limit:24, frommobile:0, sort: 'like'}, root, totalEl, listCommentEl, currentPage, isShowMore = 0, useReply = 0, app_mobile, ratingEl, _isListener = 0;
    // Private variable
    var _member = {email: '', fullname:'',share:1,type:''}, _debug = false, _showTip = false;
    var func = [];

    /*======================================Open ID========================================================*/
    var m_popup = null;
    var showOpenId = function(obj){
        var type = obj.attr('rel');
        var options = {'facebook':{'url':server_url + '/openid/facebook?callback=vne_comment&cb_url=' + window.location.host,'width':800,'height':570}
            ,'google':{'url': server_url + '/openid/google?callback=vne_comment&cb_url=' + window.location.host,'width':510,'height':510}
        };
        m_popup = window.open(options[type]['url'], "windowname1", 'width=' + options[type]['width'] + ', height=' + options[type]['width']);
    }
    window.messeger_vne_comment = function (e)
    {
        if(e.origin=='http://'+location.host || e.origin == 'http://nhanva.comment.vnexpressdev.net' || e.origin == 'http://interactions.vnexpress.net'){
            vne_comment(e);
        }
        return true;
    }
    window.vne_comment = function (e)
    {
        var data = e.data && eval('(' + e.data + ')');
        m_popup.close();
        if(data.type == 'google'){
            cookies('_g_oid', data.userid, {path: '/', expires: 15, domain: '.' + _domain});
            options.usertype = 6;
        }else if(data.type == 'facebook'){
            cookies('_f_oid', data.userid, {path: '/', expires: 15, domain: '.' + _domain});
            options.usertype = 5;
        }
        if(data.email === ''){
            // User will be fill info
            var loginForm = $('#login-vne1');
            loginForm.find('.login-type').hide();
            loginForm.find('.login-right .login-type').show().html('Hoàn tất thông tin của bạn');
            loginForm.find('a.login-social').hide();
            loginForm.find('.lg-' + data.type).show();
            loginForm.find('#txt_fullname').val(data.fullname);
            _member.fullname = data.fullname;
            _member.type = data.type;
        }else{
            _member.email = data.email;
            _member.fullname = data.fullname;
            _member.type = data.type;
            hideAlertBox();
            // Process post comment
            var info = getContentComment();
            addComment(info.content, info.parent_id, info.reply_to);

        }
        cookies('vneoe', data.email, {path: '/', expires: 15, domain: '.' + _domain});
        cookies('vneon', data.fullname, {path: '/', expires: 15, domain: '.' + _domain});
        cookies('vneotype', data.type, {path: '/', expires: 15, domain: '.' + _domain});
        var share = cookies('share_' + data.type);
        if(share === null){
            // Not set cookie
            cookies('share_' + data.type, 1, {path: '/', expires: 15, domain: '.' + _domain});
            _member.share = 1;
        }else{
            _member.share = share;
        }
        setUiOpenId();
        return true;
    }

    /*
     * Set UI for openid
     **/
    var setUiOpenId =function(){
        root.find('#open_id_name').html(_member.fullname);
        if(!_member.fullname){
            var info = root.find('.like_google');
            info.hide();
            if(typeof(info.click) == 'function'){
                info.unbind();
            }
        }else{
            var info = root.find('.like_google');
            info.show();
            if(typeof(info.click) == 'function'){
                info.unbind();
            }
            info.bind('click', function(e){
                var self = $(this);
                if(!self.hasClass('active')){
                    self.addClass('active');
                    self.children('.block_action_google').fadeIn(200);
                }
                else{
                    self.removeClass('active');
                    self.children('.block_action_google').fadeOut(200);
                }
            });

        }
        var mapping = {'1': {value: '_active', text:'Tắt chia sẻ'}, '0': {value: '_active inactive', 'text': 'Chia sẻ'}};
        root.find('#open_id_status').removeClass().addClass(_member.type +  mapping[_member.share]['value']);
        root.find('#open_id_share').html(mapping[_member.share]['text']);
    }
    /**
     * Set value for openid
     */
    var setOpenID = function(email, fullname, type){
        _member.email = email;
        _member.fullname = fullname;
        _member.type = type;
        if(type == 'google'){
            options.usertype = 6;
        }else if (type == 'facebook'){
            options.usertype = 5;
        }
    }
    /*======================================End Open ID========================================================*/

    // Set param
    var setOption = function (opt) {
        opt = opt || {};
        options = $.extend(options, opt);
        options.cookie_aid = cookies('fosp_aid');
        options.usertype = 4;
        root = $('#box_comment');
        listCommentEl = root.find('#list_comment');
        var loadingEl = root.find('div.loading');
        if(!loadingEl.find('img.img_comment_loading').length){
            loadingEl.css({"text-align": "center", "padding-top": "15px", "display": "block", "width": "100%", "float": "left", "vertical-align": "middle"}).html('<img class="img_comment_loading" src="http://st.f3.vnecdn.net/responsive/c/v1/images/graphics/loading1.gif" style="width:25px;" title="Đang tải dữ liệu" alt="Đang tải dữ liệu">');
        }
        loadingEl.hide();
        currentPage = 0;
        app_mobile = opt.app_mobile_device || 0;
        if(typeof(defaults.platform) == 'undefined'){
            defaults.platform = 4;
        }
        /*
        if (defaults.platform == 1){
            // Limit 12 comment for mobile
            options.limit = 12;
        }
        */
        if('undefined' !== typeof(options.objecttype)){
            // Check when using user rating
            if(options.objecttype == 55 || options.objecttype == 70){
                options.useRating = 1;
                buildUserRating();
            }
        }
        if(typeof(options.title) == 'undefined'){
            options.title = $(document).find("title").text();
        }

        // Check already add listener
        if(_isListener){
            return this;
        }

        // Get cookie
        setOpenID(cookies('vneoe'), cookies('vneon'), cookies('vneotype'));
        // Update UI
        setUiOpenId();

        if ('undefined' == typeof (options.app_mobile_device) || !options.app_mobile_device) {
            options.app_mobile_device = 0;
        }

        // Attach submit event
        root.find('input[type=button]').bind('click', function(){
            useReply = $(this).attr('id') == 'comment_reply_button' ? 1 : 0;

            validateComment($(this), options.app_mobile_device);
        });

        // Add listener
        initEvent(options.app_mobile_device);

        // Filter comment event
        if(defaults.platform == 1){
            // Mobile

            $('.filter_coment_mobile select').bind('change', function(e){
                var that = $(this);
                // Set option and get comment
                sortedBy(that.val());
                // Keep box reply comment
                root.find('#comment_reply_wrapper').hide().appendTo('#box_comment');
                getComments();
            })
        }else{
            // Web
            var filterEl = root.find('.filter_coment a');
            filterEl.bind('click', function(e){
                e.preventDefault();
                var that = $(this);
                if(that.hasClass('active')){
                    return;
                }
                // Set option and get comment
                sortedBy(that.attr('rel'));
                // Keep box reply comment
                root.find('#comment_reply_wrapper').hide().appendTo('#box_comment');

                getComments();
                // Add active class for current element
                filterEl.removeClass('active');
                that.addClass('active');
            });

            // Check has commentid from url
            var commentid = getParameter('commentid');
            if(typeof (commentid) != 'undefined' && 0 < commentid){
                options.commentid = commentid;
            }
        }
        if(app_mobile !== 1){
            if(typeof(myvne_users) == 'object' && typeof(myvne_users.profile) != 'undefined'){
                setTimeout(setUiMyVNE, 2000);
                myvne_users.setCallback("login",function(res) {
					_debug && log('[myvne_users.setCallback] Login');
					setUiMyVNE(res);
				});
                myvne_users.setCallback("logout",function(res) {
					_debug && log('[myvne_users.setCallback] Logout');
					setUiMyVNE(res);
				});
				
				// Only show tooltip on PC
				if(defaults.platform == 4){
					_showTip = true && (options.objecttype != 5);
					root.find('#list_comment .user_status > a.avata_coment, #list_comment .user_status span.left > a.nickname').live({
						mouseenter: function () {
							var self = $(this); self.addClass('hover');
							var userStatusEl = self.closest('.user_status');
							userStatusEl.addClass('avatahover');
							var avatarCmt = userStatusEl.find('.info_avata_cmt');
							_debug && log('[avata_coment] mouseenter on ' + self.attr('href'));
							if(!avatarCmt.length){
								return;
							}
							// Check popup is hover
							if(true || self.hasClass('hover')){
								// Show popup
								avatarCmt.fadeIn();
							}
						},
						mouseleave: function () {
							var self = $(this);
							// Hide popup after 200 ms
							setTimeout(function() {
								var userStatusEl = self.closest('.user_status');
								userStatusEl.removeClass('avatahover');
								var avatarCmt = userStatusEl.find('.info_avata_cmt');
								if(!avatarCmt.length){
									return;
								}
								// Is hover on avatar or popup
								var closePopup = (!self.hasClass('hover') || !avatarCmt.hasClass('hover'));
								if(closePopup){
									avatarCmt.stop(true,true).fadeOut();
									avatarCmt.removeClass('hover');
								}
								self.removeClass('hover');
								_debug && log('[avata_coment] mouseenter mouseleave ' + self.attr('href') + '. Popup is hover: ' + !closePopup);
								
								},200
							);
						}
					});
					// Close popup when mouse leave
					_showTip && root.find('#list_comment div.info_avata_cmt').live({
						mouseenter: function () {
							var self = $(this);
							self.addClass('hover');
						},

						mouseleave: function () {
							var self = $(this);
							_debug && log('[info_avata_cmt] mouseleave on ' + self.attr('rel'));
							self.stop(true,true).fadeOut();
							self.removeClass('hover');
						}
					});
					
				}
            }
			/*
			// Remove meta tag refresh
			root.find('#txtComment').bind('keyup', function(e){
				_debug && log("[setOption] remove meta FREFRESH");
				// Prevent reload page
				var info = getContentComment();
				console.log('xxxx')
				console.log(info)
				if(info.content.length && 'Ý kiến của bạn' != info.content){
					$(window).bind("beforeunload",function(event) {
						return "Nội dung đang nhập sẽ bị mất"
					});
				}else{
					$(window).unbind("beforeunload");
				}
				
			});
			*/
        }
		// Check cookie debug
		_debug = !!cookies('comment_debug');

        // Mark already bind listener all dom
        _isListener = 1;
		
		window.onbeforeunload =  function() {
			var e = $.Event('webapp:page:closing');
			$(window).trigger(e); // let other modules determine whether to prevent closing
			if(e.isDefaultPrevented()) {
				// e.message is optional
				return e.message || 'Nội dung đang nhập sẽ bị mất';
			}
		};
		
		$(window).on('webapp:page:closing', function(e) {
			var info = getContentComment();
			_debug && log("[webapp:page:closing] reload page with content: " + info.content);
			if(info.content.length && 'Ý kiến của bạn' != info.content) {
				e.preventDefault();
				e.message = 'Nội dung đang nhập sẽ bị mất';
			}
		});
        return this;
    };
	
	
    var sortedBy = function(value){
        if(value != 'time' && value != 'like'){
            return false;
        }
        options.sort_by = value;
        options.offset = 0;
        isShowMore = 0;
        return false;
    }
    // Log message
    var log = function (msg) {
        if (typeof(window.console) != 'undefined') {
            console.log(msg);
        }
		return true;
    }
    /**
     * Show message by Sexy alertbox
     * @param msg
     */
    var showAlertBox = function(msg, title){
        title = title || 'Thông báo';
		var mobile_web = defaults.platform!==1 ? msg:'<div class="content_form">'+msg+'</div>';
        
		if(typeof(myvne_users) !='undefined' && typeof(myvne_users._html_popup_success) == 'function'){
			myvne_users._html_popup_success(msg, null, 5);
        }else if('function' == typeof(Sexy.notice)){
			var tpl = '<div id="login-vne4" class="login-form"><div class="ttOline">' + title + '</div><div class="content_ligbox"><div class="complete-form">' + mobile_web + '</div><div class="close-lb"></div><div class="clear"></div></div></div>';
            Sexy.notice(tpl);
        }else{
            alert(msg);
        }
    };

    var hideAlertBox =function(){
        Sexy.display(0);
        $('#BoxOverlay').hide();
    };

    /**
     * Get parameter form url
     * @param name
     * @return value of param
     */
    var getParameter = function(name){
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++)
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == name)
            {
                return sParameterName[1];
            }
        }
    };
    /**
     * Show login form
     */
    var showLoginForm = function(){

        var frmmobile = '<div class="login-right">'+
            '<div class="content_form">'+
            '<p class="login-type">Hoặc nhập thông tin của bạn</p>'+
            '<p class="login-txt">'+
            '<input id="txt_email" type="text" class="txt-login" value="Email (Không hiển thị trên trang)" onfocus="if (this.value == \'Email (Không hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Email (Không hiển thị trên trang)\';}"/>'+
            '</p>'+
            '<p id="error_email" class="login-desc"></p>'+
            '<p class="login-txt">'+
            '<input id="txt_fullname" type="text" class="txt-login" value="Họ tên (Hiển thị trên trang)" onfocus="if (this.value == \'Họ tên (Hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Họ tên (Hiển thị trên trang)\';}" />'+
            '</p>'+
            '<p id="error_fullname" class="login-desc"></p>'+
            '<p class="login-btt">'+
            '<input id="bt_finish" type="button" value="Hoàn tất" class="btt-complete">'+
            '</p>'+
            '<div class="clear"></div>'+
            ' </div>'+
            '</div>';
        //app_mobile is app mobile
        var message = app_mobile !== 1 ? 'Hoặc nhập thông tin của bạn' : 'Nhập thông tin của bạn';

        var frmweb =   '<div class="login-right"><p class="login-type">'+message+'</p>' +
            '<p class="login-txt"><input id="txt_email" type="text" class="txt-login" value="Email (Không hiển thị trên trang)" onfocus="if (this.value == \'Email (Không hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Email (Không hiển thị trên trang)\';}"/></p><p id="error_email" class="login-desc"></p>'+
            '<p class="login-txt"><input id="txt_fullname" type="text" class="txt-login" value="Họ tên (Hiển thị trên trang)" onfocus="if (this.value == \'Họ tên (Hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Họ tên (Hiển thị trên trang)\';}" /></p>' +
            '<p id="error_fullname" class="login-desc"></p>'+
            '<p class="login-btt"><input id="bt_finish" type="button" class="btt-complete" value="Hoàn tất" /></p>' +
            '</div>';

        var frmnd = defaults.platform!==1 ?  frmweb: frmmobile;

        var  domleft ='<div class="login-left">' +
            '<p class="login-type login-type-mis">Đăng nhập bằng</p>' +
            '<p class="p-social"><a rel="facebook" href="#" class="login-social lg-facebook"></a></p>' +
            '<p class="p-social"><a rel="google" href="#" class="login-social lg-google"></a></p>' +
            '</div>';

        domleft = app_mobile !== 1 ? domleft : '';

        Sexy.notice('<div id="login-vne1" class="login-form"><div class="content_ligbox">'+domleft+frmnd+'<div class="close-lb"></div><div class="clear"></div></div>');

        $('.login-social').bind('click', function(e){
            e.preventDefault();
            showOpenId($(this));
        });

    };
    /**
     * Show login form
     */
    var showLoginForm_App = function(obj){
        $('#lblMessageApp').hide();
        var appHTML = '<div class="content_form">'+
            '<p class="login-txt">'+
            '<input id="txt_email" type="text" class="txt-login" value="Email (Không hiển thị trên trang)" onfocus="if (this.value == \'Email (Không hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Email (Không hiển thị trên trang)\';}"/>'+
            '</p>'+
            '<p id="error_email" class="login-desc"></p>'+
            '<p class="login-txt">'+
            '<input id="txt_fullname" type="text" class="txt-login" value="Họ tên (Hiển thị trên trang)" onfocus="if (this.value == \'Họ tên (Hiển thị trên trang)\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Họ tên (Hiển thị trên trang)\';}" />'+
            '</p>'+
            '<p id="error_fullname" class="login-desc"></p>'+
            '<p class="login-btt">'+
            '<input id="bt_finish" type="button" value="Hoàn tất" class="btt-complete">'+
            '</p>'+
            '<div class="clear"></div>'+
            ' </div>';

        obj.parent().parent().parent().parent().parent().find('.content_form').remove();
        obj.parent().parent().parent().parent().parent().prepend('<div id="login-vne1" class="login-form">'+appHTML+'<div class="clear"></div>');
    };
    /**
     * Show lightbox
     * @param String: message to show
     * @param Int: width of lightbox
     * @return {Boolean} Return value after show
     */
    var showLightBox = function(msg, width){
        if('object' == typeof (Lightbox)){
            var tpl = '<div id="comment_mobile_lightbox" style="display:none;">'
                    + '<div class="lightbox-content01">'
                    + '<div class="main_box_vipham">'
                    + '<div class="box_suscess">'
                    + '<div class="main_content_suscess"><p class="txt_center"><b>' + msg + '</b></p></div>'
                    + '<div class="txt_bottom txt_note txt_center">'
                    + '<input id="bt_close_empty_comment_from" type="button" value="Đóng" class="btn_finish" onclick="javascript:Lightbox.hide();return false;">'
                    + '</div>'
                    + '</div>'
                    + '<div class="clear">&nbsp;</div>'
                    + '</div>'
                    + '</div>'
                    + '</div>'
                ;
            width = width || 100;
            Lightbox.show('comment_mobile_lightbox', 'Thông báo', width);
        }else{
            alert(msg);
        }
        return true;
    };

    /**
     * Show form input report bad
     */
    var showReportBadForm = function(obj){
        if(typeof(Sexy.notice)=='undefined'){
            console.log('Sexy.notice is not defined');
            return;
        }
        if(cookies('report_bad_' + $(obj).attr('rel')) == 0){
            showAlertBox('Bạn đã sử dụng chức năng này. Bạn phải đợi 24h nữa mới được thao tác tiếp');
            return false;
        }
        var options = [{value:0, text:'Chọn'},
            {value:1, text:'Xúc phạm, gây hại người khác'},
            {value:2, text:'Lạc đề'},
            {value:3, text:'Vi phạm pháp luật'},
            {value:4, text:'Vi phạm đạo đức, thuần phong mỹ tục'},
            {value:5, text:'Tố cáo sai sự thật'},
            {value:6, text:'Vi phạm bản quyền'},
            {value:7, text:'Để lộ thông tin cá nhân'},
            {value:8, text:'Spam, rác'},
            {value:9, text:'Lý do khác'}
        ];
        var optionText = '<select class="txt_select_lydo" id="reason" tabindex="1">';
        $.each(options, function(index, option){
            optionText += '<option value="' + option.value+'">' + option.text+'</option>';
        });
        optionText += '</select>';
        var html =
                '<div id="baovipham">'
                    + '<div class="ttOline">Báo vi phạm</div>'
                    + '<div class="complete-form">'
                    + '<div class="item_form">'
                    + '<p class="share_email_label">Chọn lý do vi phạm</p>'
                    + optionText
                    + '<p class="login-desc width_common"></p>'
                    + '<div class="clear">&nbsp;</div>'
                    + '</div>' /* end <div class="item_form> */
                    + '<div class="item_form">'
                    + '<p class="share_email_label">Ý kiến:</p>'
                    + '<textarea name="message" tabindex="2" class="txt-login" onfocus="if (this.value == \'Nội dung phản hồi khác\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Nội dung phản hồi khác\';}" value="Nội dung phản hồi khác">Nội dung phản hồi khác</textarea>'
                    + '</div>' /* end <div class="item_form> */
                    + '<div class="item_form">'
                    + '<p class="share_email_label">Địa chỉ email của bạn:</p>'
                    + '<input name="email" type="text" tabindex="3" class="txt-login" value="Email" onfocus="if (this.value == \'Email\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Email\';}" />'
                    + '<p class="login-desc width_common"></p>'
                    + '</div>' /* end <div class="item_form> */

                    + '<div class="sercurity">'
                    + '<p class="share_email_label">Mã xác nhận</p>'
                    + '<div class="width_common">'
                    +'<div class="block_inputconfirmcapcha">'
                    + '<div class="left block_txt_capcha">'
                    + '<input type="text" tabindex="4" class="txt-capcha" name="txtCaptcha" maxlength="4">'
                    + '<p class="login-desc width_common"></p>'
                    + '</div>' /* end <div class="left block_txt_capcha"> */
                    + '<div class="input_capcha left"><img src="images/graphics/100-30.gif"></div>'
                    + '<a href="#" tabindex="5" class="left refresh_captcha"><img src="http://st.f1.vnecdn.net/responsive/images/refesh.gif"></a>'
                    +'</div>' /* end <div class="block_inputconfirmcapcha"> */
                    + '</div>' /* end <div class="width_common> */
                    + '<div class="width_common">'
                    +'<input id="btt_send_report_bad" tabindex="6" type="button" value="Gửi" class="btt-complete" rel="' + $(obj).attr('rel') +'" style="margin:0 0;">'
                    + '</div>' /* end <div class="width_common> */
                    + '</div>' /* end <div class="sercurity> */

                    + '<div class="clear">&nbsp;</div>'
                    + '</div>' /* end <div class="complete-form">*/
                    + '<div class="close-lb"></div>'
                    + '<div class="clear">&nbsp;</div>'
                    + '</div>' /*End <div id="baovipham">*/
            ;
        Sexy.notice(html);
        $('#SexyAlertBox-Box .txt_select_lydo').css('visibility', 'visible');

        // Get captcha code
        getCaptchaCode();

        // Bind event refresh code
        $('#SexyAlertBox-Box .refresh_captcha').bind('click', function(e){
            e.preventDefault();
            getCaptchaCode();
        });

    };
    var checIE8 = function(){
        var myNav = navigator.userAgent.toLowerCase();
        return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) == 8 : false;
    };
    var getCaptchaCode = function () {
        $.getJSON(server_url + '/captcha/show?callback=?', {deviceenv:1}, function (response) {
            $('#SexyAlertBox-Box .input_capcha').html(response.html);
            if(checIE8()){
                $('#SexyAlertBox-Box .input_capcha img').css('max-width', 'none');
                _debug && log('Set max-width for captcha image');
            }
        });
    };
    /**
     * Trim string value
     * @param String value
     * @return {String} return string after trim left and right
     */
    var trim =function(value){
        if('undefined' == typeof (value) || '' ==value ){
            return '';
        }
        return value.replace(/^\s+|\s+$/g, '');
    }
    /**
     * Validate email
     * @param string: email
     * @return {Boolean} True if email is valid
     */
    var validateEmail = function(email) {
        email = trim(email);
        if(email === ''){
            return false;
        }
        var re = /^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i;
        return re.test(email);
    };

    /**
     * Get script with callback function
     * @param {String} url: Relative path
     * @param {Array} options: Data send to server
     * @param {Function} callback: Function will be call when request done
     */
    var getScript = function (url, options, callback) {
        options = options || {};
        options.data = options.data || {};
        url = server_url + '/' + url + '?callback=?';
        $.getJSON(url, options).done(function (reponse) {
            ('function' == typeof callback) && callback(reponse);
        });
    };

    var getScriptExt = function (url, options, callback) {
        options = options || {};
        options.data = options.data || {};
        $.ajax({
            url: url,
            data: options,
            dataType: "jsonp",
            jsonpCallback: "InteractionCallback",
			cache: true,
            success: function(response){
                ('function' == typeof callback) && callback(response);
            }
        });
    };

    var cookies = function (key, value, options) {
        if (arguments.length > 1 && String(value) !== "[object Object]") {
            options = (typeof options === 'object') ? options : {};
            if (value === null || value === undefined) {
                options.expires = -1;
            }
            if (typeof options.expires === 'number') {
                var expireTime = 86400000 * parseInt(options.expires), t = options.expires = new Date(), time = t.getTime();

                options.expires.setTime(time + expireTime);
            }
            value = String(value);
            return (document.cookie = [encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '',
                options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''].join(''));
        }
        options = value || {};
        var result, decode = options.raw ? function (s) {
            return s;
        } : decodeURIComponent;
        return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
    };

    /* zone */
    // Set total comment
    var setTotalComment = function (total, totalParent) {
        if ('undefined' == typeof (totalEl)) {
            totalEl = root.find('#total_comment');
        }
        if (total) {
            totalEl.html(format_number(total));
            if($('.video_yk').length){
                $('.video_yk').find('#total_comment').html(format_number(total));
            }
            root.find('.ykien_vne').show();
            if (defaults.platform ===1) {
                root.find('.ykien_vne').find('.txt_songuoithich').hide();
            }
            if(totalParent > 12 && isShowMore == 0){
                root.find('.view_more_coment').show();
                root.find('#cmt-paginator').hide();
            }
        }
    };
    var format_number = function (number, decimals, dec_point, thousands_sep)
    {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    };
    var makePaginator = function(total) {

        if (defaults.platform === 1) {// is mobile
            makePaginatorMobile(total);
        } else {//web, tablet
            makePaginatorWeb(total);
        }
    };
    //make Paginator for mobile
    var makePaginatorMobile = function(total){
        listCommentEl.find('#show_more_coment').parent('.view_more_coment').remove();
        if( total > (options.offset + options.limit)){
            listCommentEl.append('<div class="view_more_coment left" style="' + (isShowMore ==1? "" :"display:none;" )+'"><a id="show_more_coment" href="#" class="txt_666">Xem thêm</a></div>');
        }

    };
    //make Paginator for website, Tablet
    var makePaginatorWeb = function(total){
        _debug && log("[makePaginatorWeb] Total page: " + total);
        if(total <= 24){
            return;
        }
        var totalPage = Math.ceil(total / options.limit);

        //console.log(pageStart, pageEnd, totalPage);
        var currentPage = ( options.offset / options.limit ) + 1, pageStart = 1, pageEnd;

        pageStart = (currentPage - 2) > 0 ? (currentPage - 2) : 1;
        pageEnd = (currentPage + 2) > totalPage ? totalPage : currentPage + 2;

        if ((currentPage === 1 || currentPage === 2) && totalPage > 4) {
            pageEnd = 5;
        } else if (totalPage <= 4) {
            pageEnd = totalPage;
        } else {
            pageEnd = (currentPage + 2) >= totalPage ? totalPage : currentPage + 2;
            //console.log(currentPage, pageStart, pageEnd, totalPage);
        }
        if (pageStart < 0) {
            pageEnd = 5;
            pageStart = 1;
        }
        if (pageEnd > totalPage) {
            pageEnd = totalPage;
            pageStart = currentPage - 4;
        }

        // Re Change
        if (totalPage - currentPage < 2) {
            pageStart = (pageEnd - 4) > 0 ? pageEnd - 4 : 1;
        }

        var pagination = '';
        if(currentPage > 1 && totalPage >0){
            pagination += '<a href="javascript:;" class="cmt-pagination pagination_btn pa_prev" rel="'+(currentPage-1)+'"></a>';
        }

        for(var index = pageStart; index <= pageEnd; index++){

            if (currentPage == index) {
                pagination += '<a class="active cmt-pagination" href="javascript:;" rel="' + (index) + '">' + index + '</a>';
            } else {
                pagination += '<a class="cmt-pagination" href="javascript:;" rel="' + (index) + '">' + index + '</a>';
            }
        }
        if(currentPage < totalPage){
            pagination += '<a href="javascript:;" class="cmt-pagination pagination_btn pa_next" rel="' + (currentPage+1) + '"></a>';
        }
        var cmt_paginator = $('#cmt-paginator');
        cmt_paginator.find('a.cmt-pagination').remove();
        cmt_paginator.find('div.pagination_news').append(pagination);


        // TODO: remove this because not using
        cmt_paginator.find('a.cmt-pagination').bind('click', function(e){
            e.preventDefault();

            var newPage = parseInt($(this).attr('rel'));
            options.offset = options.limit * (newPage-1);
            root.find('#cmt-paginator').fadeOut(200);
			root.find('#comment_reply_wrapper').hide().appendTo('#box_comment');
            _getMoreComment(options);
            //var new_position = $('#total_comment').offset();
            //$('html,body').animate({scrollTop: new_position.top - 30},1000);
        });
        if(isShowMore == 1){
            root.find('#cmt-paginator').fadeIn(200);
        }

    };

    var _getMoreComment = function(options, el){
        isShowMore = 1;

        $('.loading').show().appendTo($(el).closest('#list_comment').find('.comment_item:last'));
        getScript('index/get', options, function (response) {
            // Check eror
            if (!response.error) {
                // Check data
                if (response.data.total) {
                    // Display total comment
                    setTotalComment(response.data.totalitem, response.data.total);
                    // Display list comment
                    displayComment(response.data.items, response.data.total);
                    //make Paginator
                    makePaginator(response.data.total);
                }
            }
            // Add listener
            //initEvent();
        });
    };

    var validateComment = function(obj){
        var id = obj.attr('id');
        var btnReply = (id === 'comment_reply_button') ? 1 : 2 ; // 1 : reply, 2 : cmt

        var that = obj.parent().parent().parent(),
            contentEl = that.find('#txtComment'),
            contentInfo = getContentComment(),
            content = trim(contentInfo.content);
        if($('#btnReply').length==0){
            that.append('<input type="hidden" id="btnReply" value='+btnReply+'>');
        }else{
            $('#btnReply').val(btnReply);
        }
        // Validate content
        if('Ý kiến của bạn' == content || '' == content){
            app_mobile !== 1 ? showAlertBox('Bạn chưa nhập nội dung bình luận.') : showMessageApp('Bạn chưa nhập nội dung bình luận.',btnReply);
            (defaults.platform !== 1) && contentEl.focus();
            return false;
        }else{
            if(useReply != 1 && options.useRating){
                var userRate = parseInt(ratingEl.attr('data-rating'));
                if(!userRate){
                    showAlertBox('Bạn chưa đánh giá.');
                    return false;
                }
            }
            // Check exist myvne
            if(typeof(myvne_users) == 'object' && typeof(myvne_users.authentication) == 'function'){
                // Use comment with myvne
                myvne_users.authentication(function(){
                    var myvneInfo = myvne_users.profile;
                    // Check info
                    if(!myvneInfo.user_email || !myvneInfo.user_fullname){
                        return false;
                    }

                    _member.fullname = myvneInfo.user_fullname;
                    _member.email = myvneInfo.user_email;
                    _member.userid = myvneInfo.user_id;
                    _member.user_session = myvneInfo.user_session;
                    _member.usertype = myvneInfo.user_login_type;

                    // Add comment
                    addComment(content, contentInfo.parent_id, contentInfo.reply_to);
                    if(!root.find('.like_google .avata_coment').length){
                        setUiMyVNE(myvneInfo);
                    }
                });
            }else{
                // use comment witl old style
                if(_member.fullname && _member.email){
                    if(_member.type){
                        addComment(content, contentInfo.parent_id, contentInfo.reply_to);
                    }else{
                        showLoginForm();
                        var loginEl = $('#login-vne1');
                        loginEl.find('#txt_email').val(_member.email);
                        loginEl.find('#txt_fullname').val(_member.fullname);
                    }

                }else{
                    // Fill email, fullname
                    if(app_mobile === 1){
                        var email = trim(that.find('#txt_email').val());
                        if(email == '' || !validateEmail(email)){
                            that.find('#error_email').html('Địa chỉ email không hợp lệ.');
                        }else{
                            that.find('#error_email').html('');
                            _member.email = email;
                            var info = getContentComment();
                            addComment(info.content, info.parent_id, info.reply_to);
                        }
                    }else{
                        showLoginForm();
                    }

                }
            }

            return false;
        }
    };
    /**
     * Add comment
     * @param obj
     * @return {Boolean}
     */
    var addComment = function(content, parentComment, replyTo){

        var params = jQuery.extend({}, options);

        params.content = content;
        if(_member.fullname == 'Họ tên (Hiển thị trên trang)' || _member.fullname == null){
            _member.fullname = '';
        }
        if(_member.fullname == ''){
            _member.fullname = _member.email.replace(/@.*/, '');
        }
        params.fullname = _member.fullname;
        params.email = _member.email;
        if(typeof(_member.userid) != 'undefined' && typeof(_member.usertype) != 'undefined'){
            params.userid = _member.userid;

            params.usertype = _member.usertype;
        }
        if(typeof(_member.user_session) != 'undefined'){
            params.user_session = _member.user_session;
        }
        if(parentComment){
            params.parentid = parseInt(parentComment);
        }else{
            params.parentid = 0;
        }
        if(defaults.platform == 1){
            params.frommobile = 1;
        }
        if(replyTo){
            params.reply_to = parseInt(replyTo);
        }
        // Check share facebook
        if(_member.share && _member.type == 'facebook'){
            params.shareurl = window.location.href.split('?')[0];
            params.share = 1;
        }

        // Have user rating
        if(options.useRating){
            params.rating = getUserRating();
            if(useReply == 0 && options.objecttype == 55){
                var advantage = trim(root.find('#txtAdvantage').val());
                var disAdvantage = trim(root.find('#txtDisAdvantage').val());
                if(advantage.length && 'Ý kiến của bạn' != advantage){
                    params.advantage = advantage;
                }
                if(disAdvantage.length && 'Ý kiến của bạn' != disAdvantage){
                    params.dis_advantage = disAdvantage;
                }
            }
        }
        hideAlertBox();
        getScript('index/add', params, function (response) {
            // Check eror
            if (!response.error) {
                if(app_mobile===1){
                    showMessageApp('Bạn đã gửi ý kiến thành công',btnReply);
                }else{
                    showAlertBox('Bạn đã gửi ý kiến thành công');
                    setTimeout(function(){hideAlertBox();}, DELAY_TIME);
                }
                //remove style of auto height textarea 
                $('#txtComment').removeAttr( "style" );
                if(params.parentid > 0){
                    root.find('#comment_reply_wrapper').fadeOut(200);
                }
                // Clear content
                var contentEl = useReply ? $('#comment_reply_form').find('#txtComment')  : $('#comment_post_form').find('#txtComment');
                contentEl.val('');
                contentEl.val('Ý kiến của bạn');
                if(useReply == 0 && options.objecttype == 55){
                    root.find('#txtAdvantage').val('');
                    root.find('#txtDisAdvantage').val('');
                }
                if(options.useRating){
                    ratingEl = root.find('#comment_rating');
                    ratingEl.attr('data-rating', 0);
                    ratingEl.find('.star').removeClass('hover');
                    root.find('#user_rating').html(0);
                }
                // Show popup share
                if( _member.type == 'google' && _member.share){
                    window.open('https://plus.google.com/share?url='+encodeURIComponent(location.href),'sharewindow','width=626,height=436');
                }
				if(typeof(response.data.new_id_comment) != 'undefined'){
					postReview(params, response.data.new_id_comment);
				}
                return true;
            }else if(response.error == 102){
                if(typeof(response.errorDescription) != 'string'){
                    if(app_mobile===1) {
                        showMessageApp('Bạn không thể gửi bình luận liên tục. Xin hãy đợi ' + response.errorDescription + ' giây nữa.',btnReply);
                    }else{
                        showAlertBox('Bạn không thể gửi bình luận liên tục. Xin hãy đợi ' + response.errorDescription + ' giây nữa.');
                        setTimeout(function(){hideAlertBox();}, DELAY_TIME);
                    }
                }else{
                    if(app_mobile===1) {
                        showMessageApp('Bạn không được bình luận liên tục.',btnReply);
                    }else{
                        showAlertBox('Bạn không được bình luận liên tục.');
                        setTimeout(function(){hideAlertBox();}, DELAY_TIME);
                    }
                }
                return false;
            }else{
                if(app_mobile===1) {
                    showMessageApp(response.errorDescription,btnReply);
                }else{
                    showAlertBox(response.errorDescription);
                    setTimeout(function(){hideAlertBox();}, DELAY_TIME);
                }

                return false;
            }
        });
    };
    /**
     * Validate and add report bad
     * @param el
     */
    var addReportBad = function(){
        var rootEl = $('#SexyAlertBox-Box #baovipham');
        var elReason = rootEl.find('#reason');
        var elReasonMsg = elReason.closest('.item_form').find('.login-desc'), reason = parseInt(elReason.val());

        if(reason == 0){
            elReasonMsg.html('Chọn lý do vi phạm');
            elReason.focus();
            return false;
        }else{
            elReasonMsg.html('');
        }
        // Get email
        var emailEl = rootEl.find('input[name="email"]'), email = emailEl.val();

        if(email == 'Email'){
            email = '';
        }

        // Validate email if user has input
        if(email){
            var emailElMsg = emailEl.closest('.item_form').find('.login-desc');
            if(!validateEmail(email)){
                emailElMsg.html('Địa chỉ email không hợp lệ.');
                return false;
            }else{
                emailElMsg.html('');
            }
        }

        // Get captcha
        var captchaEl = rootEl.find('input[name="txtCaptcha"]'),
            captcha = trim(captchaEl.val()),
            captchaElMsg =  captchaEl.closest('.block_txt_capcha').find('.login-desc');
        if(captcha.length != 4){
            captchaElMsg.html('Mã xác nhận không đúng');
            captchaEl.focus();
            return false;
        }else{
            captchaElMsg.html('');
        }

        // All data is valid, add this to server
        var param = {reason: reason,
            captcha: captcha,
            captchaCode: rootEl.find('#captchaID').val(),
            commentid: rootEl.find('#btt_send_report_bad').attr('rel'),
            siteid:options.siteid,
            linkbad: window.location.href
        };

        // Has email
        if(email.length){
            param.email = email;
        }
        // Has message
        var message = trim(rootEl.find('textarea[name="message"]').val());
        if(message.length && message != 'Nội dung phản hồi khác'){
            param.message = message;
        }
        // Get myvne user id
        if(typeof(myvne_users) == 'object' && typeof(myvne_users.profile) == 'object'){
            var myvneInfo = myvne_users.profile;
            if(myvneInfo.user_id){
                param.userpost = myvneInfo.user_id;
            }
        }
        getScript('index/reportbad', param, function (response) {
            if(!response.error){
                hideAlertBox();
                cookies('report_bad_'+param.commentid, 0, {expires:2400 /* 1 day*/});

                showAlertBox('Báo vi phạm thành công');
                setTimeout(function(){hideAlertBox();}, DELAY_TIME);

            }else if(response.error == 10){
                var captchaEl = $('#SexyAlertBox-Box input[name="txtCaptcha"]'),
                    captchaElMsg =  captchaEl.closest('.block_txt_capcha').find('.login-desc');
                captchaElMsg.html('Mã xác nhận không đúng');
            }
        });
    }
    /**
     * Get all reply of comment
     * @param obj
     */
    var getReply =function(obj){
		var REPLY_PER_PAGE = 12;
        var params = {siteid: options.siteid,
            objectid: options.objectid,
            objecttype: options.objecttype,
            id: obj.attr('rel'),
            limit: REPLY_PER_PAGE,
			offset: parseInt(obj.attr('data-offset')) + 1,
            cookie_aid: options.cookie_aid,
            sort_by: options.sort_by
        };
        $('.loading').show().appendTo($(obj).closest('.sub_comment').find('.subcomment_item:last'));
        obj.hide();
        getScript('index/getreplay', params, function (response) {
            if(!response.error){
                var parent = $(obj.parents('.sub_comment'));
                $.each(response.data.items, function (index, value) {
                    if(typeof(value.content) != 'undefined'){
                        parent.append('<div class="subcomment_item width_common">' + getCommentItemDetail(value) + '</div>');
                    }
                });
				addListenerForCommentDetail(parent);
				var total = obj.data('total');
				response.data.offset += REPLY_PER_PAGE;
				console.log(total);
				console.log(response);
				if(total > response.data.offset){
					obj.attr('data-offset', response.data.offset);
					parent.append(obj.parent());
					obj.show();
				}else{
					obj.parent().remove();
				}
                $('.loading').hide().appendTo(root);

                // Parser user myVNE
                parseUserMyVNE();
				
				// Check focus to comment
				if(typeof(options.commentid) != 'undefined'){
					focusToComment();
				}
				
            }

        });

    };
    /**
     * Display comment, sub comment
     * @param comment item
     */
    var displayComment = function (obj, totalComment) {
        // Move class loading to root
        $('.loading').hide().appendTo('#box_comment');
        if(options.offset == 0 || defaults.platform == 4){
            $('#list_comment').html('');
        }
        if(root.find('#list_comment').html() == '' && isShowMore == 1 && defaults.platform == 4){
            var new_position = $('#total_comment').offset();
            $('html,body').animate({scrollTop: new_position.top - 30},1000);
        }

        // Render content
        $.each(obj, function (index, value) {
            listCommentEl.append(getCommentItem(index, value));
        });
        // Attach event reply
        addListenerForCommentDetail(listCommentEl);

        // Attach event view reply
        root.find('.btn_view_reply > .a-viewMore').bind('click', function(e){
            e.preventDefault();
            getReply($(this));
        });
        $('.view_more_coment a').bind('click', function(e){
            e.preventDefault();
            //show sub comment 
            $.each(root.find('a.nickname:hidden'), function(e) {
                var that =$(this);
                var subItem = that.closest('.subcomment_item');
                if (subItem.length) {
                    subItem.show();
                }
                that.closest('.comment_item').show();

            });
            $(this).parents('.view_more_coment').hide();

            // View comment hidden comment
            listCommentEl.find('#show_more_coment').parent('.view_more_coment').show();

            root.find('#cmt-paginator').fadeIn(200);

        });

        if(isShowMore == 0 && options.offset == 0 && typeof (options.commentid) =='undefined'){
            $.each(root.find('a.nickname::gt(11)'), function(e) {
                var subItem = $(this).closest('.subcomment_item');
                if (subItem.length) {
                    subItem.hide();
                } else {
                    $(this).closest('.comment_item').hide();
                }

            });
        }else if(typeof (options.commentid) !='undefined'){
            // Hide view more
            root.find('div.view_more_coment').hide();
            // Show paginator
            root.find('#cmt-paginator').fadeIn(200);

            // Scroll to selected comment
            focusToComment(options.commentid);

            // Remove commentid
            delete(options.commentid);
        }
        // Add event view all reply
        listCommentEl.find('a.view_all_reply').bind('click', function(e){
            e.preventDefault();
            var myParent = $(this).closest('.sub_comment');
            // Remove class end
            myParent.find('.subcomment_item:last').removeClass('end');

            // Show subcomment was hidden
            myParent.find('.subcomment_item:hidden').fadeIn(200);

            // Get reply comment
            getReply($(this));
        });

        // Show button view more
        if(options.offset + options.limit < totalComment){
            $('.btn_viewmore').show();
        }else{
            $('.btn_viewmore').hide();
        }

        // Parser user myVNE
        parseUserMyVNE();
    };
    var showMessageApp = function (message) {
        var parent = ($('#btnReply').val() == 1 ) ?  root.find('#comment_reply_wrapper') : root.find('#comment_post_form');
        if(parent.find('#error_comment').length==0){
            $('#error_comment').remove();
            parent.before('<div id="error_comment">' + message + '</div>');
        }else{
            parent.find('#error_comment').html(message);
        }
    };
    /**
     * Generate html content
     * @param obj Comment item
     * @return {String} Html content
     */
    var getCommentItem = function (num, obj) {
        if(typeof(obj.content) == 'undefined'){
            return '';
        }
        var result = '<div class="width_common">' + getCommentItemDetail(obj, 1) + '</div>';
        var moreclass = ( num % 2 ) ? " hight_light" : "";
        // Check have child comment
        result += '<div class="sub_comment">';
        if (obj.replys.total) {

            $.each(obj.replys.items, function (index, value) {
                if(typeof(value.content) != 'undefined'){
                    result += '<div class="subcomment_item width_common' + ((index == 1 && obj.replys.total > 2) ? ' end' : '')+'">' + getCommentItemDetail(value) + '</div>';
                }
            });
            if(obj.replys.total > 2 && obj.replys.total > obj.replys.items.length){
                result += '<div class="txt_view_more width_common"><a href="#" class="txt_blue view_all_reply" rel="' + obj.comment_id +'" data-total=" ' + obj.replys.total + '" data-offset="1">Xem tất cả ' + obj.replys.total + ' trả lời</a></div>';
            }

        }
        result += '</div>';// End <div class="sub_comment">

        return '<div class="comment_item'+moreclass+'"><div class="right width_comment_item width_common">' + result + '</div><div class="clear">&nbsp;</div></div>';
    };
    /**
     * Generate html content detail
     * @param obj: Comment items
     * @param isParent: 1 when it is parent comment
     * @return {String}: Html content
     */
    var getCommentItemDetail = function (obj, isParent) {
        var content, web;
        if(options.objecttype == 55 || options.objecttype == 70){
            content = obj.content;
        }else{
            var arrContent = obj.content.split(' ');
            var totalWord = 50;
            if(arrContent.length > 60){
                content = '<p class="content_less" rel="content_more">' + arrContent.splice(0,totalWord).join(' ') + ' ... ' + '<a class="icon_show_full_comment" rel="content_more" href="javascript:void(0);" alt="Xem đầy đủ nội dung" title="Xem đầy đủ nội dung">&nbsp;</a></p>' +
                    '<p class="content_more" rel="content_less" style="display:none;">' + obj.content + '<a class="icon_show_full_comment icon_tru" rel="content_less" href="javascript:void(0);" alt="Thu gọn nội dung" title="Thu gọn nội dung">&nbsp;</a></p>';
            }else{
                content = '<p class="full_content">' + obj.content + '</p>';
            }
        }

        // Tra loi, thich, total like
        if(app_mobile != 1){
            // Layout tra loi thich tren web
            web = '<p class="txt_666 txt_11 right block_like_web">'+
                '<a id="' + obj.comment_id + '" class="txt_blue txt_11 link_reply" href="javascript:;" rel="' + obj.comment_id + '" parent="' + obj.parent_id + '"><span class="icon_portal icon_feedback">&nbsp;</span> <b>Trả lời</b></a> &nbsp;|&nbsp;'+
                '<a id="' + obj.comment_id + '" class="txt_666 txt_11 link_thich" href="javascript:;" rel="' + obj.comment_id + '">Thích&nbsp;'+
                '<span class="icon_portal icon_like' + (obj.like_ismember ? ' unline': '') +'">&nbsp;</span></a>&nbsp;'+
                '<a class="txt_666 txt_11 total_like" href="javascript:;">' + format_number(obj.userlike) + '</a>'+
                (defaults.platform !== 1 ? '&nbsp;|&nbsp;<a class="txt_blue txt_11 report_bad" rel="' + obj.comment_id + '" href="#" title="Vi phạm" alt="Vi phạm">Vi phạm</a>' : '') +
                '&nbsp;|&nbsp;<a href="javascript:;" rel="' + obj.comment_id + '" class="share_cmt_fb txt_blue txt_11">Chia sẻ</a>' + 
                '</p>';
        }else{
            // Layout tra loi thich tren mobile app
            web = '<span class="toggle_arrow_cm"><i class="ico_arrow"></i></span>'
                +'<div class="like_rep_ios">'
                +    '<div class="like ">'
                +       '<a class="link_thich_app" id="' + obj.comment_id + '" href="javascript:;" rel="' + obj.comment_id + '">Thích&nbsp;<i class="icon_like"></i>&nbsp;.&nbsp;<span class="total_like">' + format_number(obj.userlike) + '</span></a>'
                +    '</div>'
                +   '<div class="rep"><a title="" href="'
                // Link to page detail
                + (server_url + '/widget/mcommentdetail?siteid=' + options.siteid + '&categoryid=' + options.categoryid + '&objectid=' + options.objectid + '&objecttype=' + options.objecttype + '&parentid=' + obj.parent_id + '&reply_to=' + obj.comment_id + '&commentid=' +  obj.comment_id + '&title=' + encodeURIComponent(options.title))
                + '">Trả lời</a></div>'
                + '</div>'
            ;
        }
        var linkProfile = 'javascript:;', title = '';
		var isMyUser = 0 < obj.userid && typeof(obj.type) != 'undefined' && obj.type != 4;
        //if(defaults.platform !== 1 && app_mobile != 1 && isMyUser){
		if(isMyUser){
            content += '<div class="user_status width_common myuser" data-userid="' + obj.userid +'" data-user-type="' + obj.type +'">';
            if((typeof(myvne_users) == 'object' && typeof(myvne_users.myvne_url) != 'undefined')){
                linkProfile =  myvne_users.myvne_url + '/users/feed/'  + obj.userid;
				title = 'Xem trang ý kiến của ' + obj.full_name;
            }
        }else{
            content += '<div class="user_status width_common">';
        }
        return content
            + ((typeof(myvne_users) == 'object' && typeof(myvne_users.myvne_url) != 'undefined' && app_mobile != 1) ? '<a class="avata_coment" href="' + linkProfile +'"></a>' : '')
            + '<span class="left txt_666 txt_11"><a class="nickname txt_666" href="' + linkProfile +'" title="' + title +'"><b>' + obj.full_name + '</b></a> - ' + obj.time + '</span>'
            + web
            + '</div>' // End class="user_status width_common"
            ;
    };
    /*
    * Show block share comment
     */
    var showBlockShare = function(obj){
		
        var shareEl = root.find('span.block_share_cmt_fb');
        if(!shareEl.length){
            shareEl = $('<span class="block_share_cmt_fb"></span>');
            if(defaults.platform != 1){
                shareEl.append('<a data-type="facebook" href="javascript:;" title="Chia sẻ phản hồi lên facebook"><img src="http://st.f1.vnecdn.net/responsive/i/v1/graphics/icon_fb.gif" /></a>');
                shareEl.append('&nbsp;<a data-type="google" href="javascript:;" title="Chia sẻ phản hồi lên google+"><img src="http://st.f1.vnecdn.net/responsive/i/v1/graphics/icon_google.gif"/></a>');
                shareEl.append('&nbsp;<a data-type="twitter" href="javascript:;" title="Chia sẻ phản hồi lên twitter"><img src="http://st.f1.vnecdn.net/responsive/i/v1/graphics/icon_tw.gif"/></a>');
            }else{
                shareEl.append('<a data-type="facebook" href="javascript:;" title="Chia sẻ phản hồi lên facebook"><img src="http://st.f1.vnecdn.net/responsive/i/v1/icons/social_fb.gif" /></a>');
                shareEl.append('&nbsp;&nbsp;<a data-type="google" href="javascript:;" title="Chia sẻ phản hồi lên google+"><img src="http://st.f1.vnecdn.net/responsive/i/v1/icons/social_google.gif"/></a>');
                shareEl.append('&nbsp;&nbsp;<a data-type="twitter" href="javascript:;" title="Chia sẻ phản hồi lên twitter"><img src="http://st.f1.vnecdn.net/responsive/i/v1/icons/social_tweet.gif"/></a>');
            }
            shareEl.find('a').bind('click',function(e){
                e.preventDefault();
                shareComment($(this));
            });
        }
		if(shareEl.attr('data-id') != obj.attr('rel')){
			shareEl.attr('data-id', obj.attr('rel'));
			shareEl.appendTo(obj.parent());
			shareEl.fadeIn(200);
		}else{
			shareEl.removeAttr('data-id');
			shareEl.fadeOut(200);
		}
    };
    /**
     * Share comment
     * @param el
     */
    var shareComment =function(el){
        var shareEl = el.closest('.block_share_cmt_fb');
        var commentId = shareEl.attr('data-id');
        var type = el.attr('data-type');
        var pageUrl = window.location.protocol + '//' + window.location.host  + window.location.pathname + '?commentid=' + commentId;
        switch (type){
            case 'facebook':
                var title = ''
                    , img = 'undefined' != typeof article_image ? article_image : ''
                    , url = 'https://www.facebook.com/sharer/sharer.php?u='+ encodeURIComponent(pageUrl) + '&t='+ title + '&p[images][0]=' + img;
                window.open(url,'_blank','menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                break;
            case 'google':
                var url = 'https://plus.google.com/share?url='+encodeURIComponent(pageUrl);
                window.open(url,'_blank','menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=520');
                break;
            case 'twitter':
                var url = 'https://twitter.com/intent/tweet?source=webclient&text='+encodeURIComponent(pageUrl);
                window.open(url,'_blank','menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                break;
            ;
        }
    };
    /**
     * Add listener for comment item
     */
    var addListenerForCommentDetail = function(el){
        // Attach event reply
        if(app_mobile != 1){
			el.find('a.link_reply').unbind('click');
            el.find('a.link_reply').bind('click', function (e) {
                var self = $(this), parent = self.attr('parent'), rel= self.attr('rel'), appendTo, replyEl = root.find('#comment_reply_wrapper');
                e.preventDefault();
                var txtComment =replyEl.find('#txtComment');
                if(txtComment.attr('toogle') == self.attr('rel')){
                    txtComment.attr('toogle', 0);
                    replyEl.fadeOut(200);
                    return;
                }
                // Is parent comment
                if(parent == rel){
                    appendTo = self.parents('.comment_item').find('.sub_comment');
                    replyEl.fadeIn(200).prependTo(appendTo);
                }else{
                    appendTo = self.parents('.subcomment_item');
                    replyEl.fadeIn(200).appendTo(appendTo);
                }
                
                txtComment.attr({'rel': parent, 'toogle': rel});
                txtComment.attr('reply_to', (parent != rel ? rel: 0));

            });
        }else{
            // Add listener to Show button like and reply
            el.find('.toggle_arrow_cm').unbind('click');
            el.find('.toggle_arrow_cm').bind('click', function(e){
                e.preventDefault();
                var that = $(this);
                var likePanel = that.closest('div.user_status').find('.like_rep_ios');
                if(!that.data('flag')){
                    likePanel.addClass('active');
                    that.addClass('active');
                    that.data('flag', true);
                }else{
                    likePanel.removeClass('active');
                    that.removeClass('active');
                    that.data('flag', false);
                }
            });
        }
		if(app_mobile == 1){
			// Touch or click on area
			el.find('.like_rep_ios .like').bind('touchstart', function(e){
				e.preventDefault();
				var self = $(this);
				var aEl = self.find('a');
				likeComment(aEl);
			});
			el.find('.like_rep_ios .rep').bind('touchstart', function(e){
				e.preventDefault();
				var self = $(this);
				var aEl = self.find('a');
				window.location.href = aEl.attr('href');
			});
		}
        // Attach event like
        el.find((app_mobile != 1 ? 'a.link_thich' : 'a.link_thich_app')).bind('click', function (e) {
            e.preventDefault();
            likeComment($(this));
        });

        // Collapse expand content
        if(defaults.platform != 4){
            // Expand/ collapse text on mobile
            el.find('p[class^="content_"]').bind('click', function(e){
                e.preventDefault();
                var self = $(this);
                self.hide();
                self.parent().find('p.' + self.attr('rel')).show();
                var toogle = self.closest('.width_common').find('.toggle_arrow_cm');
                // Collapse/expand div like/reply when collapse/expand comment content
                if(self.hasClass('content_less') && toogle.data('flag') != true || (self.hasClass('content_more') && toogle.data('flag') != false)){
                    toogle.trigger('click');
                }
            });
            el.find('p.full_content').bind('click',function(e){
                e.preventDefault();
                var self = $(this);
                // Toggle div like/reply when click on comment content
                self.closest('.width_common').find('.toggle_arrow_cm').trigger('click');
            });
        }

        // Expand/ collapse icon
        el.find('a[class^="icon_"]').bind('click', function(e){
            e.preventDefault();
            var self = $(this);
            self.parent().hide();
            self.closest('div.width_common').find('p.' + self.attr('rel')).show();
        });
    };


    /* End  zone */
    /**
     * Get all myvne userid to get name & avatar
     */
    var parseUserMyVNE = function(){
        // Check valid myvne
        if(typeof(myvne_users) == 'object' && typeof(myvne_users.myvne_url) != 'undefined'){
            var arrUser = [];
            root.find('.myuser').each(function(item){
                var self = $(this), userid = self.data('userid');
                self.removeClass('myuser');
                if($.inArray(userid, arrUser) == -1){
                    arrUser.push(userid);
                }
            });
            // Check user
            if(!arrUser.length){
                return;
            }
			arrUser.sort();
			_debug && log('[parseUserMyVNE] Parse list user');
			_debug && log(arrUser);
            getScriptExt(myvne_users.myvne_url + '/apifrontend/getusersprofile', {myvne_users_id : arrUser, cache:1},function(res){
                displayUserMyVNE(res);
            });
        }
    };
    /**
     * Display user & avatar for user myvne
     */
    var displayUserMyVNE = function(data){
        if(!data.error){

            $.each(data.arrUsers, function(index, user){
                var userEl = root.find('div[data-userid="' + user.user_id + '"]');
				// Update user name
				if(userEl.length){
					// Update size of avatar
					user.user_avatar = user.user_avatar.replace('/src/', '/c60x60/');
					var userAvatar = userEl.find('a.avata_coment'), userUrl = myvne_users.myvne_url + '/users/feed/' + user.user_id, nicknameEl, statusEl = userEl.closest('.user_status');
					// Update avatar
					userAvatar.find('img.img_avatar').remove();
					userAvatar.append('<img class="img_avatar" src="' + user.user_avatar + '">');
					
					// Update name
					!_showTip && userAvatar.attr({'href': userUrl, 'target': '_blank', 'title': 'Xem trang ý kiến của ' + user.user_fullname});
					nicknameEl = userEl.find('a.nickname');
					nicknameEl.attr({'href': userUrl, 'target': '_blank', 'title': 'Xem trang ý kiến của ' + user.user_fullname})
						.find('b').html(user.user_fullname);
					;
					// Remove attr
					userEl.removeAttr('data-userid');
					_showTip && addToolTip(statusEl, user);
				}
				// Update reply name
				var repUserEl = root.find('span[data-userid="' + user.user_id + '"]');
				if(repUserEl.length){
					// Update name
					repUserEl.html('@' + user.user_fullname);
					// Remove attr
					repUserEl.removeAttr('data-userid');
				}
				
            });

        }
    }
	/**
	Buil tooltip
	*/
	var addToolTip = function(el, info){
		_debug && log('[addToolTip] Add tooltip for ' + info.user_id);
		el.find('.info_avata_cmt').remove();
		var lastComment =  info.content;
		var arrContent = lastComment.split(' ');
		if(arrContent.length > 25){
			lastComment = arrContent.splice(0, 25).join(' ') + ' ...';
		}
		var totalComment = typeof(info.total_feed) != 'undefined' ? format_number(info.total_feed) : 0;
		var lastArticle = typeof(info.title) != 'undefined' ? info.title : '';
		var share_url = 'javascript:;;', profileUrl =  myvne_users.myvne_url + '/users/feed/' + info.user_id;
		if(typeof(info.share_url) != 'undefined' && typeof(info.comment_id) != 'undefined'){
			share_url = info.share_url + '?commentid=' + info.comment_id;
		}
		
		
		var tmp = '<div class="info_avata_cmt" style="display:none;">' +
		'<a href="' + profileUrl +'" class="avata_coment" target="_blank"><img src="' + info.user_avatar + '" alt="Đang tải ảnh đại diện" /></a>' +
		'<p><a href="' + profileUrl +'" target="_blank"><strong>' + info.user_fullname +'</strong></a> - <span class="txt_666">' + totalComment + ' ý kiến</span></p>' +
		'<p><img class="icon_content icon_title_coment" alt="" src="http://st.f1.vnecdn.net/responsive/i/v9/graphics/img_blank.gif">&nbsp;<a href="' + share_url + '">' + lastComment +'</a></p>' +
		'<p class="txt_666">' + info.public_time +'</p>' +
		(lastArticle.length ? '<p><strong><a href="' + share_url +'">' + lastArticle + '</a></strong></p>' : '') +
		'</div>' // End div
		;
		el.append(tmp);
	};
    /**
     * Set avatar and full namef of myvne user
     */
    var setUiMyVNE = function(profile){
		_debug && log('[setUiMyVNE] Show myvn avatar');
        var myInfo = profile || myvne_users.profile;
        var userEl = root.find('.like_google');
        if(typeof(myInfo.user_fullname) == 'undefined' || !myInfo.user_id){
            userEl.html('');
            return;
        }
		// Check exists cookie fosp_vid
		var fosp_vid = cookies('fosp_vid');
		if(typeof(fosp_vid) != 'undefined'){
			_debug && log('[setUiMyVNE] Set cookie fosp_vid:' + myInfo.user_id);
			// Create it if not exists
			cookies('fosp_vid', myInfo.user_id, {path: '/', expires: 15, domain: '.' + _domain});
		}
		// Show user avatar
        userEl.html('<div class="google_name"><a class="avata_coment" href="' +
            myvne_users.myvne_url + '/users/feed/' + myInfo.user_id +
            '" target="_blank"><img src="' + myInfo.user_avatar.replace('/src/', '/c60x60/') + '" alt=""></a>'+
            '<span>' + myInfo.user_fullname + '</span>' +
            '</div>'
        ).fadeIn(200);

    }
	/**
	* Set focus to specify comment
	*/
	var focusToComment = function(commentId){
		commentId = commentId || options.commentid;
		_debug && log('[focusToComment] Focus to comment:' + commentId);		
		// Scroll to selected comment
		var cmEl = root.find('#' + commentId);

		if(cmEl.length){
			setTimeout(function(){
				var new_position;
				if(cmEl.closest('.subcomment_item').length){
					new_position= cmEl.closest('.subcomment_item').offset().top;
				}else{
					new_position = cmEl.closest('.comment_item').offset().top;
				}
				$('html,body').animate({scrollTop: new_position},1000);
				var focus = getParameter('focus');
				if(focus == 'reply'){
					cmEl.trigger('click');
				}
			}, 200);
		}
		// Remove commentid
        delete(options.commentid);
	};
    /* End MyVNE ZONE */

    /**
     * Get comment from interactions
     * @return VNE.Comment
     */
    var getComments = function (callback) {

        if ('undefined' == typeof (options.objectid) || 'undefined' == typeof (options.objecttype)) {
            log('Vui long set objectid va objecttype');
            //return;
        }
        //root.find('div.loading').show();
        getScript('index/get', options, function (response) {
            var total = -1;
            // Check eror
            if (!response.error) {
                // Check data
                if (response.data.total) {
                    // Update Offset from client
                    if(typeof(response.data.offset) != 'undefined'){
                        options.offset = response.data.offset;
                    }


                    // Display total comment
                    setTotalComment(response.data.totalitem, response.data.total);

                    // Display list comment
                    displayComment(response.data.items, response.data.total);

                    // Build paginator
                    makePaginator(response.data.total);
                }else{
                    root.find('div.title_show').hide();
                }
                total = response.data.total;
            }
            // Call callback function
            ('function' == typeof callback) && callback(total);
        });
        return this;
    }
    /**
     * Get input comment
     * @return {Object}
     */
    var getContentComment = function(){
        var contentEl = useReply ? $('#comment_reply_form').find('#txtComment')  : $('#comment_post_form').find('#txtComment');
        var parentCommentId = contentEl.attr('rel'), replyTo = contentEl.attr('reply_to');
        if(useReply == 0 || !parentCommentId){
            parentCommentId = 0;
        }
        if(parentCommentId ==0 ){
            replyTo = 0;
        }
        return {'content' : trim(contentEl.val()), 'parent_id': parentCommentId, 'reply_to': replyTo};
    }
	/**
	* Show post review comment
	*/
	var postReview = function(param, new_id_comment){
		var item = {
			'comment_id':0,
			'parent_id': param.parentid,
			'content': param.content,
			'userid': param.userid,
			'full_name': param.fullname,
			'time': '1 phút trước',
			'isliked':0,
			'userlike':0,
			'like_ismember':0,
			'replys':{
				total: 0,
				items: []
			}
		};
		var newEl, userStatus;
		// Is reply comment
		if(param.parentid){
			newEl = $('<div class="subcomment_item width_common">' + getCommentItemDetail(item) + '</div>');
			var subCmtEl = root.find('a#' + param.parentid).closest('div.comment_item').find('.sub_comment');
			// Check total reply comment, if total < 2, show new comment
			if(subCmtEl.find('div.subcomment_item').length < 2){
				newEl.attr('data-action', 'focus');
				subCmtEl.append(newEl);
			}else{
				// View all reply
				newEl.attr('data-action', 'viewAllReply');
				subCmtEl.append(newEl.hide());
			}
		}else{
			newEl = $(getCommentItem(1, item));
			// Focus to new comment
			if(options.sort_by == 'time'){
				newEl.attr('data-action', 'focus');
			}else{
				// Reload and focus to new comment
				newEl.attr('data-action', 'reload');
			}
			root.find('#list_comment').prepend(newEl.hide());
		}
		userStatus = newEl.find('div.user_status');
		userStatus.attr({'data-cmt-id':new_id_comment});
		
		// Get temp comment
		_debug && log('[postReview] Post review of tmp id: ' + new_id_comment);
		setTimeout(function(){
			getScript('index/getcommentid',{new_cmt:new_id_comment}, function(res){
				parsePostReview(res);
			});
		}, 500);
		
	};
	/**
	* Parser commentid from temple comment id
	*/
	var parsePostReview = function(res){
		if(res.error){
			return;
		}
		// Find temp commentid
		var newEl = root.find('div[data-cmt-id="' + res.new_cmt +'"]');
		var divEl = newEl.find('a[id="0"]'), action = newEl.closest('.subcomment_item').attr('data-action');
		
		// Replace temp comment to real comment id
		divEl.attr({id:res.commentid, rel:res.commentid, parent: res.commentid});
		newEl.find('a[rel="0"]').attr({rel:res.commentid, parent: res.commentid});
		
		// is parent comment
		if(typeof(action) == 'undefined'){
			action = newEl.closest('.comment_item').attr('data-action');
		}
		
		_debug && log('[parsePostReview] Parse post review of tmp id: ' + res.new_cmt  + ' with real id: ' + res.commentid + ' with action: ' + action);
		
		// Case focus comment
		if(action == 'focus'){
			// Attach event like
			addListenerForCommentDetail(newEl);
			
			if(!newEl.is(':visible')){
				newEl.closest('.comment_item').fadeIn(200);
			}
			
			// Focus comment
			focusToComment(res.commentid);
		}else if(action == 'viewAllReply'){
			var allReplyEl = newEl.closest('.sub_comment').find('.view_all_reply');
			if(allReplyEl.length){
				newEl.closest('.subcomment_item').remove();
				options.commentid = res.commentid;
				allReplyEl.trigger('click');
			}else{
				newEl.closest('.subcomment_item').fadeIn(200);
			
				// Focus comment
				focusToComment(res.commentid);
			}
		}else if(action == 'reload'){
			options.commentid = res.commentid;
			options.offset = 0;
			getComments();
		}
		
	};
	
    var initEvent = function(app_mobile){
        // Add event for button 'Hoan tat'
        $('#bt_finish').live('click',function(e){
            e.preventDefault();
            var that = $(this).parents('#login-vne1'),

                emailEl = that.find('#txt_email'),
                fullnameEl = that.find('#txt_fullname'),
                email = trim(emailEl.val()),
                fullname = trim(fullnameEl.val());
            // Validate email
            if(!validateEmail(email)){
                that.find('#error_email').html('Địa chỉ email không hợp lệ.');
                return false;
            }else{
                that.find('#error_email').html('');
            }
            _member.email = email;
            _member.fullname = fullname;
            var info = getContentComment();
            addComment(info.content, info.parent_id, info.reply_to);
        });
        // Add event for button sign out
        $('#open_id_sigout').bind('click', function(e){
            e.preventDefault();
            if(_member.type == 'google'){
                // Google
                $('<iframe id="logout_google" src="https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout"/>').appendTo("body");
            }
            // openid/logout
            getScript('openid/logout', {type: _member.type}, function (response) {
                setTimeout(function(){$('#logout_google').remove();}, 1500);
                // reset value
                setOpenID('', '', '');
                cookies('vneoe', null, {path: '/', expires: 9999999999, domain: '.' + _domain});
                cookies('vneon', null, {path: '/', expires: 9999999999, domain: '.' + _domain});
                cookies('vneotype', null, {path: '/', expires: 9999999999, domain: '.' + _domain});
                // Update UI
                setUiOpenId();
            });

        });
        // Add event for link 'Chia se'
        $('#open_id_share').bind('click', function(e){
            e.preventDefault();
            if(_member.share == 1){
                _member.share = 0;
            }else{
                _member.share = 1;
            }
            cookies('share_' + _member.type, _member.share, {path: '/', expires: 15, domain: '.' + _domain});
            setUiOpenId();
        });
        if(app_mobile){
            $('#box_comment textarea').bind('focus', function(e){
                if(_member.email){
                    var tmpParent = $(this).parents('.input_comment');
                    tmpParent.find('#txt_email').val(_member.email);
                }
            });
        }
        // Add listener view more comment
        root.find('#show_more_coment').live('click', function(e){
            e.preventDefault();
            $(this).parent().hide();
            options.offset = options.limit + options.offset;
            _getMoreComment(options, this);
        });

        // Add listener for report bad
        root.find('.report_bad').live('click', function(e){
            e.preventDefault();
            showReportBadForm($(this));
        });

        if(app_mobile != 1){
            // Add listener for share comment
            root.find('a.share_cmt_fb').live('click', function(e){
                e.preventDefault();
                showBlockShare($(this));
            });
        }

        // Add listener validate end send report bad
        $('#SexyAlertBox-Box #btt_send_report_bad').live('click',function(e){
            e.preventDefault();
            addReportBad();
        });

        //Auto height textarea
        try{
            $('#txtComment').attr("rows", "").attr("cols", "").addClass('block_input');
            $('#comment_reply_form').find('#txtComment').attr("rows", "").attr("cols", "").addClass('block_input');
            if(typeof($.autosize) == 'function'){
                $('.block_input').autosize();
            }
        }catch(e){}

    };
    // Like comment
    var likeComment = function (item) {

        var opt = {}, status = 1;
        //var status = item.hasClass('unlike') ? 0 : 1;

        var iconLike = item.find('.icon_like');
		var status = iconLike.hasClass('unline') ? 0 : 1;
		
        opt.commentid = parseInt(item.attr('rel'));
        opt.status = status;
        opt.article_id = options.objectid;
        opt.objecttype = options.objecttype;
        opt.cookie_aid = options.cookie_aid;
        if(cookies('like_cmt_'+opt.commentid) == 0){
            showAlertBox('Bạn đã sử dụng chức năng này. Bạn phải đợi 24h nữa mới được thao tác tiếp');
            return false;
        }
        if(typeof(myvne_users) == 'object' && typeof(myvne_users.profile) == 'object'){
            var myvneInfo = myvne_users.profile;
            if(myvneInfo.user_id){
                opt.userid = myvneInfo.user_id;
                opt.token_key = myvneInfo.user_session;;
            }
        }

        // Call interaction
        getScript('index/liketoggle', opt, function (response) {
            if (true || !response.error) {
                // Get total like element
                var numberEl = item.parent().find('.total_like');
                // Parse total like
                var totalLike = parseInt(numberEl.html().replace(',', ''));
                if (status) {
                    item.addClass('unlike');
                    iconLike.addClass('unline');
                    totalLike++;
                } else {
                    item.removeClass('unlike');
                    iconLike.removeClass('unline');
                    cookies('like_cmt_'+opt.commentid, 0, {expires:2400 /* 1 day*/});
                    totalLike--;
                }
                // Update total like
                numberEl.html(format_number(totalLike));
            }
        });
        return false;
    }

    /**
     * Comment rating sohoa
     */
    var buildUserRating = function(){
        ratingEl = root.find('#comment_rating');
        var items = ratingEl.find('.star');
        var event = {
            fill: function(el) {
                var index = items.index(el) + 1;
                items.children('a').css('width', '100%').end().slice(0, index).addClass('hover').end();
                root.find('#user_rating').html(index);
            },
            drain: function() {
                items.filter('.on').removeClass('on').end().filter('.hover').removeClass('hover').end();
            },
            all: function(el) {
                var index = items.index(el) + 1;
                return true;
            },
            select: function(index){
                items.children('a').css('width', '100%').end().slice(0, index).addClass('hover').end();
            }
        };

        items.mouseover(function() {
            event.drain();
            event.fill(this);
        }).mouseout(function() {
                event.drain();
                var index = parseInt(ratingEl.attr('data-rating'));
                if(index){
                    event.select(index);
                    root.find('#user_rating').html(index);
                }else{
                    root.find('#user_rating').html(0);
                }
            }).focus(function() {
                event.drain();
                event.fill(this);
            }).blur(function() {
                event.drain();
            }).click(function(e){
                e.preventDefault();
                ratingEl.attr('data-rating', $(this).find('a').attr('data-rating'));
            });
    };
    /**
     * Get result user rating
     */
    var getUserRating = function(){
        var arrItem = [];
        var arrResult = [];
        if(options.objecttype == 70){
            arrItem = [1,2,3,4,6];
        }else if(options.objecttype == 55){
            arrItem = [8, 9, 10];
        }

        var userRate = parseInt(ratingEl.attr('data-rating'));
        $.each(arrItem, function(index, value){
            // Add item with format rating_item-userRate ex: 1-5
            arrResult.push(value + '-' + userRate);
        });
        // Return data in format 1-5;2-5
        return arrResult.join(';');
    };


    /* Public function for VNE.Comment */
    // Set params for get comment
    VNE.Comment.setOptions = setOption;
    VNE.Comment.getComments = getComments;
    VNE.Comment.sortBy = sortedBy;
    /* End Public function for VNE.Comment */

})(window, document);
/*
 * Usage:
 * Set param
 *  VNE.Comment.setOptions({objectid: 1921258, objecttype: 1, siteid: 1002565, categoryid: 1002582, limit: 5});
 * Get comment without callback function
 *  VNE.Comment.getComments();
 * Get comment with callback function
 *  VNE.Comment.getComments(function(total){console.log(total);});
 * // Change sort option
 *  VNE.Comment.sortBy('time') or VNE.Comment.sortBy('like')
 * */