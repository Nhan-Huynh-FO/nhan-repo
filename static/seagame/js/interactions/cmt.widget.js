var CmtWidget = (function() {
    // Localize jQuery variable
    var $,jQuery, server = interactions_url || 'http://interactions.vnexpress.net', siteid=1000, showComment = 0;

    if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.7.1') {
        var script_tag = document.createElement('script'), done = false, head;
        !head && (head = document.head || document.getElementsByTagName('head')[0] || document.documentElement);
        script_tag.setAttribute("type","text/javascript");
        script_tag.setAttribute("src","http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js");
        if (script_tag.readyState) {
            script_tag.onreadystatechange = function () { // For old versions of IE
                if (!done && (this.readyState == 'complete' || this.readyState == 'loaded')) {
                    init();
                }
            };
        } else {
            script_tag.onload = init;
        }

        //Use insertBefore instead of appendChild  to circumvent an IE6 bug.
        head.insertBefore(script_tag, head.firstChild);

    } else {
        $ = jQuery = window.jQuery;
        // Init onload
        intCommentWidget();
    }

    function init() {
        $ = jQuery  = window.jQuery.noConflict(true);
        intCommentWidget();
    }

    function parse_cmt_number(){
        //For total comment
        var sp = ($('span.txt_num_comment')), n = sp.length;
        //For total media(luot nghe)
        var sv = ($('span.view_num')), nv = sv.length;

        if('undefined' != typeof(SHOW_TOTAL_COMMENT)){
            showComment = parseInt(SHOW_TOTAL_COMMENT)
        }else{
            if('undefined' != typeof(SITE_ID)){
                siteid = parseInt(SITE_ID);
            }else if('undefined' != typeof(PAGE_FOLDER)){
                siteid = parseInt(PAGE_FOLDER);
            }else{
                siteid = 1000000;
            }

        }
        if(siteid == 1002774){
            showComment = 1;
        }
        var objectid, objecttype, type;
        var strParam = [];
        strParam['cid'] = [];
        strParam['vid'] = [];
        strParam['likeid'] = [];
        strParam['rid'] = [];
        strParam['mid'] = [];
        strParam['ratingobj'] = [];
        strParam['listenid'] = [];
        strParam['ratingdl'] = [];
        strParam['ratingsh'] = [];
        strParam['topcmt'] = [];

        //Total comment
        if(n){
            sp.each(function(){
                objectid = Number($(this).attr('data-objectid'));
                objecttype = Number($(this).attr('data-objecttype'));
                type = ($(this).attr('data-type'));
                if(objectid>0 && objecttype>0){
                    strParam['cid'].push(objectid + '-' + objecttype);
                    $(this).addClass('widget-'+ type +'-'+ objectid + '-' + objecttype)
                        .removeAttr('data-type')
                        .removeAttr('data-objecttype')
                        .removeAttr('data-objectid')
                    ;
                }
            });
        }

        //Total media
        if(nv){
            sv.each(function(){
                objectid = Number($(this).attr('data-objectid'));
                objecttype = Number($(this).attr('data-objecttype'));
                type = ($(this).attr('data-type'));
                if(objectid>0 && objecttype>0){
                    strParam['vid'].push(objectid + '-' + objecttype);
                    $(this).addClass('widget-'+ type +'-'+ objectid + '-' + objecttype)
                        .removeAttr('data-type')
                        .removeAttr('data-objecttype')
                        .removeAttr('data-objectid')
                    ;
                }
            });
        }

        /* Like widget VNE*/
        var likeSite;
        $.each($('div[data-component-type="like"]'), function (index, value){
            var divLike = $(value);
            likeSite = divLike.attr('data-component-siteid') || 0;
            var objectid = divLike.attr('data-component-objectid');
            var object_type = divLike.attr('data-objecttype');
            var key = likeSite + '-' + objectid;
            if(divLike.find('a[id="vne-like-anchor-' + key + '"').length == 0){
                // Check total present at local
                if(defaults.platform!=1){
                    divLike.html('<a class="btn_quantam" id="vne-like-anchor-' + key + '" href="#"></a><div class="number_count"><span id="like-total-' + key + '"></span></div>');
                }else{//show with mobile
                    divLike.html('<a class="btn_like" id="vne-like-anchor-' + key + '" href="#"><span>Quan tâm</span></a><div class="numb_like"><span id="like-total-' + key + '"></span></div>');
                }
                divLike.find('#vne-like-anchor-' + key).bind('click', function(event){
                    event.preventDefault();
                    vneLike.addLike($(this));
                    return false;
                });
            }
            divLike.hide();
            strParam['likeid'].push(objectid+'-'+object_type);
        });

        $.each($('div[data-component-type="ratingwd"]'), function (index, value){
            var ratingid = $(this).attr('class').replace('widget-rating-', '');
            if(ratingid){
                strParam['rid'].push(ratingid + '-' + ($(this).attr('data-imdb') || 0) + '-' + $(this).attr('display-stype') || 1);
            }
        });

        $.each($('div[data-component-type="likematch"]'), function (index, value){
            strParam['mid'].push($(this).attr('data-component-matchid'));
        });

        /* End Like widget*/
        $.each($('div[data-component-type="rating-obj-wd"]'), function (index, value){
            strParam['ratingobj'].push($(this).attr('rel')+ '-70');
        });

        /*Danh gia du lich*/
        $.each($('div[data-component-type="rating-dl-ks"]'), function (index, value){
            strParam['ratingdl'].push($(this).attr('rel')+ '-70');
        });
        /*Danh gia du so hoa*/
        $.each($('div[data-component-type="rating-sh-wd"]'), function (index, value){
            strParam['ratingsh'].push($(this).attr('rel')+ '-55');
        });
        $.each($('span[data-component-type="listen-wd"]'), function (index, value){
            var that = $(this);
            strParam['listenid'].push(that.attr('rel'));
            that.removeAttr('data-component-type');
        });
        $.each($('div[data-component-type="top-cmt"]'), function (index, value){
            var that = $(this);
            strParam['topcmt'].push(that.attr('rel')+ '-' + that.attr('data-type'));
        });

        var url ='';
        var shareTwiter = $('a.twitter-share');
        if(shareTwiter.length){
            url = 'bitly_url=' + ( shareTwiter.attr('data-url') || encodeURIComponent(window.location.href)) + '&';
        }
        //lastestcmt
        var lastestCmt = $('[data-component-type="lastest-cmt"]');
        if(lastestCmt.length && !lastestCmt.hasClass('render')){
            lastestCmt.addClass('render');
            url += 'lastestcmt=' + lastestCmt.data('cate') + '-' + lastestCmt.data('objecttype') + '&';
        }
        for (x in strParam)
        {
            // Fix bug on IE (x != 'indexOf')
            if(strParam[x].length && x != 'indexOf' && typeof(strParam[x]) == 'object'){
                url += x + '=' + strParam[x].join(';')+'&';
            }
        }
        var predict = $('.predict-statistic');
        if(predict.length){
            url += 'predict-st=' + predict.data('rel');
        }
        if(url!= ''){
            var e=window.document,k=e.getElementsByTagName("head")[0]||e.body;
            var m=e.createElement("script");
            m.src=server +'/widget/index/?s=' + likeSite + '&' +url;
            m.async=!0;
            m.charset="UTF-8";
            k.appendChild(m);
        }
        return false
    }

    function intCommentWidget() {
        jQuery(document).ready(function($) {
            parse_cmt_number();
        });
    }

    function tracking(object_id, object_type){
        $.getJSON(server + '/index/tracklisten?callback=?',{id: object_id, type:object_type}, function(data,status,xhr){
            //console.log(data);
        });
    }
    function number_format (number, decimals, dec_point, thousands_sep) {
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
    }
    return {
        mix: function(){
            parse_cmt_number();
        },
        parse: function(id,value){
            $('.'+id).each(function(){
                var self = $(this);
                var parent_el = self.parent();
                //self.html(number_format(value,0,'.',','));
                //self.append(number_format(value,0,'.',','));
                var label = self.find('label.total');
                if (!label.length) {
                    var label = $('<label class="total"></label>').appendTo(self);
                }
                label.html(number_format(value, 0, '.', ','));

                //Slide show mobile
                if (showComment === 1 && value!==0) {
                    //show data
                    parent_el.show();
                }
                else if (value == 0 || (siteid == 1002835 && value < 30) || (value < 20)) { // Ko hien icon comment neu tong so comment nho hon 30 cho ngoisao, 20 ione
                    try {
                        //hide comment 
                        parent_el.hide();
                        //parent_el.find(self).hide();//hiden span
                    } catch (e) {
                    }
                } else {
                    //show data
                    parent_el.show();
                }
            });
        },
        parseVoteSMS: function(id,value){
            if(0 == value){
                return;
            }
            $('.'+id).each(function(){
                var self = $(this);
                self.html(number_format(value, 0, '.', ',') + ' phiếu').show();
            });
        },
        parseLike: function(objectid, siteid, object_type,total){
            vneLike.displayLike(objectid, siteid, object_type,total);
        },
        parseRating: function(id, vne_rating, user_rating, imdb_rating, style){
            if(style == 2){
                if(vne_rating){
                    vne_rating = Math.round(vne_rating);
                    $('.' + id).each(function(){
                        if($(this).attr('display-stype') == 2){
                            var listItem = $('<ul class="list_rating"></ul>');
                            for(var index=0; index < 10; index++){
                                listItem.append($('<li><a href="javascript:;"><span></span></a></li>'));
                            }
                            var result = $('<div class="rating_infor rating_movie addon_line_rating_red"></div>').append(listItem);
                            listItem.find('li:lt(' + vne_rating + ')').addClass('active');
                            listItem.find('li:gt(' + (vne_rating > 0 ? (vne_rating-1) : vne_rating) + ')').addClass('unactive');
                            $(this).html('').append(result);
                        }
                    });
                }
            }else{
                $('.' + id).each(function() {
                    if ($(this).attr('display-stype') == 1) {
                        if ($(this).attr('data-giaitri') == 1) {
                            var result = $('<div class="block_rt_thuvien"></div>');
                            if (user_rating > 0) {
                                result.append('<div class="rt_vne txt_11"><span class=" txt_666">Độc giả:</span> <b class="ratingdocgia">' + user_rating + '</b></div>');
                            }
                            if (vne_rating > 0) {
                                result.append('<div class="rt_bandoc txt_11"><span class=" txt_666">VnExpress:</span> <b class="txt_vne">' + vne_rating + '</b></div>');
                            }
                            $(this).html('').append(result);
                        } else {
                            var result = $('<p class="txt_11 txt_666">');
                            if (imdb_rating > 0) {
                                result.append('IMDb:' + imdb_rating);
                            }
                            if (vne_rating > 0) {
                                result.append((imdb_rating > 0 ? ' | ' : '') + 'VnE:' + vne_rating);
                            }
                            if (user_rating > 0) {
                                result.append(((imdb_rating > 0 || vne_rating > 0) ? ' | ' : '') + 'Độc giả:' + user_rating);
                            }
                            $(this).html('').append(result);
                        }
                    }

                });
            }
        },
        parseRatingObjSH: function(data){
            $('.rating-sh-wd-' + data.objectid ).each(function(){
                var totalobj = data.totalobj ? data.totalobj : 0;
                var result1 ='<div class="box_in_160 border_2_1"><p class="bandoc_danhgia"><strong>Bạn đọc đánh giá</strong><span>('+totalobj+')</span></p>'+
                    '<p class="dg_rate_icon">'+
                    '<img src="'+img_url+'/graphics/img_blank.gif" class="number_rating_red rating'+number_format(data.avg)+'">'+
                    '</p>'+
                    '<p class="diem_danhgia">Trung bình: <strong><label>'+number_format(data.avg)+'</label>/10 </strong></p>'+
                    '</div>';
                var result2 ='<div class="box_in_160"><p class="send_danhgia"><a href="#" class="go_rating">Gửi đánh giá</a></p></div>';
                $(this).html(result1+result2);
                $('a.go_rating').bind('click', function(){
                    var el = $('#cmt-paginator');
                    $('html,body').animate({
                        scrollTop: el.offset().top
                    },1000);
                    return false;
                });

            });
        },
        parseRatingObjDL: function(data){
            $('.rating-dl-ks-' + data.objectid).each(function() {
                var totalobj = data.totalobj ? data.totalobj : 0;
                if ($(this).attr('data-style-dl') == 2) {
                    var result = '<span class="diem">' + number_format(data.avg) + '</span> | ' + totalobj + ' nhận xét';
                    $(this).html(result);
                }else if ($(this).attr('data-style-dl') != 1) {
                    var result = '<div class="txt_danhgia  width_common"><span id="total">' + totalobj + '</span> đánh giá <span class="numdanhgia">' + number_format(data.avg) + '</span></div>';
                    $(this).html(result);
                } else {
                    var result1 = '<div class ="box_in_160 border_2_1" ><p class ="bandoc_danhgia"><strong>Bạn đọc đánh giá </strong><span>(' + totalobj + ')</span ></p>'+
                        '<p class ="dg_rate_icon">'+
                        '<img src = "' + img_url + '/graphics/img_blank.gif" class ="number_rating rating' + number_format(data.avg) + '">'+
                        '</p>'+
                        '<p class = "diem_danhgia" ><strong><label>' + number_format(data.avg) + '</label>/10 </strong></p>'+
                        '</div>';
                    var result2 = '<div class="box_in_160"><p class="send_danhgia"><a id="go_rating" class="txt_view_more" href="#">Gửi đánh giá</a></p></div>';
                    $(this).html(result1 + result2);
                    $('a#go_rating').bind('click', function() {
                        var el = $('#box_comment');
                        if (el.length) {
                            $('html,body').animate({
                                scrollTop: el.offset().top
                            }, 1000);
                        }
                        return false;
                    });
                }
            });
        },
        parseRatingObj: function(data){
            var arrMapping = {
                1: 'Vị trí',
                2: 'Giá cả',
                3: 'Dịch vụ',
                4: 'Phục vụ',
                5: 'Chất lượng',
                6: 'Không gian',
                7: 'Sản phẩm'
            };
            $('.rating-obj-wd-' + data.objectid ).each(function(){
                var self = $(this).hide().append('<div class="bg_block dthdl_box_rating_new"><div class="content_block_rating"></div><div class="clear"></div><a href="#" class="go_rating">Gửi đánh giá của bạn</a></div>');
                var root = self.find('div.content_block_rating');
                // Raring summary
                root.append('<div class="diemtrungbinh"><span>Điểm trung bình</span><p class="tongdiem">' + number_format(data.avg, (data.avg == 10 ? 0 : 1)) + '</p></div>');
                // Rating detail
                root.append($('<div class="vitri_giaca left"><div class="col_right_rating"></div></div>'));
                // Reference to detail rating
                var detail = root.find('div.col_right_rating');
                $.each(data.items, function(key, value){
                    detail.append('<div class="row_rating"><div class="vitri_diem"><p class="left">' + arrMapping[key] +'</p><span class="right">' + number_format(value, (value == 10 ? 0 : 1), '.', ',') + '</span></div><div class=""><div class="line_rating_out"><div style="width:' + (value * 10) + '%;" class="line_rating_in">&nbsp;</div></div></div></div>')
                });
                self.find('a.go_rating').bind('click', function(){
                    var el = $('#rating-obj-' + data.objectid);
                    if(el.length){
                        $('html,body').animate({
                            scrollTop: el.offset().top
                        },1000);
                    }
                    return false;
                });
                self.fadeIn(200);
            });
        },
        parseListen: function(objectid, object_type, total){
            $('span.listen_' + objectid + '-' + object_type).html(number_format(total, 0, '.', ','));
        },
        trackingListen: function(id, type){
            tracking(id, type);
            return false;
        },
        parseTwiter: function(url){
            if(typeof(url) != 'undefined'){
                $('#twitter').attr('data-url', url);
                var twitter = $('a#twitter-share-button');
                twitter.attr('data-url', url);
                twitter.removeClass('twitter-share');
                !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
            }
        },
        parseTopComment: function(id, info){
            if(typeof(info) != 'undefined'){
                var el = $('div[data-component-type="top-cmt"][rel="' + id + '"]');
                $.each(info, function(index, value){
                    if(value.content){
                        var content = '', arrContent = value.content.split(' ');
                        if(arrContent.length > 25){
                            content = arrContent.splice(0, 25).join(' ') + '...';
                        }else{
                            content = value.content;
                        }
                        el.append('<p class="lead">'  + content + '</p><p class="person_cmt_baibien"><b>' + value.full_name + ' </b>- ' + value.creation_time + '</p>');
                    }
                    //console.log(value);

                });
                el.removeAttr('rel');
            }

        },
        parsePredict: function(id, value){
            var el = $('.predict-statistic[data-rel="' + id + '"]');
            var team1 = el.data('text');
            ;//el.find('tbody');

            $.each(value.detail, function( index, info ) {
                var text = '<tr><td class="team_score text_right">' + team1;
                if(info.score1 > info.score2){
                    text += ' thắng ';
                }else if(info.score1 == info.score2){
                    text += ' hòa ';
                }else{
                    text += ' thua ';
                }
                text += info.score1 + '-' + info.score2 + '</td>';
                text += '<td class="percent"><div class="progress_bar_style_3"><span style="width:' + info.avg + '%">' + info.avg+ '%</span></div></td>';
                text += '<td class="score">&nbsp;</td></tr>';
                el.append(text);
            });
            var tmpEl = $('.predict-statistic-total');
            tmpEl.html('Thống kê dự đoán');
            tmpEl.closest('div.total_source_guess').fadeIn(200);

        },
        parseLastestCmt: function(cateId, data){
            var lastCmt = $('[data-component-type="lastest-cmt"][data-cate="' + cateId + '"]');
            if(data.length){

                var el = $('<ul style="overflow: hidden;" tabindex="5000">');
                var totalWord = 50;
                $.each(data, function(index, value){
                    var arrContent = value.content.split(' ');
                    var title = value.title;
                    if(title.length > 40){
                        var arrTitile = title.split(' ');
                        title = arrTitile.splice(0, 8).join(' ') + ' ... ';
                    }
                    var content;
                    if(arrContent.length > 60){
                        content =  '<span class="collapse">' + arrContent.splice(0, totalWord).join(' ') + ' ... ' + '</span>'
                            + '<span class="expand" style="display: none;">' + value.content + '</span>'
                            + '&nbsp;<a href="#" title="Xem đầy đủ nội dung" alt="Xem đầy đủ nội dung"><i class="ico ico-plus"></i></a>';
                    } else{
                        content = value.content;
                    }
                    el.append(
                        '<li>'
                            + content
                            + '<div class="author"><strong><a title="Xem chi tiết" alt="Xem chi tiết" style="color:#333" href="http://photo.vnexpress.net/index/redirect/id/' + value.article_id + '">' + title + '</a></strong> - ' + value.full_name +'</div>'
                            +'</li>'
                    );
                });
                el.find('i.ico').bind('click', function(e){
                    e.preventDefault();
                    var self = $(this), parent = self.closest('li');

                    if(self.hasClass('expanded')){
                        parent.find('.collapse').show();
                        parent.find('.expand').hide();
                        self.removeClass('expanded');
                        self.addClass('ico-plus').removeClass('ico-minus');
                        self.attr({'title': 'Xem đầy đủ nội dung','alt':'Xem đầy đủ nội dung'});
                    }else{
                        parent.find('.expand').show();
                        parent.find('.collapse').hide();
                        self.addClass('expanded');
                        self.attr({'title': 'Thu gọn nội dung', 'alt': 'Thu gọn nội dung'});
                        self.addClass('ico-minus').removeClass('ico-plus');
                    }
                });
                lastCmt.append(el);
				if(typeof(el.niceScroll) === 'function'){
					el.niceScroll({cursorborder:"",cursorcolor:"#ccc",cursorwidth:"3px", autohidemode:false});
				}
            }else{
                lastCmt.closest('div.last-comment').hide();
            }

        }
    };
})();