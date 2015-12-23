$(document).ready(function(){   
    $('.btn_common.block_right').live('click',function(){      
        var $this = $(this);
        $this.parent().find('.infor_rating').find('.rat_news').toggle();
        if($this.hasClass('btn_detail_rat')){
            $this.removeClass('btn_detail_rat');
            $this.addClass('btn_back');
        }else{
            $this.removeClass('btn_back');
            $this.addClass('btn_detail_rat');
        }
    });
    $('.radio_rating').live('click',function(){        
        $(this).each(function(){
            $('.radio_rating').removeClass('check');
            $(this).addClass('check');
        });
    });    
    var w_rating = $('.cert_rating .box_certi_rating').width();       
});
/* widget.js*/

var vneRating = new function () {
       
    this.ratingStaticServerJS = 'http://st.f2.vnecdn.net/responsive/js/interactions';
    this.ratingStaticServerCSS = 'http://st.f3.vnecdn.net/responsive/css';
    this.ratingWebServer = interactions_url;
    this.bbNumberCount = 0;
    this.timeout = 0;
    this.debug = 1;
    this.maxID = 0;
    this.userRating = 0;
    this.sendingRequest=null;
    this.writeLog = function () {
        if (window.console != undefined && window.console != null && vneRating.debug == 1) {
            window.console.log(arguments);
        }
    };
    this.addJsScript = function (jsFile) {
        var g = document.createElement('script');
        g.setAttribute('src', jsFile);
        g.setAttribute('type', 'text/javascript');
        g.setAttribute('charset', 'utf-8');
        document.getElementsByTagName('head')[0].appendChild(g);
        return g;
    };
    this.addCssScript = function (cssFile) {
        var g = document.createElement('link');
        g.setAttribute('href', cssFile);
        g.setAttribute('type', 'text/css');
        g.setAttribute('rel', 'stylesheet');           
        document.getElementsByTagName('head')[0].appendChild(g);
        return g;
    };
    this.addJQueryPlugin = function () {
        /*vneRating.addCssScript(vneRating.ratingStaticServerCSS + '/rating.css');*/
        /*vneRating.addCssScript(vneRating.ratingStaticServerCSS + '/ui-lightness/jquery-ui-1.8.22.custom.css');*/
        //vneRating.addJsScript(vneRating.ratingStaticServerJS + '/jquery.slider.js');
        return;
    };
    this.requestURL = function (urlRequest, callbackFunction) {
        var idf = Math.floor(Math.random() * 1000000);
        var callbackRequestFunction = "ratingFunction_" + idf;
        urlRequest += '&callback=' + callbackRequestFunction;
        vneRating.writeLog(urlRequest);
        if (callbackFunction != undefined) {
            window[callbackRequestFunction] = callbackFunction;
            var gFunction = vneRating.addJsScript(urlRequest);
            if (navigator.userAgent.indexOf("MSIE") > -1 && !window.opera) {
                gFunction.onreadystatechange = function () {
                    if (gFunction.readyState == "loaded") {
                        try {
                            delete window[callbackRequestFunction];
                            document.getElementsByTagName("head")[0].removeChild(gFunction);
                        } catch (e) {
                            window[callbackRequestFunction] = null;
                        }
                    }
                };
            } else {
                gFunction.onload = function () {
                    delete window[callbackRequestFunction];
                    document.getElementsByTagName("head")[0].removeChild(gFunction);
                };
            }
        }
    };
    this.initRating = function () {  
            
        try {
            //init jquery framework
            if (typeof jQuery == 'undefined') {
                    
                var vneMarkJquery = vneRating.addJsScript(vneRating.ratingStaticServerJS + '/jquery-1.7.1.min.js');  
                  
                if (navigator.userAgent.indexOf("MSIE") > -1 && !window.opera) {
                    vneMarkJquery.onreadystatechange = function () {
                        if (vneMarkJquery.readyState == "loaded") {
                            try {
                                vneRating.addJQueryPlugin();
                                vneRating.getRating();
                            } catch (e) {
                                vneRating.writeLog(e);
                            }
                        }
                    };
                } else {
                    vneMarkJquery.onload = function () {
                        vneRating.addJQueryPlugin();   
                        vneRating.getRating();
                    };
                }                     
            } else {
                vneRating.addJQueryPlugin();
                vneRating.getRating();
            }
				 
        } catch (ex) {
            vneRating.writeLog(ex);
        }
    };
    this.changeCaptcha = function(rating_id){
       $.getJSON(vneRating.ratingWebServer + '/captcha/show?callback=?', {deviceenv: 1}, function(json){ 
                    $('#SexyAlertBox-Box').find('#showCaptcha'+rating_id).html(json.html);
        });  
    };   
   
    this.showCaptcha = function (rating_id,type) {
        vneRating.userRating = 0;
        if ($.cookie('vne_rating_' + rating_id + '_' + type) !== null) {
            //Sexy.notice($("#errorMsg").html());
            showAlertBox('Bạn đã đánh giá cho mục này');
        }else {
            if ($.cookie('vne_rating_' + rating_id) != null)
            {
                showAlertBox('Bạn đã đánh giá cho mục này.');
                //vneRating.showResultNew(rating_id);
                //setTimeout(function(){Sexy.display(0)},5000);
            } else {
                if (vneRating.sendingRequest != null) {
                    return false;
                }
                vneRating.sendingRequest = $.getJSON(vneRating.ratingWebServer + '/captcha/show?callback=?', {deviceenv: 1}, function(json) {
                    $('#showCaptcha' + rating_id).html(json.html);
                    Sexy.notice($('#rating_popup' + rating_id).html());
                    if ($.cookie('rating_id_' + rating_id) != null)
                    {
                        var userRating = vneRating.userRating;
                    } else {
                        userRating = 0;
                    }
                    vneRating.userRating = vneRating.userRating > 0 ? vneRating.userRating : '-';
                    $('#SexyAlertBox-InBox').find('div.star:lt(' + userRating + ')').addClass('hover');
                    var event = {
                        fill: function(el) { // fill to the current mouse position.
                            var index = stars.index(el) + 1; //console.log(index);
                            $('#SexyAlertBox-Box').find('#ratinglt').find('#txt_numbdanhgia').html(index);
                            stars.children('a').css('width', '100%').end()
                                    .slice(0, index).addClass('hover').end();
                        },
                        drain: function() { // drain all the stars.
                            stars.filter('.on').removeClass('on').end()
                                    .filter('.hover').removeClass('hover').end();
                        },
                        all: function(el) {
                            var index = stars.index(el) + 1;
                            vneRating.userRating = index;
                            return true;
                        }
                    };
                    //event.reset();
                    var stars = $('#SexyAlertBox-Box').find('#ratinglt').find('#rate2').find('.block_one_start').find('.star');

                    stars.mouseover(function() {
                        event.drain();
                        event.fill(this);
                    }).mouseout(function() {
                        event.drain();
                        var userRating = vneRating.userRating;
                        $('#SexyAlertBox-InBox').find('div.star:lt(' + userRating + ')').addClass('hover');
                        $('#SexyAlertBox-InBox').find('#txt_numbdanhgia').html(userRating);
                    }).focus(function() {
                        event.drain();
                        event.fill(this);
                    }).blur(function() {
                        event.drain();
                    });

                    stars.click(function() {
                        //console.log($(this).attr('data-value'));
                        var gt = $(this).attr('data-rating');
                        $.cookie('rating_id_' + gt, gt, {
                            expires: 1,
                            path: '/'
                        });
                        event.all($(this));
                        //hide error rating
                        $('#SexyAlertBox-Box').find('#ratinglt').find('#rating_error').hide();
                    });

                    vneRating.sendingRequest = null;
                });
            }
        }
    };
    this.showResultNew = function (rating_id) {
          
        Sexy.notice($("#lightbox_rating_core" + rating_id).html());  
        if(defaults.platform!==1){
            $('#SexyAlertBox-Box #ratinglt_result .width_colrating div').click(function(){
                $(this).addClass('active');
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').find('#value_amount').html($(this).attr('amount_rating'));
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').find('#value_num').html($(this).attr('num')+' điểm');
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').show();	
            });
        }else{//is mobile
            $('#SexyAlertBox-Box #ratinglt_result #mobile_result_ratting .number_point').click(function(){
                $(this).addClass('active');
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').find('#value_amount').html($(this).attr('amount_rating'));
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').find('#value_num').html($(this).attr('num')+' điểm');
                $('#SexyAlertBox-Box #ratinglt_result #result_choose').show();
            });     
        }
        
    };
    this.insertRating = function(rating_id, type){
            
        var text = $.trim($('#SexyAlertBox-Box').find('#txtCaptcha'+rating_id).val());
        $('#SexyAlertBox-Box').find('#txtCaptcha'+rating_id).val(text);
        var code = $.trim($('#SexyAlertBox-Box').find('#captchaID').val());            
        var point_type = vneRating.userRating || $("#SexyAlertBox-Box").find("#rating-result").html();
        if(!point_type || point_type==0 || point_type=='-'){
            $('#SexyAlertBox-Box').find('#ratinglt').find('#rating_error').html('Bạn chưa đánh giá');
            return false;
        }
        if(text != ''){                  
            if(vneRating.sendingRequest!=null){
                return false;
            }
            var siteid = $('#siteid').val();
            vneRating.sendingRequest = $.getJSON(vneRating.ratingWebServer + "/rating/insertrating?callback=?",
            {
                siteid: siteid,
                rating_id: rating_id,
                point_type: point_type,
                text: text,
                code: code,
                cookie_aid:$.cookie('fosp_aid'),
                type: type
            },
            function(json){  
                vneRating.sendingRequest=null;
                if ( json.error === 0 ) { 
                    $.cookie('vne_rating_'+rating_id, 1, {
                        expires: 1,
                        path:'/'
                    });           
                    showAlertBox('Bạn đã gửi đánh giá thành công.');
                    //$("#BoxOverlay").hide();
                    Sexy.display(0);
                    //Sexy.display(1);
                    //vneRating.showResultNew(rating_id);
                    //setTimeout(function(){Sexy.display(0)},5000);
                }else if ( json.error == 10 ) {                              
                    $.getJSON(vneRating.ratingWebServer + '/captcha/show?callback=?', {deviceenv: 1}, function(json){ 
                        //Set message error 
                        $('#SexyAlertBox-Box').find('#captcha_error').html('Mã xác nhận không đúng');
                        //F5 captchar
                        $('#SexyAlertBox-Box').find('#showCaptcha'+rating_id).html(json.html);
                        //reset 
                        $('#SexyAlertBox-Box').find('#txtCaptcha'+rating_id).val('');  
                    });
                } else {
                    alert(json.message);
                }
            });
                   
        }
        else{
            $('#SexyAlertBox-Box').find('#captcha_error').html('Mã xác nhận không đúng');
        }
                       
    };
    
    this.insertRatingObject = function(object, type){
        var text = $.trim($('#SexyAlertBox-Box').find('#txtCaptcha'+object).val());
        $('#SexyAlertBox-Box').find('#txtCaptcha'+object).val(text);
        var code = $.trim($('#SexyAlertBox-Box').find('#captchaID').val());
        
        var point = vneRating.userRating;
        if (!point || point === 0 || point=='-') {
            $('#SexyAlertBox-Box').find('#ratinglt').find('#rating_error').html('Bạn chưa đánh giá');
            return false;
        }
        if($.cookie('vne_rating_' + object + '_' + type) !== null){
            showAlertBox('Bạn đã đánh giá cho mục này.');
        }else {
            if (text !== '') {
                var siteid = $('#siteid').val();
                vneRating.sendingRequest = $.getJSON(vneRating.ratingWebServer + "/rating/insertratingobj?callback=?",
                        {
                            siteid: siteid,
                            rating_id: object,
                            point_type: point,
                            cookie_aid: $.cookie('fosp_aid'),
                            type: type,
                            text: text,
                            code: code
                        },
                function(json) {
                    vneRating.sendingRequest = null;
                    if (json.error === 0) {
                        $.cookie('vne_rating_' + object + '_' + type, point, {
                            expires: 1,
                            path: '/'
                        });
                        showAlertBox('Bạn đã gửi đánh giá thành công.');
                        Sexy.display(0);
                        var listRating = $('.rt_point #point_rating_' + object);
                        listRating.find('.current_point').html(point);
                        listRating.find('.number_rating').addClass('rating' + point);
                        /*
                         var listRating = $('.rating_infor #rating_el' + object);
                         var lastLi = listRating.find('li.end_li');
                         lastLi.find('#total_avg').html(json.avg);
                         lastLi.find('#total_amount').html(json.total);
                         lastLi.find('.current_point').html(point);
                         listRating.find('li:lt(' + point + ')').addClass('active_hover');
                         $('#avg_user_rating_' + object).html(json.avg).parent().show();
                         */
                        } else if (json.error == 10) {
                        $.getJSON(vneRating.ratingWebServer + '/captcha/show?callback=?', {deviceenv: 1}, function(json) {
                            //Set message error 
                            $('#SexyAlertBox-Box').find('#ratinglt').find('#captcha_error').html('Mã xác nhận không đúng');
                            //F5 captchar
                            $('#SexyAlertBox-Box').find('#showCaptcha' + object).html(json.html);
                            //reset 
                            $('#SexyAlertBox-Box').find('#txtCaptcha' + object).val('');
                        });
                    } else {
                        alert(json.message);
                    }
                });
            } else {
                $('#SexyAlertBox-Box').find('#captcha_error').html('Mã xác nhận không đúng');
            }
        }

    };
        
    this.getRating = function () {   
        var arrObjectID = [], arrType = [], arrStyle = [], arrIMDB = [];
        var siteid = 0;
        $('div[data-component-type="rating"]').each(function(){
            var self = $(this);
            siteid = self.attr('data-component-siteid');
            var objectid = self.attr('data-component-value');
            if(objectid){
                arrObjectID.push(objectid);
                arrType.push(self.attr('data-component-object-type') ? self.attr('data-component-object-type') : 0);
                arrStyle.push(self.attr('data-component-object-stype') ? self.attr('data-component-object-stype') : 0);
                arrIMDB.push(self.attr('data-imdb') ? self.attr('data-imdb') : 0);
            }
        });
        
        var e=window.document,k=e.getElementsByTagName("head")[0]||e.body;
            var m=e.createElement("script");
            m.src=vneRating.ratingWebServer + '/rating/index?siteid=' + siteid + '&objectid=' + arrObjectID.join(';') +  '&type=' + arrType.join(';') + '&style=' + arrStyle.join(';') + '&imdb=' + arrIMDB.join(';')+'&deviceenv=' + defaults.platform;
            m.async=!0;
            m.charset="UTF-8";        
            k.appendChild(m); 
    };
    this.displayRating = function (objectid, data) {
        $('div[data-component-value='+objectid+']').html(data);
        var listRating = $('#point_rating_' + objectid);
        type = listRating.attr("rating_type");
        if (type) {
            if ($.cookie('vne_rating_' + objectid + '_' + type) !== null) {
                listRating.find('.number_rating').addClass('rating' + $.cookie('vne_rating_' + objectid + '_' + type));
                listRating.find('.number_rating').addClass('rating' + $.cookie('vne_rating_' + objectid + '_' + type));
                listRating.find('.current_point').html($.cookie('vne_rating_' + objectid + '_' + type));
            }
        }
        
    };
    this.closePopupRating = function(){
        Sexy.display(0);
    };
              
};
var showAlertBox = function(msg, title){
        title = title || 'Thông báo';
        if('function' == typeof(Sexy.notice)){
            var mobile_web = defaults.platform!==1 ? msg:'<div class="content_form">'+msg+'</div>';
            var tpl = '<div id="login-vne4" class="login-form"><div class="ttOline">' + title + '</div><div class="content_ligbox"><div class="complete-form">' + mobile_web + '</div><div class="close-lb"></div><div class="clear"></div></div></div>';
            Sexy.notice(tpl);
        }else{
            alert(msg);
        }
    };
    
 
vneRating.initRating();