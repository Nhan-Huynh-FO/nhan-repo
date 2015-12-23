define(function(){
    var Detail = function() {
        var delayTimer = 0;
        function socialButtonLike(){
            var article = {
                url: window.location.href.replace(window.location.hash, ''),
                title: typeof(article_title) != 'undefined' ? article_title : '',
                description: typeof(article_description) != 'undefined' ? article_description : '',
                image: typeof(article_image) != 'undefined' ? article_image : ''
            };
            html = '<div class="social_fb left"><div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"  data-href="' + article.url + '"></div></div>';
            $('#social_like').append(html);
            ////// FACEBOOK //////////
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = '//connect.facebook.net/en_US/all.js#xfbml=1';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        };
        function getBlockContent(params, callback){
            if (params.part.length > 0) {
                $.ajax({
                  method: 'GET',
                  url: '/index/block',
                  dataType: 'json',
                  data: params
                })
                .done(function(data) {
                    $.each(data, function(position, html){
                        $('#ajax_block_' + position).replaceWith(html);  
                    });
                    $('img.lazy').lazyload({
                        effect: 'fadeIn',
                        threshold: 400,
                        skip_invisible : true,
                        load: function() {
                            $('img.lazy').each(function() {
                                if ($(this).attr('src') == $(this).attr('data-original')) {
                                    $(this).removeClass('lazy');
                                }
                            });
                        }
                    });
                    CmtWidget.mix();
                    $('body').attr('data-load', 1);
                    //call callback
                    if (callback && typeof(callback) == 'function') {
                        callback();
                    }
                });
            }
            return true; 
        };
        function setBlock(pos){
            if (typeof(paramsBlock) != 'undefined') {
                paramsBlock.part.push(pos);    
            }
            return true;
        };
        function delayFireOnce(timeout) {
            var d = $.Deferred();
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function() {
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
        /**
        * resize image for detail photo
        */
        function resizeImagePhoto(container, objwidth) {
            container = container ? container : '.item_slide_show';
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
        return {
            socialButtonLike: socialButtonLike,
            getBlockContent: getBlockContent,
            setBlock: setBlock,
            delayFireOnce: delayFireOnce,
            resizeImageDetail: resizeImageDetail,
            resizeImagePhoto: resizeImagePhoto
        };
    };
    return Detail;
});