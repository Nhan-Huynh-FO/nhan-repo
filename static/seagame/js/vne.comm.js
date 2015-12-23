define('slideShow', ['jquery'], function ($) {
    var SlideShow = function (options) {
        var params = {
            title: '',
            lead: '',
            width: '',
            height: '',
            masterial: '',
            started: 0,
            sharelink: ''
        };
        var cssurl = 'http://st.f1.vnecdn.net/responsive/js/utils/slideshow/css';
        var articleid = 0;
        var windowsize = {};
        var framesize = {};
        var current = 0;
        var imgarr = '';
        var imgleng = 0;
        var expandstatus = 0;
        var loadedslide =0;
        var detectdevide = 0;

        function createhtml() {
            $('body').append('<div id="popupslide" style="display:none;"></div>');
            $('#popupslide').append('<link href="' + cssurl + '/slideshow/slide_show_gal.css" media="screen" rel="stylesheet" type="text/css"/>');
            var $html = '<div id="slide_show_gal" class="fullscreen"><div class="icon_close"><a href="javascript:;"></a></div><div class="content_gal"><div id="slide_gal" class="slide_gal sh_slide_left"><div id="galleria" class="galleria-container"></div><div class="showthumb"></div><div class="galleria-share-facebook"></div>' +
            // next and prev
            '<div class="galleria-image-nav"><div class="galleria-image-nav-right">&nbsp;</div><div class="galleria-image-nav-left">&nbsp;</div></div>' +
            // zoom in
            '</div><div class="clear"></div></div><div class="bg_opacity_slide" style="display:none;"></div><div class="clear"></div>' +
            /////////////////// SLIDE THUMB //////////
            '<div id="slide_thumbs" style=""><div id="close_thumbs"></div><div class="galleria-thumbnails-container galleria-carousel"><div class="galleria-thumbnails-list slides_container"></div></div><div class="galleria-counter">Tất cả ảnh (<span class="galleria-total">0</span>) </div></div>';
            $('#popupslide').append($html);
            //////////// touch for slide ////
            var script_2 = document.createElement('script');
            script_2.type = 'text/javascript';
            script_2.src = cssurl + '/slideshow/js/jquery.touchSwipe.min.js';
            document.body.appendChild(script_2);
            /////////// thumb ///////////
            var script_3 = document.createElement('script');
            script_3.type = 'text/javascript';
            script_3.src = cssurl + '/slideshow/js/jquery.flexslider-min.js';
            document.body.appendChild(script_3);
        };

        function run() {
            createhtml();
            getImage();
            $('#popupslide .icon_close, #popupslide .quaylai').click(function () {
                close();
            });
            // next And prev button
            $('#popupslide .galleria-image-nav-left').bind('click', function () {
                showPrev();
            });
            $('#popupslide .galleria-image-nav-right').bind('click', function () {
                showNext();
            });
            ///////////////////// show thumb /////////////
            $('#slide_show_gal').find('.showthumb').bind('click', function(){
                showThumb();
            });
            $(window).resize(function() {
                resizeBox();
            });
            $('.viewall a').click(function () {
                showThumb();
            });
            // fullscreen button
            $('#popupslide .sh_slide_left').mouseenter(function () {
                $('#popupslide .galleria-bar').show();
            });
            // share facebook
            $('#popupslide').find('.galleria-share-facebook').bind('click', function(){
                sharefacebook();
            });
            $(document).ready(function(){
                resizeBox();
                if (params.started > 0) {
                    showImage(params.started - 1);
                }
            });
        };

        function sharefacebook() {
            var width = 500, height = 400;
            var objectid = parseInt(imgarr[current].attr("data-reference-id"));
            var leftPosition, topPosition;
            //Allow for borders.
            leftPosition = (windowsize.width / 2) - ((width / 2) + 10);
            //Allow for title and status bars.
            topPosition = (windowsize.height / 2) - ((height / 2) + 50);
            var windowFeatures = 'status=no,height=' + height + ',width=' + width + ',resizable=yes,left=' + leftPosition + ',top=' + topPosition + ',screenX=' + leftPosition + ',screenY=' + topPosition + ',toolbar=no,menubar=no,scrollbars=no,location=no,directories=no';
            var u = location.href;
            if (params.sharelink) {
                u = params.sharelink + '/' + articleid + '/refer/' + objectid;
            } else {
                u = 'http://vnexpress.net/detail/photoshare/id/' + articleid + '/refer/' + objectid;
            }

            var t = document.title;
            window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(u) + '&t=' + encodeURIComponent(t), 'sharer', windowFeatures);
            return false;
        };

        // get and save and set event image to array
        function getImage() {
            var mypic = new Array();
            var i = 0;
            ////////////// get META //////////////
            var name = 'tt_article_id';
            var b = document.getElementsByTagName('meta');
            if (b.length > 0) {
                for (var c = 0; c < b.length; c++) {
                    if (b[c].getAttribute('name') != null && b[c].getAttribute('name').toLowerCase() == name.toLowerCase()) {
                        articleid = b[c].getAttribute('content');
                    }
                }
            }
            // parser caption to image
            /********** KHONG DUNG TRONG BAI PHOTO ***********
             $("#article_content .tplCaption").each(function (index) {
             var caption = $.trim($(this).find(".Image").text());
             $(this).find("img").attr({"data-component-caption": caption});
             });
             *********************************/
            var $thumhtml = '<div class="galleria-thumb-item"><div id="galleria-thumb-jcarousellite">';
            var css_path = cssurl;
            $thumhtml += '<ul class="slides">';
            $('#article_content img').each(function (index) {
                $thumhtml += '<li class="galleria-image galleria-image-' + index + '" onClick="sh_fullscreen.activeImg(this)"><img rel="' + index + '" src="' + $(this).attr("src") + '"></li>';
                // gan id vao cho img
                $(this).attr('id', 'vne_slide_image_' + index);
                mypic[index] = $(this).clone();
                $(this).css('cursor', 'url(' + css_path + '/slideshow/images/icons/zoom_cursor.png),auto');
                $(this).on('click', function () {
                    open(index);
                });
                /// apply voi icon phia tren anh
                $(this).parent().find('.btn_icon_show_slide_show').on('click', function(){
                    open(index);
                });

                i++;
            });
            $thumhtml += '</ul>';
            $thumhtml += '</div>';
            $thumhtml += '<div class="galleria-thumb-nav-left"></div><div class="galleria-thumb-nav-right"></div></div>';
            $('#popupslide .galleria-thumbnails-list').append($thumhtml);
            imgleng = i - 1;
            imgarr = mypic;
        };

        // close slideshow
        function close() {
            $('html').css({ 'overflow-x': 'auto', 'overflow-y' : 'auto' });
            if (params.masterial){
                $('#slide_show_gal').find('.footer_slideshow').remove();
            }
            $('#popupslide').hide();
            //// an thumb
            hideThumb();
            $(document).off('keydown');
        };

        /** Active anh thumb khi show **/
        function activeImg(obj) {
            $('.galleria-image').removeClass('active');
            $(obj).addClass('active');
        };

        // ham nhan load lan dau khi moi popup
        function open(index, type){
            $(document).keydown(function(event){
                if (event.keyCode == 39){
                    showNext();
                }
                if (event.keyCode == 37){
                    showPrev();
                }
                if (event.keyCode == 27){
                    close();
                }
            });
            showImage(index, type);
        };

        function showImage(index, type) {
            $('html').css({ 'overflow-x': 'hidden', 'overflow-y' : 'hidden' });
            // nut loading
            $('#popupslide').prepend('<div class="galleria_loading"><img src="' + cssurl + '/slideshow/images/icons/classic-loader.gif" alt="Loading"></div>');
            current = parseInt(index);
            // check size anh doc hay ngang
            var image = imgarr[index];
            var $src = image.attr('src');
            /*** RESIZE ANH **/
            $src = $src.replace('_660x0.', '_1200x0.');
            // them anh can load vao 
            $('#galleria').html('<img style="max-width: 100%; max-height: 100%; display: none;" src="' + $src + '" />');
            // caption anh
            $('#galleria-info').remove();
            var textinfo = image.attr("data-component-caption");
            textinfo = htmldecode(textinfo);
            var TextInfoLength = textinfo.length;
            if (TextInfoLength >= 3){
                $('#galleria').append('<div id="galleria-info" data-show="1" class="galleria-info"><div class="galleria-info-text"><div class="galleria-info-description"><div class="text-too-lenght show_description_all"><span class="galleria-count-image-thumb">' + (current + 1) + '/' + (imgleng + 1) + '  |  </span>' + textinfo + '</div></div></div></div>');
                ////////// kiem tra do cao ///////
                var fori = 1;
                var appint = setInterval(function(){
                    var devheight = $(".text-too-lenght").height();
                    //console.log(devheight + '- status: ' + self.expandstatus);
                    if (devheight > 0 || fori > 5) {
                        if (devheight > 60){
                            $('.galleria-info-description').append('<div class="too-length-dot"> ...</div>');
                            if (expandstatus == 0) {
                                $('#galleria-info').prepend('<div class="view-more-too-lenght" onclick="sh_fullscreen.showviewmore();">&nbsp;</div>');
                                showviewmore();
                            } else {
                                $('#galleria-info').prepend('<div class="view-more-too-lenght show_icon_all" onclick="sh_fullscreen.showviewmore();">&nbsp;</div>');
                                $('.too-length-dot').hide();
                            }
                        }
                        clearInterval(appint);
                    }
                    fori ++;
                }, 100);
            }
            // hien thi fullscreen
            /////////// khi co masterial /////////
            if (params.masterial && $('#slide_show_gal .footer_slideshow').size() < 1) {
                $('#slide_show_gal').append('<div class="footer_slideshow"><div class="footer_gal">' + params.masterial + '</div></div></div>');
                $('#slide_show_gal').find('.content_gal').css('bottom', 70);
            }
            $('#popupslide').show();
            // Not loaded yet, register the handler
            $('#galleria').find('img').load(function(){
                var hBoxLeft = framesize.height;
                var hheight = $(this).height();
                var marTop = hBoxLeft / 2 - hheight / 2;
                //console.log(hwidth + '-' + hheight + '-' +hBoxLeft);
                if (marTop < 0) {marTop = 0;}
                // xoa nut loading + hien thi anh
                $('.galleria_loading').remove();
                $(this).css('margin-top', marTop + 'px').fadeIn(500);
            });
            // neu la tablet
            if (detectdevide == 0){
                isTouchEnabled();
            }
            /// check nut next + prev
            if (current == 0) {
                $('.galleria-image-nav-left').hide();
            } else {
                $('.galleria-image-nav-left').show();
            }
            if (current == imgleng){
                $('.galleria-image-nav-right').hide();
            } else {
                $('.galleria-image-nav-right').show();
            }
        };
        
        function isTouchEnabled() {
            /// kiem tra thiet bi co touch ko ?
            var result = !!document.createTouch;
            if (result) {
                $(function() {
                    //Enable swiping...
                    $("#galleria").swipe({
                        //Generic swipe handler for all directions
                        swipe:function(event, direction, distance, duration, fingerCount) {
                            if (direction == 'left') {
                                showNext();
                            }
                            if (direction == 'right') {
                                showPrev();
                            }
                        },
                        tap:function(event, target) {
                            //testvar;
                            jQuery(target).trigger('click');
                        }
                    });
                });
            }
            detectdevide = 1;
        };

        // show text viewmore
        function showviewmore(){
            var status = parseInt($('#galleria-info').attr('data-show'));

            /// chua duoi ra
            if (status == 0) {
                $('#galleria-info').find('.text-too-lenght').addClass('show_description_all');
                $('#galleria-info').find('.view-more-too-lenght').addClass('show_icon_all');
                $('#galleria-info').find('.too-length-dot').hide();
                $('#galleria-info').find('.galleria-count-image-thumb').hide();
                $('#galleria-info').attr('data-show', 1);
                expandstatus = 1;
            } else {
                $('#galleria-info').find('.text-too-lenght').removeClass('show_description_all');
                $('#galleria-info').find('.view-more-too-lenght').removeClass('show_icon_all');
                $('#galleria-info').attr('data-show', 0);
                $('#galleria-info').find('.too-length-dot').show();
                $('#galleria-info').find('.galleria-count-image-thumb').show();
                expandstatus = 0;
            }
        };

        // next or prev button
        function showNext() {
            if (current < imgleng) {
                showImage(parseInt(current) + 1);
            }
    
            return true;
        };

        function showPrev() {
            if (current != 0) {
                showImage(parseInt(current) - 1);
            }
        };
        
        // hide thumb when click thumb pic
        function hideThumb() {
            $('#slide_thumbs').animate({ bottom: '-250px' }, 500).hide();
            $('.footer_gal li.viewall a').removeClass('hideslideshow');
            $('.footer_gal li.viewall a').text('Xem tất cả');
            $('#thumb-more').css('background-position', 'right top 0px');
            $('.bg_opacity_slide').hide();
            ///////// unbind slide //////
            $('#galleria-thumb-jcarousellite').find('li').removeAttr('style');
        };

        function showThumb() {
            $('#thumb-more').css('background-position', 'right top -4px');
            $('#thumb-more').css('margin-top', '6px');
            if (loadedslide == 0) {
                $('#galleria-thumb-jcarousellite').flexslider({
                    animation: 'slide',
                    itemWidth: 150,
                    itemMargin: 10,
                    animationLoop: false,
                    directionNav: false,
                    pauseOnAction: false,
                    slideshow: false,
                    controlNav: false,
                    move: 1,
                    start: function(slider) {
                        $('.galleria-thumb-nav-right').click(function(event){
                            event.preventDefault();
                            slider.flexAnimate(slider.getTarget('next'));
                        });
                        $('.galleria-thumb-nav-left').click(function(event){
                            event.preventDefault();
                            slider.flexAnimate(slider.getTarget('prev'));
                        });
                        var start = 0;
                        $('.showthumb').bind('click', function(){
                            var endimg = slider.last;
                            if (current > endimg) {
                                start = endimg;
                            } else {
                                start = current;
                            }
                            slider.flexAnimate(start, true);
                        });
                        slider.flexAnimate(start, true);
                    }
                });
                $('#popupslide .galleria-total').text(imgleng + 1);
                $('#close_thumbs').bind('click', function () {
                    hideThumb();
                });
                $('#popupslide .galleria-image').find('img').bind('click', function () {
                    showImage($(this).attr('rel'));
                    hideThumb();
                    $('.bg_opacity_slide').hide();
                });
                loadedslide = 1;
            }
            ////////////////////////////////////
            $('#slide_thumbs').css('bottom', '-250px');
            $('#slide_thumbs').show();
            var bottomspace = (params.masterial) ? 70 : 20;
            $('#slide_thumbs').animate({ bottom: bottomspace }, 500);
            $('.bg_opacity_slide').show();
            $('#galleria-thumb-jcarousellite').find('.galleria-image').removeClass('active');
            $('#galleria-thumb-jcarousellite').find('.galleria-image-' + current).addClass('active');
        };

        function resizeBox(){
            var documentWidth = $(window).width(); //retrieve current window width
            var documentHeight = $(window).height(); //retrieve current window height
            var framewidth = documentWidth - 80;
            var frameheight = (params.masterial) ? (documentHeight - 100) : (documentHeight - 60);
            windowsize = {width: documentWidth, height: documentHeight};
            framesize = {width: framewidth, height: frameheight};
            /////////// run resize khoang cach ///////////
            calculator();
        };

        function calculator(){
            $('#popupslide').find('.galleria-image-nav').css('top', (framesize.height - 50) / 2);
            $('#galleria').height(framesize.height);
        };
        
        function htmldecode(str) {
            return $('<div/>').html(str).text();
        };
    
        function init(options) {
            params = $.extend(params, options || {});
        };
        
        init(options);

        return {
            run: run
        };
    };
    return SlideShow;
});

define('resizeImage', ['jquery'], function ($) {
    var ResizeImage = function () {
        var delayTimer = 0;

        function delayFireOnce(timeout) {
            var d = $.Deferred();
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function () {
                d.resolve();
            }, timeout);

            return d.promise();
        };

        /**
         * resize image for detail article
         */
        function resizeImageDetail(container, objwidth) {
            var tableObj = container ? $(container).find('table[class!="tbl_fptplay"]') : $('.fck_detail table[class!="tbl_fptplay"]');
            var width_global = 0;
            var pwidth = objwidth ? $(objwidth).width() : $('.fck_detail').width();

            var checkalign = function (obj, scale) {
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

            var fcondition1 = function (obj) {
                var img = new Image();
                img.onload = function () {
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
                    $(obj).attr({'data-width': iwidth, 'data-pwidth': pwidth});
                };
                img.src = $(obj).attr('src');
            };

            var fcondition2 = function (obj) {
                var img = new Image();
                img.onload = function () {
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
                    $(obj).attr({'data-width': iwidth, 'data-pwidth': pwidth});
                };
                img.src = $(obj).attr('src');
            };

            var fcondition3 = function (obj) {
                var img = new Image();
                img.onload = function () {
                    var iwidth = img.width;
                    $(obj).attr({'data-width': iwidth, 'data-pwidth': pwidth});
                    if (iwidth > pwidth) {
                        fcondition31(obj);
                    }
                };
                img.src = $(obj).attr('src');
            };

            var fcondition31 = function (obj) {
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

            var fcondition4 = function (obj, count_td) {
                var img = new Image();
                img.onload = function () {
                    var iwidth = img.width;
                    var td_width = (pwidth / count_td);
                    var scale = (iwidth / pwidth) * 100;
                    if (iwidth >= td_width) {
                        $(obj)
                            .width('100%')
                            .parents('td')
                            .width(scale + '%');
                    }
                    $(obj).attr({'data-width': iwidth, 'data-pwidth': pwidth});
                };
                img.src = $(obj).attr('src');
            };

            tableObj.each(function (i, o) {
                if ($(o).find('div[data-component="true"]').size() < 1 && $(o).parents('div[data-component="true"]').size() < 1) {
                    $(o)
                        .removeAttr('width')
                        .find('img')
                        .css('height', '');
                    var condition = $(o).find('tr').size() > 1;
                    if (condition) {
                        var count_img = count_td = 0;
                        var condition1 = condition2 = false;


                        $(o).find('tr').each(function () {
                            count_td = $(this).find('td').size();
                            $(this).find('td').each(function () {
                                count_img += $(this).find('img').size();
                                if ($(this).find('img').size() > 1) {
                                    condition2 = true;
                                }
                            });
                        });

                        condition1 = count_img < 2;
                        if (condition1) {
                            $(o).find('img').each(function () {
                                fcondition1(this);
                            });
                        } else {
                            if (condition2) {
                                $(o).find('img').each(function () {
                                    fcondition2(this);
                                });
                            } else {
                                $(o).find('img').each(function () {
                                    fcondition4(this, count_td);
                                });
                            }
                        }
                    } else {
                        $(o).find('img').each(function () {
                            fcondition3(this);
                        });
                    }
                }
            });
        };

        function resizeImagePhoto(container, objwidth) {
            container = container ? container : '.item_slide_show';
            var imgObj = $(container).find('img');
            var pwidth = objwidth ? $(objwidth).width() : $('#article_content').width();

            imgObj.each(function (i, o) {
                //delete height of image
                $(o).css('height', '');
                var img = new Image();
                img.onload = function () {
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
        };
        
        return {
            resizeImage: resizeImageDetail,
            resizeSocial: resizeImagePhoto,
            delayFireOnce: delayFireOnce,
            resize: resize
        };
    };
    return ResizeImage;
});

define('email', ['jquery'], function (jQuery) {
    var eMail = function (options) {
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
        var contentShareMail = '<div id="login-vne5" class="login-form">'
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
        function blank(str) {
            return /^\s*$/.test(str || ' ');
        };
        
        function sendMail() {
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
                    success: function (response) {
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


            contentShareMail = contentShareMail.replace('{PAGE_LINK}', pageLink);
            contentShareMail = contentShareMail.replace(/\{PAGE_TITLE\}/g, pageTitle);
            contentShareMail = contentShareMail.replace('{CATE_LINK}', cateLink);
            contentShareMail = contentShareMail.replace(/\{CATE_NAME\}/g, cateName);
            contentShareMail = contentShareMail.replace('{SITE_URL}', siteUrl);
            contentShareMail = contentShareMail.replace(/\{SITE_TITLE\}/g, options.siteTitle);
            contentShareMail = contentShareMail.replace('{IMAGE_URL}', options.imgUrl);
        };
        
        init(options);

        return {
            contentShareMail: contentShareMail,
            cateLink: cateLink,
            cateName: cateName,
            sendMail: sendMail
        };
    };
    return eMail;
});

define('common', ['jquery', 'email'], function (jQuery, eMail) {
    var siteUrl = '';
    var siteTitle = '';
    var imgUrl = '';
    var blockShare = '';
    
    var Common = function (options) {
        function loadCaptcha(width, height) {
            var divShowCaptcha = $('#showCaptcha');
            var txtCode = $('#txtCode');
            width = typeof (width) != 'undefined' ? width : 60;
            height = typeof (height) != 'undefined' ? height : 28;
            txtCode.val('');
            jQuery.ajax({
                type: 'GET',
                url: siteUrl + '/captcha/show/width/' + width + '/height/' + height,
                dataType: 'json',
                success: function (response) {
                    if (response != false) {
                        divShowCaptcha.html(response.html);
                    }
                }
            });
        };
        
        //check string is blank or not
        function blank(str) {
            return /^\s*$/.test(str || ' ');
        };
        
        function printContent() {
            blockShare.find('.button_print').on('click', function () {
                var url = window.location.href;
                url = url.replace(/\/(tin-tuc|photo|danh-gia|infographic)\//, '/print/');
                window.open(url, '_blank', 'left=300,top=0,width=550,height=600,toolbar=0,scrollbars=1,status=0');
            });
        };

        function shareMail() {
            //init email to send,
            var email = new eMail({siteUrl: siteUrl, siteTitle: siteTitle, imgUrl: imgUrl, loadCaptcha: loadCaptcha});
            var content = email.contentShareMail;
            blockShare.find('.button_email').on('click', function () {
                Sexy.notice(content);
                loadCaptcha();
                var refreshCaptcha = $('#refreshCaptcha');
                var btnSubmit = $('#btnSubmit');
                if (blank(email.cateLink) && blank(email.cateName)) {
                    $('.share_email_right ul li:last').remove();
                }
                refreshCaptcha.on('click', function () {
                    loadCaptcha();
                });
                btnSubmit.on('click', function () {
                    email.sendMail();
                });
            });
        };
        
        function menuLeft() {
            var containerMenuLeft = $('.btn_control_col_left');
            var columnLeft = $('#box_col_left');
            var page = $('#page');
            var dropDownMenu = $('.img_arrow_timer');
            var breadcrumbMenu = $('.block_show_breakumb');
            //show menu left
            containerMenuLeft.click(function () {
                var pos1 = columnLeft.css('left');
                if (pos1 == '-240px') {
                    columnLeft.animate({left: '0'}, 200, function () {
                        var height = columnLeft.find('.block_scoll_menu').height();
                        page.height(height);
                        page.addClass('hidden');
                    });
                } else {
                    columnLeft.animate({left: '-240'}, 200, function () {
                        page.height('');
                        page.removeClass('hidden');
                    });
                }
            });

            $('html').click(function(){
                var pos1 = columnLeft.css('left');
                if (pos1 != '-240px') {
                    columnLeft.animate({left: '-240'}, 200, function() {
                        page.height('');
                        page.removeClass('hidden');
                    });
                }
                if (breadcrumbMenu.is(':visible')) {
                    dropDownMenu.removeClass('active');
                    breadcrumbMenu.hide();
                }
            });
            //show drop down menu
            dropDownMenu.click(function () {
                if (breadcrumbMenu.css('display') == 'none') {
                    $(this).addClass('active');
                    breadcrumbMenu.fadeIn(200);
                } else {
                    $(this).removeClass('active');
                    breadcrumbMenu.hide();
                }
                return false;
            });
        };

        function loadMore() {
            var $target = $('.more_btn a');
            if ($target.size() < 1) {
                return;
            }
            //set is not loading
            $target.attr('data-status', 'unload');
            var url = $target.attr('href');
            var container = $target.attr('data-container');
            var params = $.parseJSON($target.attr('data-params'));

            if (params.offset >= params.total) {
                $target.parent().remove();
                return false;
            }
            
            $target.click(function (e) {
                e.preventDefault();
                var status = $target.attr('data-status');
                if (status == 'unload') {
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        data: params,
                        beforeSend: function () {
                            $target.attr('data-status', 'loading').html('Đang tải...');
                        },
                        success: function (response) {
                            if (response.error != 1) {
                                $(container).children('li:last').removeClass('end');
                                $(container).append(response.html);
                                CmtWidget.mix();

                                if (response.total >= params.limit) {
                                    $target.attr('data-status', 'unload').html('Xem tiếp');
                                    delete response.html;
                                    delete response.error;
                                    delete response.message;
                                    delete response.total;
                                    params = $.extend(params, response);
                                } else {
                                    $target.parent().remove();
                                }
                                $(container).children('li:last').addClass('end');
                            } else {
                                $target.parent().remove();
                            }
                        },
                        error: function () {
                            $target.attr('data-status', 'unload').html('Xem tiếp');
                        }
                    });
                }
                return false;
            });
        };
        
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
            shareMail: shareMail,
            loadMore: loadMore,
            clock: clock,
            switchToDesktop: switchToDesktop,
            printContent: printContent
        };
    };
    return Common;
});

define('social', ['facebook'], function (FB) {
    var SocialPlugin = function (options) {
        var urlFacebook = 'https://www.facebook.com/sharer/sharer.php?u=';
        var urlGoogle = 'https://plus.google.com/share?url=';
        var urlTwitter = 'https://twitter.com/intent/tweet?source=webclient&text=';
        var pageLink = '';
        var pageImage = '';
        var pageTitle = '';
        var platform = 4;

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
            return encodeURIComponent(str).replace(/#!/g, '%23').replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
        };
        
        // setup link for social
        function socialButtonShare() {
            //facebook
            $('a.btn_facebook').click(function (e) {
                var url = urlFacebook + urlEncode(pageLink) + '&t=' + pageTitle + '&p[images][0]=' + pageImage;
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
            //google+
            $('a.btn_google').click(function (e) {
                var url = urlGoogle + urlEncode(pageLink);
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=520');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
            //twitter
            $('a.btn_twitter').click(function (e) {
                var url = urlTwitter + pageTitle + '+' + (urlEncode($(this).attr('data-url')));
                var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
                if (window.focus) {
                    newwindow.focus();
                }
                e.preventDefault();
            });
        };
        
        function socialButtonLike() {
            if (platform != 1) {
                var html = '<div class="item_social social_fb hidden_320" style="overflow: hidden;"><div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"  data-href="' + pageLink + '"></div></div>';
                html += '<div class="item_social social_twitter hidden_320"><a href="https://twitter.com/share" id="twitter-share-button" class="twitter-share-button twitter-share" data-url="' + pageLink + '" data-counturl="' + pageLink + '" data-lang="en" data-count="horizontal">Tweet</a></div>';
                html += '<div class="item_social social_plus hidden_320"><div class="g-plusone" data-size="medium" data-href="' + pageLink + '"></div></div>';
            } else {
                var html = '<div class="social_fb left"><div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"  data-href="' + pageLink + '"></div></div>';
            }
            $('#social_like').append(html);
            ////// FACEBOOK //////////
            if (FB.XFBML !== undefined) {
                FB.XFBML.parse();
                $(".social_fb").css("overflow", "visible");
            }
            if (platform != 1) {
                // google
                window.___gcfg = {lang: 'vi'};
                (function () {
                    var po = document.createElement('script');
                    po.type = 'text/javascript';
                    po.async = true;
                    po.src = 'https://apis.google.com/js/plusone.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(po, s);
                })();
                // update like tweet
                if (typeof (CmtWidget) != 'undefined') {
                    CmtWidget.parseTwiter();
                }
            }
        };
        
        function init(options) {
            platform = options.platform;
            pageLink = options.pageLink;
            pageImage = options.pageImage;
            pageTitle = document.getElementsByTagName('title')[0].innerHTML;
        };

        init(options);

        return {
            socialButtonShare: socialButtonShare,
            socialButtonLike: socialButtonLike
        };
    };
    return SocialPlugin;
});

define(['jquery', 'parser', 'parser.album', 'social', 'common', 'resizeImage', 'slideShow', 'carousel'], function (jQuery, Parsers, ParserAlbum, SocialPlugin, Common, ResizeImage, SlideShow) {
    var configs = $.extend({}, defaults, {});

    //init parser to parser video, vote, rating, like
    var parser = new Parsers({
        siteId: configs.siteId,
        autoPlay: configs.autoPlay,
        vneUrl: configs.vneUrl,
        baseUrl: configs.baseUrl
    });
    
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
    if (configs.platform != 1) {
        common.printContent();
        common.shareMail();
    }
    //resize image detail article and photo
    var resizeImage = new ResizeImage();
    var imgObj = $('.item_slide_show').find('img');
    resizeImage.resize(imgObj);
    $(window).resize(function () {
        resizeImage.delayFireOnce(1000).done(function () {
            resizeImage.resize(imgObj);
        });
    });
    
    //slideshow
    if (imgObj.size() > 0) {
        if (configs.platform != 1) {
            var slideShow = new SlideShow(configs.paramsSlideshow);
            slideShow.run();
        } else {
            slideshow._init();
        }
    }
    
    //show menu left tablet + mobile
    common.menuLeft();
    common.loadMore();
    //scroll top top
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 200) {
            $('#to_top').fadeIn();
        } else {
            $('#to_top').fadeOut();
        }
    });
    //switchToDesktop
    common.switchToDesktop();

    //parser video carousel for home seagames
    var listVideo = $('.list_video');
    var owlVideo = listVideo.find('#owl-demo');
    owlVideo.owlCarousel({
        autoPlay: 4000,
        items: 4,
        itemsDesktop: [1199, 3],
        itemsTablet: [600, 2], //2 items between 600 and 0
        itemsDesktopSmall: [900, 3], // betweem 900px and 601px
        itemsMobile: [479, 2],
        pagination: false
    });
    $('#block_video').show();
    listVideo.find('.next_slider').show().click(function () {
        owlVideo.trigger('owl.next');
    });
    listVideo.find('.prev_slider').show().click(function () {
        owlVideo.trigger('owl.prev');
    });
    
    //init scroll
    if ($('.category_list_hc_vn .scroll-pane').size() > 0) {
        $('.category_list_hc_vn .scroll-pane').niceScroll('.category_list_hc_vn .content_scoller',{ touchbehavior: false, autohidemode: false });
    }

    //render social plugin
    var socialPlugin = new SocialPlugin({
        platform: configs.platform,
        pageLink: configs.pageLink,
        pageImage: configs.pageImage
    });
    if (configs.platform != 1) {
        socialPlugin.socialButtonShare();
    }
    socialPlugin.socialButtonLike();
	
    //call detect flash in page video
    if ($('#block_video_html5').size() > 0) {
        try {
            if (FlashDetect.installed == false) {
                $.getJSON(configs.siteUrl + '/video/vne-info/?callback=?', {
                    id: configs.pageId,
                    typeHTML: 'html5'
                }, function (response) {
                    if (response.error == 0) {
                        $('#videoplayer').hide();
                        var w = $('.embed-container').width();
                        var h = (w * 56.25) / 100;
                        var object = '<video width="' + w + '" height="' + h + '" controls autoplay preload="none">'
                                + '<source src="' + response.media_url + '" type="video/mp4" />'
                                + '</video>';
                        $('#block_video_html5').html(object).show();
                    }
                });
            }
        } catch (e) {
        }
        $(window).resize(function() {
            var w = $('.embed-container').width();
            var h = (w * 56.25) / 100;
            $('video').attr('width', w).attr('height', h);
        });
    }
});