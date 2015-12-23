define('resizeImage', ['jquery'], function($){
    var ResizeImage = function() {
        var delayTimer = 0;

        function delayFireOnce(timeout) {
            var d = $.Deferred();
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function() {
                d.resolve();
            }, timeout);

            return d.promise();
        }

        /**
         * resize image for detail article
         */
        function resizeImageDetail(container, objwidth) {
            var tableObj = container ? $(container).find('table[class!="tbl_fptplay"]') : $('.fck_detail table[class!="tbl_fptplay"]');
            var width_global = 0;
            var pwidth = objwidth ? $(objwidth).width() : $('.fck_detail').width();

            var checkalign = function(obj, scale) {
                if ($(obj).attr('align')) {
                    switch ($(obj).attr('align')) {
                        case 'left':
                            $(obj).css('margin-right', '10px');
                            break;
                        case 'right':
                            $(obj).css('margin-left', '10px');
                            break;
                    }
                }
                return scale;
            };

            var fcondition1 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    if (iwidth > pwidth) {
                        $(obj)
                            .width('100%')
                            .parents('table')
                            .width('100%');
                    } else {
                        var scale = (iwidth / pwidth) * 100;
                        scale = checkalign($(obj).parents('table'), scale);
                        $(obj)
                            .css('width', '')
                            .parents('table')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            var fcondition2 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    width_global = iwidth >= width_global ? iwidth : width_global;
                    if (iwidth > pwidth) {
                        $(obj)
                            .width(pwidth);
                    } else {
                        $(obj)
                            .css('width', '');
                    }
                    if (width_global < pwidth) {
                        var scale = (width_global / pwidth) * 100;
                        scale = checkalign($(obj).parents('table'), scale);
                        $(obj)
                            .parents('table')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            var fcondition3 = function(obj) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                    if (iwidth > pwidth) {
                        fcondition31(obj);
                    }
                };
                img.src = $(obj).attr('src');
            };

            var fcondition31 = function(obj) {
                if ($(obj).attr('data-width') > pwidth) {
                    $(obj)
                        .width('100%')
                        .parents('table')
                        .width('100%');
                } else {
                    var scale = ($(obj).attr('data-width') / pwidth) * 100;
                    scale = checkalign($(obj).parents('table'), scale);
                    $(obj)
                        .css('width', '')
                        .parents('table')
                        .width(scale + '%');
                }
            };

            var fcondition4 = function(obj, count_td) {
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    var td_width = (pwidth / count_td);
                    var scale = (iwidth / pwidth) * 100;
                    if (iwidth >= td_width) {
                        $(obj)
                            .width('100%')
                            .parents('td')
                            .width(scale + '%');
                    }
                    $(obj).attr({ 'data-width': iwidth, 'data-pwidth': pwidth });
                };
                img.src = $(obj).attr('src');
            };

            tableObj.each(function(i, o) {
                if ($(o).find('div[data-component="true"]').size() < 1 && $(o).parents('div[data-component="true"]').size() < 1) {
                    $(o)
                        .removeAttr('width')
                        .find('img')
                        .css('height', '');
                    var condition = $(o).find('tr').size() > 1;
                    if (condition) {
                        var count_img = count_td = 0;
                        var condition1 = condition2 = false;


                        $(o).find('tr').each(function() {
                            count_td = $(this).find('td').size();
                            $(this).find('td').each(function() {
                                count_img += $(this).find('img').size();
                                if ($(this).find('img').size() > 1) {
                                    condition2 = true;
                                }
                            });
                        });

                        condition1 = count_img < 2;
                        if (condition1) {
                            $(o).find('img').each(function() {
                                fcondition1(this);
                            });
                        } else {
                            if (condition2) {
                                $(o).find('img').each(function() {
                                    fcondition2(this);
                                });
                            } else {
                                $(o).find('img').each(function() {
                                    fcondition4(this, count_td);
                                });
                            }
                        }
                    } else {
                        $(o).find('img').each(function() {
                            fcondition3(this);
                        });
                    }
                }
            });
        };

        function resizeImagePhoto(container, objwidth) {
            var container = container ? container : '.item_slide_show';
            var imgObj = $(container).find('img');
            var pwidth = objwidth ? $(objwidth).width() : $('#article_content').width();

            imgObj.each(function(i, o) {
                //delete height of image
                $(o).css('height', '');
                var img = new Image();
                img.onload = function() {
                    var iwidth = img.width;
                    if (iwidth < pwidth) {
                        var scale = (iwidth / pwidth) * 100;
                        $(o)
                            .parents(container)
                            .width(scale + '%');
                    } else {
                        $(o)
                            .parents(container)
                            .width('100%');
                    }
                };
                img.src = $(o).attr('src');
            });
        };

        function resize(imgObj) {
            imgObj.length > 0 ? resizeImagePhoto() : resizeImageDetail();
        }
        return {
            resizeImage: resizeImageDetail,
            resizeSocial: resizeImagePhoto,
            delayFireOnce: delayFireOnce,
            resize: resize
        };
    };
    return ResizeImage;
});
define( 'email', [ 'jquery' ], function(jQuery) {
    var eMail = function (options)
    {
        var siteUrl = '';
        var pageLink = '';
        var pageTitle = '';
        var cateLink = '';
        var cateName = '';

        var loadCaptcha;

        var messages = {
            feedback_completed: 'Bạn đã gửi phản hồi thành công.',
            feedback_error: 'Có lỗi, hãy thử lại.',
            sendmail_completed: 'Bạn đã gửi email thành công.',
            sendmail_error: 'Có lỗi, hãy thử lại.',
            post_completed: 'Bạn đã gửi bài viết thành công.',
            post_error: 'Có lỗi, hãy thử lại.',
            share_completed: 'Bạn đã gửi chia sẻ thành công.',
            share_error: 'Có lỗi, hãy thử lại.',
            email_required: 'Email không được để trống.',
            email_invalid: 'Địa chỉ email không hợp lệ.',
            fullname_required: 'Họ tên không được để trống.',
            title_required: 'Tiêu đề không được để trống.',
            title_maxlength_error: 'Tiêu đề không được dài hơn 100 ký tự',
            description_required: 'Mô tả ngắn không được để trống.',
            content_required: 'Nội dung không được để trống.',
            content_minlength_error: 'Nội dung không được ít hơn 5 từ',
            post_content_required: 'Nội dung câu hỏi không được để trống.',
            captcha_required: 'Mã xác nhận không được để trống.',
            captcha_invalid: 'Mã xác nhận không đúng.'
        };

        var tmpAlert = '<div id="login-vne4" class="login-form">'
            + '<div class="ttOline">Thông báo</div>'
            + '<div class="complete-form">'
            + '<div class="content_form">{MESSAGE}</div>'
            + '</div>'
            + '<div class="close-lb"></div>'
            + '<div class="clear">&nbsp;</div>'
            + '</div>';
        var contendShareMail = '<div id="login-vne5" class="login-form">'
            + '<form action="" name="send_mai" method="post">'
            + '<div class="ttOline">Chia sẻ bài viết qua Email</div>'
            + '<div class="share_email_des">'
            + '<div class="share_email_left">'
            + '<a href="{PAGE_LINK}" title="{PAGE_TITLE}">Bài viết</a>'
            + '</div>'
            + '<div class="share_email_right">'
            + '<div>{PAGE_TITLE}</div>'
            + '<ul>'
            + '<li class="active"><a href="{SITE_URL}/" title="{SITE_TITLE}">{SITE_TITLE}</a></li>'
            + '<li class="txt_underline">&nbsp;&gt;&nbsp;<a href="{CATE_LINK}/" title="{CATE_NAME}">{CATE_NAME}</a></li>'
            + '</ul>'
            + '</div>'
            + '</div>'
            + '<div class="complete-form">'
            + '<p class="login-txt"><input type="text" class="txt-login" onblur="if(this.value==\'\') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=\'\'"  value="Email người gửi" name="sender_email" id="sender_email" /></p>'
            + '<p class="login-desc" id="errorEmail"></p>'
            + '<p class="login-txt"><input type="text" class="txt-login" onblur="if(this.value==\'\') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=\'\'"  value="Họ tên người gửi"  name="sender_name" id="sender_name" /></p>'
            + '<p class="login-desc" id="errorName"></p>'
            + '<p class="login-txt"><input type="text" class="txt-login" onblur="if(this.value==\'\') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=\'\'"  value="Email người nhận" name="receiver_mail" id="receiver_mail" /></p>'
            + '<p class="login-desc" id="errorReceiver"></p>'
            + '<p class="login-txt"><textarea name="message_mail" id="message_mail" class="txt-login" name="thongdiep" onblur="if(this.value==\'\') this.value=this.defaultValue" onfocus="if(this.value==this.defaultValue) this.value=\'\'" value="Thông điệp gửi kèm">Thông điệp gửi kèm</textarea></p>'
            + '<div class="sercurity">'
            + '<p class="share_email_label">Mã xác nhận</p>'
            + '<p class="login-txt">'
            + '<input name="txtCode" id="txtCode" type="text" class="txt-capcha" />'
            + '<span class="txt_huongdan" id="showCaptcha"></span>'
            + '<a id="refreshCaptcha" href="javascript:;" title="Tạo mã khác"><img src="{IMAGE_URL}/graphics/refesh.gif" /></a>'
            + '<input type="button" id="btnSubmit" class="btt-complete" value="Hoàn tất" />'
            + '</p>'
            + '<p class="login-desc" id="errorCode"></p>'
            + '</div>'
            + '</div>'
            + '<div class="close-lb"></div>'
            + '</form>'
            + '</div>';

        //check string is blank or not
        function blank (str)
        {
            return /^\s*$/.test(str || ' ');
        };
        function sendMail() {
            var self = this;
            var email = $('#sender_email').val();
            var name = $('#sender_name').val();
            var receiverMail = $('#receiver_mail').val();
            var messageMail = $('#message_mail').val();
            var txtCode = $('#txtCode').val();
            var isValid = true;
            var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;

            if (blank(email) || email == 'Email người gửi') {
                $('#errorEmail').text(messages.email_required);
                isValid = false;
            } else if (!filter.test(email)) {
                $("#errorEmail").text(messages.email_invalid);
                isValid = false;
            }

            if (blank(name) || name == 'Họ tên người gửi') {
                $('#errorName').text(messages.fullname_required);
                isValid = false;
            } else if (name.length > 100) {
                $('#errorName').text('Họ tên người gửi không quá 100 kí tự.');
                isValid = false;
            }

            if (blank(receiverMail) || receiverMail == 'Email người nhận') {
                $('#errorReceiver').text(messages.email_required);
                isValid = false;
            } else if (!filter.test(receiverMail)) {
                $('#errorReceiver').text(messages.email_invalid);
                isValid = false;
            }

            if (blank(txtCode)) {
                $('#errorCode').text(messages.captcha_required);
                isValid = false;
            }

            if (isValid) {
                $.ajax({
                    type: 'POST',
                    url: siteUrl + '/detail/ajaxsendmail',
                    dataType: 'json',
                    data: {
                        sender_name: name,
                        email_user: email,
                        receiver_mail: receiverMail,
                        title_email: pageTitle,
                        message_mail: messageMail,
                        url: pageLink,
                        txtCode: txtCode,
                        captchaID: $('#captchaID').val()
                    },
                    success: function(response) {
                        if (response.error == 0) {
                            Sexy.notice(tmpAlert.replace('{MESSAGE}', messages.sendmail_completed));
                            Sexy.display(0);
                        }
                        else if (response.error == 1) {
                            Sexy.notice(tmpAlert.replace('{MESSAGE}', messages.sendmail_error));
                        }
                        if (response.error == 2) {
                            $('#errorCode').html(messages.captcha_invalid);
                            loadCaptcha();
                        }
                    }
                });
            }
        };
        function init(options) {
            var breadcrumbWebActive = $('#main_menu .active');
            siteUrl = options.siteUrl;
            loadCaptcha = options.loadCaptcha;
            pageLink = window.location.href.replace(window.location.hash, '');
            pageTitle = document.getElementsByTagName("title")[0].innerHTML;
            cateLink = siteUrl + breadcrumbWebActive.data('url');
            cateName = breadcrumbWebActive.data('name');


            contendShareMail = contendShareMail.replace('{PAGE_LINK}', pageLink);
            contendShareMail = contendShareMail.replace(/\{PAGE_TITLE\}/g, pageTitle);
            contendShareMail = contendShareMail.replace('{CATE_LINK}', cateLink);
            contendShareMail = contendShareMail.replace(/\{CATE_NAME\}/g, cateName);
            contendShareMail = contendShareMail.replace('{SITE_URL}', siteUrl);
            contendShareMail = contendShareMail.replace(/\{SITE_TITLE\}/g, options.siteTitle);
            contendShareMail = contendShareMail.replace('{IMAGE_URL}', options.imaUrl);

        };
        init( options );

        return {
            contendShareMail: contendShareMail,
            cateLink: cateLink,
            cateName: cateName,
            sendMail: sendMail
        }
    }
    return eMail;
});
define('common', ['jquery', 'email'], function(jQuery, eMail){
    var siteUrl = '';
    var siteTitle = '';
    var imgUrl = '';
    var blockShare = '';
    var Common = function(options) {

        function loadCaptcha(width, height) {
            var divShowCaptcha = $('#showCaptcha');
            var txtCode = $('#txtCode');
            width = typeof(width) != 'undefined' ? width : 60;
            height = typeof(height) != 'undefined' ? height : 28;
            txtCode.val('');
            jQuery.ajax({
                type: 'GET',
                url: siteUrl + '/captcha/show/width/' + width + '/height/' + height,
                dataType: 'json',
                success: function(response) {
                    if (response != false) {
                        divShowCaptcha.html(response.html);
                    }
                }
            });
        };
        //check string is blank or not
        function blank (str)
        {
            return /^\s*$/.test(str || ' ');
        };
        function printContent()
        {
            blockShare.find('.button_print').on('click', function(){
                var url = window.location.href;
                url = url.replace(/\/(tin-tuc|photo|danh-gia|infographic)\//, '/print/');
                window.open(url, '_blank', 'left=300,top=0,width=550,height=600,toolbar=0,scrollbars=1,status=0');
            })
        }

        function shareMail()
        {
            //init email to send,
            var email = new eMail({siteUrl: siteUrl,siteTitle: siteTitle,imgUrl: imgUrl,loadCaptcha: loadCaptcha});
            var content = email.contendShareMail;
            blockShare.find('.button_email').on('click', function() {
                Sexy.notice(content);
                loadCaptcha();
                var refreshCaptcha = $('#refreshCaptcha');
                var btnSubmit = $('#btnSubmit');
                if (blank(email.cateLink) && blank(email.cateName)) {
                    $('.share_email_right ul li:last').remove();
                }
                refreshCaptcha.on('click', function() {
                    loadCaptcha();
                });
                btnSubmit.on('click', function() {
                    email.sendMail();
                });
            });
        }
        function menuLeft() {
            var containerMenuLeft = $('.btn_control_col_left');
            var columnLeft = $('#box_col_left');
            var page = $('#page');
            var closeMenu = $('.block_close_menu');

            //show menu left
            containerMenuLeft.click(function () {
                var pos1 = columnLeft.css('left');
                if (pos1 == '-240px') {
                    columnLeft.animate({ left: '0' }, 200, function () {
                        var height = columnLeft.find('.block_scoll_menu').height();
                        page.height(height);
                        page.addClass('hidden');
                        closeMenu.show();
                    });
                } else {
                    columnLeft.animate({left: '-240'}, 200, function () {
                        page.height('');
                        page.removeClass('hidden');
                        closeMenu.hide();
                    });
                }
            });

            //Generic swipe handler for all directions
            closeMenu.swipe({
                //Generic swipe handler for all directions
                swipeLeft: function (event, direction, distance, duration, fingerCount) {
                    columnLeft.animate({ left: '-240' }, 200, function () {
                        $('#page').height('');
                        $('#page').removeClass('hidden');
                        closeMenu.hide();
                    });
                },
                tap: function (event, target) {
                    columnLeft.animate({ left: '-240' }, 200, function () {
                        $('#page').height('');
                        $('#page').removeClass('hidden');
                        closeMenu.hide();
                    });
                },
                //Default is 75px, set to 0 for demo so any distance triggers swipe
                threshold: 0
            });
        };
        function trackVne() {
            // tracking article VNE
            $('a').bind('click', function () {
                var url = $(this).attr('href');
                vneRecommendation(url);
            });

            // get after load page
            var cur_url = window.location.href;
            if (cur_url.indexOf('?fosp_aid=') > -1) {
                cur_url = cur_url.split('?fosp_aid=');
                vneRecommendation(cur_url[0]);
            }
        }

        function vneRecommendation(url) {
            if (!url) {
                return false;
            }
            var uri = url.split('/');
            var total_uri = uri.length - 1;
            var end_uri = uri[total_uri];
            if (uri[3] == 'tin-tuc' && end_uri.indexOf('.html') > -1) {
                // get article_id
                var euri = end_uri.split('-');
                var turi = (euri.length) - 1;
                var article_id = euri[turi];
                article_id = article_id.replace('.html', '');
                // get list article_id from cookie
                var recommendation = $.cookie('vne_recommendation');
                if (recommendation == null) {
                    var article_list = article_id;
                }
                else {
                    recommendation = recommendation.replace(article_id, '');
                    recommendation = recommendation.replace('||', '|');
                    var count_recommendation = recommendation.split('|');
                    var article_list = '';
                    if (count_recommendation.length + 1 > 3) {
                        article_list += article_id;
                        $.each(count_recommendation, function (key, val) {
                            if (key < 9) {
                                article_list += '|' + val;
                            }
                        });
                    }
                    else {
                        article_list = article_id + '|' + recommendation;
                    }
                }
                // set recommendation to cookie
                var dateEx = new Date();
                dateEx.setTime(dateEx.getTime() + (3600 * 1000 * 24 * 365));
                $.cookie('vne_recommendation', article_list, { path: '/', domain: document.domain, expires: dateEx });
            }
        }
        function switchToDesktop() {
            var switchToDesktop = $('#switch-to-desktop');
            var delSwitchTo = $('#del-switch-to');
            //switch
            if (switchToDesktop[0] != undefined) {
                switchToDesktop.click(function () {
                    var dateEx = new Date();
                    dateEx.setTime(dateEx.getTime() + (60 * 60 * 1000));
                    $.cookie('device_env', 5, {path: '/', domain: document.domain, expires: dateEx});
                    window.location.reload(true);
                });
            }
            // delete
            if (delSwitchTo[0] != undefined) {
                delSwitchTo.click(function () {
                    $.cookie('device_env', '', {path: '/', domain: document.domain, expires: -1});
                    window.location.reload(true);
                });
            }
        };
        function clock() {
            var d = new Date();
            var weekday = new Array(7);
            weekday[0] = 'Chủ nhật';
            weekday[1] = 'Thứ hai';
            weekday[2] = 'Thứ ba';
            weekday[3] = 'Thứ tư';
            weekday[4] = 'Thứ năm';
            weekday[5] = 'Thứ sáu';
            weekday[6] = 'Thứ bảy';
            var n = weekday[d.getDay()];
            var now = new Date();
            var date = now.getDate();
            var month = now.getMonth() + 1;
            var hour = now.getHours();
            var minute = now.getMinutes();
            var outStr = n + ', ' + date + '/' + month + '/' + now.getFullYear() + ' | ' + (hour >= 10 ? hour : '0' + hour) + ':' + (minute >= 10 ? minute : '0' + minute) + ' GMT+7';

            $('#clockPC, #clockTablet, #clockMobile').html(outStr);
            setTimeout('common.clock()', 1000);
        };

        function init(options) {
            siteUrl = options.siteUrl;
            siteTitle = options.siteTitle;
            imgUrl = options.imgUrl;
            blockShare = options.blockShare;
        };

        init(options);

        return {
            menuLeft: menuLeft,
            trackVne: trackVne,
            shareMail: shareMail,
            clock: clock,
            switchToDesktop: switchToDesktop,
            printContent: printContent
        }
    }
    return Common;
});

define( 'social', ['facebook'], function (FB) {
    var SocialPlugin = function(options) {
        var urlFacebook = 'https://www.facebook.com/sharer/sharer.php?u=';
        var urlGoogle = 'https://plus.google.com/share?url=';
        var urlTwitter = 'https://twitter.com/intent/tweet?source=webclient&text=';
        var pageLink = '';
        var pageImage = '';
        var pageTitle = '';

        function urlEncode(str) {
            // %        note 1: This reflects PHP 5.3/6.0+ behavior
            // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
            // %        note 2: pages served as UTF-8
            // *     example 1: urlencode('Kevin van Zonneveld!');
            // *     returns 1: 'Kevin+van+Zonneveld%21'
            // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
            // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
            // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
            // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
            str = (str + '').toString();
            // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
            // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
            return encodeURIComponent(str).replace(/#!/g,'%23').replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
        };
        // setup link for social
        function socialButtonShare() {
            //facebook
            $('a.btn_facebook').click(function(e) {
                var url = urlFacebook + urlEncode(pageLink) + '&t=' + pageTitle + '&p[images][0]=' + pageImage;
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
            //google+
            $('a.btn_google').click(function(e) {
                var url = urlGoogle + urlEncode(pageLink);
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=520');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
            //twitter
            $('a.btn_twitter').click(function(e) {
                var url = urlTwitter + pageTitle + '+' + (urlEncode($(this).attr('data-url')));
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
        };
        function socialButtonLike(){
            html = '<div class="item_social social_fb hidden_320" style="overflow: hidden;"><div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"  data-href="' + pageLink + '"></div></div>';
            html += '<div class="item_social social_twitter hidden_320"><a href="https://twitter.com/share" id="twitter-share-button" class="twitter-share-button twitter-share" data-url="' + pageLink + '" data-counturl="' + pageLink + '" data-lang="en" data-count="horizontal">Tweet</a></div>';
            html += '<div class="item_social social_plus hidden_320"><div class="g-plusone" data-size="medium" data-href="' + pageLink + '"></div></div>';
            $("#social_like").append(html);
            ////// FACEBOOK //////////
            if (FB.XFBML !== undefined) {
                FB.XFBML.parse();
                $(".social_fb").css("overflow","visible");
            }
            // google
            window.___gcfg = {lang: 'vi'};
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
            // update like tweet
            if(typeof(CmtWidget) != "undefined"){
                CmtWidget.parseTwiter();
            }
        };
        function init(options) {
            pageLink = options.pageLink;
            pageImage = options.pageImage;
            pageTitle = document.getElementsByTagName("title")[0].innerHTML;
        };

        init(options);

        return {
            socialButtonShare: socialButtonShare,
            socialButtonLike: socialButtonLike
        }
    };
    return SocialPlugin;
})

define( ['jquery', 'parser', 'parser.album', 'social', 'common', 'resizeImage'], function (jQuery, Parsers, ParserAlbum, SocialPlugin, Common, ResizeImage) {
    var configs = $.extend({}, defaults, {});

    //init parser to parser video, vote, rating, like
    var parser = new Parsers({
        siteId: configs.siteId,
        autoPlay: configs.autoPlay,
        vneUrl: configs.vneUrl,
        baseUrl: configs.baseUrl
    })
    //parser vote if have
    var containerVote = 'div[data-component-type="vote"]';
    var containerLike = 'div[data-component-type="like"]';
    var containerRating = 'div[data-component-type="rating"]';
    var containerVideo = 'div[data-component-type="video"]';
    var containerAudio = 'div[data-component-type="audio"]';

    ($(containerVote).length) > 0 && parser.voteParser();
    ($(containerLike).length > 0) && parser.likeParser();
    ($(containerRating).length > 0) && parser.ratingParser();
    ($(containerVideo).length > 0) && parser.videoParser();
    ($(containerAudio).length > 0) && parser.audioParser();

    //init parser album slide show
    var parserAlbum = new ParserAlbum({
        siteId: configs.siteId,
        vneUrl: configs.vneUrl,
        baseUrl: configs.baseUrl,
        platform: configs.platform
    });
    parserAlbum.parse();

    var blockShare = $('.block_share');
    //init common for page
    var common = new Common({
        siteUrl: configs.siteUrl,
        siteTitle: configs.siteTitle,
        imgUrl: configs.imgUrl,
        blockShare: blockShare
    });
    common.printContent();
    common.shareMail();
    //resize image detail article and photo
    var resizeImage = new ResizeImage();
    var imgObj = $('.item_slide_show').find('img');
    resizeImage.resize(imgObj)
    $(window).resize(function() {
        resizeImage.delayFireOnce(1000).done(function() {
            resizeImage.resize(imgObj)
        });
    });
    //show menu left tablet + mobile
    common.menuLeft();

    //scroll top top
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 200) {
            $('#to_top').fadeIn();
        } else {
            $('#to_top').fadeOut();
        }
    });
    //tracking vne
    common.trackVne();
    //switchToDesktop
    common.switchToDesktop();
    //render social plugin
    var socialPlugin = new SocialPlugin({
        pageLink: defaults.pageLink,
        pageImage: defaults.pageImage
    });
    socialPlugin.socialButtonLike();
    socialPlugin.socialButtonShare();
});