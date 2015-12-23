define(function () {
    var Category = function () {
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
        function stripTags(input, allowed) {
            allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
            return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        };
        function scrollToTop() {
            window.scrollTo(0, 0);
            return false;
        };
        function setClassEnd(container) {
            if (!container) {
                container = '.make_class_end';
            }
            $.each($(container), function (i, o) {
                $(o).children(':last').addClass('end');
            });
        };
        function noticeAlert(message) {
            var html_message = '<div id="login-vne4" class="login-form">'
                + '<div class="ttOline">Thông báo</div>'
                + '<div class="content_ligbox">'
                + '<div class="complete-form">'
                + '<div class="content_form">' + message + '</div>'
                + '</div>'
                + '</div>'
                + '<div class="close-lb"></div>'
                + '<div class="clear">&nbsp;</div>'
                + '</div>';
            Sexy.notice(html_message);
        };
        function initCommon() {
            //show menu left
            $('.btn_control_col_left').click(function () {
                var pos1 = $('#box_col_left').css('left');
                if (pos1 == '-240px') {
                    $('#box_col_left').animate({ left: '0' }, 200, function () {
                        var heightColleft = $('#box_col_left .block_scoll_menu').height();
                        $('#page').height(heightColleft);
                        $('#page').addClass('hidden');
                        $('.block_close_menu').show();
                    });
                } else {
                    $('#box_col_left').animate({left: '-240'}, 200, function () {
                        $('#page').height('');
                        $('#page').removeClass('hidden');
                        $('.block_close_menu').hide();
                    });
                }
            });

            //Generic swipe handler for all directions
            $('.block_close_menu').swipe({
                //Generic swipe handler for all directions
                swipeLeft: function (event, direction, distance, duration, fingerCount) {
                    $('#box_col_left').animate({ left: '-240' }, 200, function () {
                        var heightColleft = $('#box_col_left .block_scoll_menu').height();
                        $('#page').height('');
                        $('#page').removeClass('hidden');
                        $('.block_close_menu').hide();
                    });
                },
                tap: function (event, target) {
                    $('#box_col_left').animate({ left: '-240' }, 200, function () {
                        var heightColleft = $('#box_col_left .block_scoll_menu').height();
                        $('#page').height('');
                        $('#page').removeClass('hidden');
                        $('.block_close_menu').hide();
                    });
                },
                //Default is 75px, set to 0 for demo so any distance triggers swipe
                threshold: 0
            });

            //show dropdown menu
            $('.img_arrow_timer').click(function () {
                if ($('.block_show_breakumb').css('display') == 'none') {
                    $(this).addClass('active');
                    $(this).parent().next().fadeIn(200);
                } else {
                    $(this).removeClass('active');
                    $('.block_show_breakumb').hide();
                }

                return false;
            });

            /*Add script menu cuoi*/
            $('#menu_web').find('.item_have_more_menu').click(function () {
                if ($(this).find('.sub_main_menu_item').css('display') == 'none') {
                    $(this).find('.sub_main_menu_item').show();
                    $(this).addClass('active');
                } else {
                    $(this).find('.sub_main_menu_item').hide();
                    $(this).removeClass('active');
                }
            });

            //auto close dropdown menu when click other places
            $(document).click(function () {
                if ($('.block_show_breakumb').is(':visible')) {
                    $('.img_arrow_timer').removeClass('active');
                    $('.block_show_breakumb').hide();
                }
            });

            //scroll top top
            $(function () {
                var expand = false;
                var position = 0;
                var header_h = 0;
                var iden1;
                document.addEventListener('touchmove', touchMove, false);
                document.addEventListener('scroll', Scroll, false);

                function touchMove() {
                    if (expand == 0) {
                        var a = $(window).scrollTop();
                        if (a > position) {
                            if (a > header_h + 50) {
                                $('#to_top').fadeOut();
                                iden1 = 0;
                            }
                        } else {
                            if (position > a + 5) {
                                if ($(window).scrollTop() > header_h) {
                                    if (iden1 == 0) {
                                        $('#to_top').stop(true, true).fadeIn();
                                        iden1 = 1;
                                    }
                                }
                            }
                        }
                        position = a;
                    }
                };

                function Scroll() {
                    if (expand == 0) {
                        var a = $(window).scrollTop();
                        if (a > position) {
                            if (a > header_h + 50) {
                                $('#to_top').fadeOut();
                                iden1 = 0;
                            }
                        } else {
                            if (position > a + 5) {
                                if ($(window).scrollTop() > header_h) {
                                    if (iden1 == 0) {
                                        $('#to_top').stop(true, true).fadeIn();
                                        iden1 = 1;
                                    }
                                }
                            }
                        }
                        position = a;
                    }
                    if (a == header_h) {
                        $('#to_top').fadeOut();
                    }
                };
            });

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

            // ipad webview
            isIpadWebViewApp();
        };
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
            return true;
        };
        function isIpadWebViewApp() {
            var cur_url = window.location.href;
            if (typeof(cur_url) != 'undefined' && cur_url.indexOf('view=app') > -1 && (navigator.userAgent.match(/iPad/i) != null)) {
                $('#detail_page, , #pvtt_page, #detail_infographic, , #detail_page_live').css({
                    padding: '0 20% 0 15%',
                    width: '65%'
                });
            }
        };
        function switchToDesktop() {
            /**
             * switch
             */
            if ($('#switch-to-desktop')[0] != undefined) {
                $('#switch-to-desktop').click(function () {
                    var dateEx = new Date();
                    dateEx.setTime(dateEx.getTime() + (60 * 60 * 1000));
                    $.cookie('device_env', 5, {path: '/', domain: document.domain, expires: dateEx});
                    window.location.reload(true);
                });
            }

            /**
             * delete
             */
            if ($('#del-switch-to')[0] != undefined) {
                $('#del-switch-to').click(function () {
                    $.cookie('device_env', '', {path: '/', domain: document.domain, expires: -1});
                    window.location.reload(true);
                });
            }
        };
        function initLoadImage(imgObj, options) {
            if (typeof(imgObj) == 'object') {
                options = imgObj;
                imgObj = 'img.lazy';
            }
            if (!imgObj) {
                imgObj = 'img.lazy';
            }
            var settings = {
                event: 'scroll',
                effect: 'fadeIn',
                placeholder: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAADCAYAAABbNsX4AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NTkzNDQ0QjZGMjNDMTFFNDhCRTZEQjA5OTE4OTkxNDgiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NTkzNDQ0QjdGMjNDMTFFNDhCRTZEQjA5OTE4OTkxNDgiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo1OTM0NDRCNEYyM0MxMUU0OEJFNkRCMDk5MTg5OTE0OCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo1OTM0NDRCNUYyM0MxMUU0OEJFNkRCMDk5MTg5OTE0OCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PrN4gbgAAAAkSURBVHjaYnz6+PFTBgYGXgYE+MwCJKQYUAEvSPAZukqAAAMAGokGp8ui1YwAAAAASUVORK5CYII=',
                threshold: 400,
                skip_invisible: true,
                load: function () {
                    $(imgObj).each(function () {
                        if ($(this).attr('src') == $(this).attr('data-original')) {
                            $(this).removeClass('lazy');
                        }
                    });
                }
            };
            settings = $.extend(settings, options || {});
            $(imgObj).lazyload(settings);
        };
        function partialVne(params, callback) {
            if (params.part.length > 0) {
                $.ajax({
                    method: 'GET',
                    url: '/index/block',
                    dataType: 'json',
                    data: params
                }).done(function (data) {
                    $.each(data, function (position, html) {
                        $('#ajax_block_' + position).replaceWith(html);
                    });
                    initLoadImage();
                    CmtWidget.mix();
                    $('body').attr('data-load', 1);
                    //call callback
                    if (callback && typeof(callback) == 'function') {
                        callback();
                    }
                });
            }
        };
        function initLinkSlideshow() {

        };
        function parseLinkNewsLead() {
            $('div[data-mobile-href]').each(function () {
                var href = $(this).attr('data-mobile-href');
                if (href) {
                    $(this)
                        .wrapInner($('<a href="' + href + '"></a>'))
                        .removeAttr('data-mobile-href');
                    initLinkSlideshow();
                }
            });
        };
        function parserAdsFullScreen() {
            var detail = $('.fck_detail').children();
            if (detail && typeof ads_full_screen != 'undefined') {
                var divF = $('<div id="divfirst"></div>');
                var divM = $('<div id="admbackground"></div>');
                var divE = $('<div id="divend"></div>');
                for (var i = 0; i < detail.length; i++) {
                    var tmp = $(detail[i]).clone();
                    if (i < (detail.length) / 2) {
                        divF.append(tmp);
                    } else {
                        divE.append(tmp);
                    }
                }
                $('.fck_detail').html('').append(divF).append(divM).append(divE);
            }
        };
        return {
            messages: messages,
            clock: clock,
            stripTags: stripTags,
            scrollToTop: scrollToTop,
            setClassEnd: setClassEnd,
            noticeAlert: noticeAlert,
            initCommon: initCommon,
            vneRecommendation: vneRecommendation,
            isIpadWebViewApp: isIpadWebViewApp,
            switchToDesktop: switchToDesktop,
            initLoadImage: initLoadImage,
            partialVne: partialVne,
            initLinkSlideshow: initLinkSlideshow,
            parseLinkNewsLead: parseLinkNewsLead,
            parserAdsFullScreen: parserAdsFullScreen
        };
    };
    return Category;
});