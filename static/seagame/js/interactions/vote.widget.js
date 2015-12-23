var vneVote = new function () {
    this.voteStaticServerJS = "http://st.f2.vnecdn.net/responsive/js";
    this.voteStaticServerCSS = "http://st.f3.vnecdn.net/c/v100/rating";
    this.voteWebServer = interactions_url;
    this.bbNumberCount = 0;
    this.timeout = 0;
    this.debug = 1;
    this.maxID = 0;
    this.sendingRequest= null;
    this.writeLog = function () {
        if (window.console != undefined && window.console != null && vneVote.debug == 1) {
            window.console.log(arguments);
        }
    };

    this.CheckThisVote = function (field) {
        // Fix bug can not uncheck after checked
        if($(field).is(':checked')==false){ $(field).attr('checked',false); return; }

        var all_checkbox = $(field).parent().parent().parent().find('input[type="checkbox"]');

        all_checkbox.each(function(){
            if($(this).attr('checked')){
                $(this).removeAttr('checked');
            }
        });

        $(field).attr('checked', true);
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
        /*vneVote.addCssScript(vneVote.voteStaticServerCSS + '/vote_vne_core.css');*/
        return;
    };
    this.requestURL = function (urlRequest, callbackFunction) {
        var idf = Math.floor(Math.random() * 1000000);
        var callbackRequestFunction = "voteFunction_" + idf;
        urlRequest += '&callback=' + callbackRequestFunction;
        vneVote.writeLog(urlRequest);
        if (callbackFunction != undefined) {
            window[callbackRequestFunction] = callbackFunction;
            var gFunction = vneVote.addJsScript(urlRequest);
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
    this.initVote = function () {
        try {
            //init jquery framework
            if (typeof jQuery == 'undefined') {

                var vneVoteJquery = vneVote.addJsScript(vneVote.voteStaticServerJS + '/jquery-1.7.1.min.js');

                if (navigator.userAgent.indexOf("MSIE") > -1 && !window.opera) {
                    vneVoteJquery.onreadystatechange = function () {
                        if (vneVoteJquery.readyState == "loaded") {
                            try {
                                vneVote.addJQueryPlugin();
                                vneVote.getVote();
                            } catch (e) {
                                vneVote.writeLog(e);
                            }
                        }
                    };
                } else {
                    vneVoteJquery.onload = function () {
                        vneVote.addJQueryPlugin();
                        vneVote.getVote();
                    };
                }
            } else {
                vneVote.addJQueryPlugin();
                vneVote.getVote();
            }

        } catch (ex) {
            vneVote.writeLog(ex);
        }
    };

    this.animateResults = function(question_id){
        if($('#siteid').val() == 1002966 && $('#Article_Banner_468x90_Vote_available').length ==0 && typeof Poly_Position_ShowArticleBanner468x90_Vote == "function"){
            Poly_Position_ShowArticleBanner468x90_Vote();
        }
        $('.bg_center_scroll').each(function(){
            var percent = $(this).attr('percent');
            $(this).css('width',percent + "%");
        });
        var mess = $("#vne_result_box_" + question_id).html();
        $('#BoxAlertBtnOk').click();
        //Show result
        Sexy.notice(mess);
    };

   this.changeCaptcha = function(question_id){
       $.getJSON(vneVote.voteWebServer + '/captcha/show?callback=?', {deviceenv: defaults.platform}, function(json){
                    $('#SexyAlertBox-Box').find('#showCaptcha'+question_id).html(json.html);
        });
    };
    this.insertvoteOK = function (question_id){

        var text = $.trim($('#SexyAlertBox-Box').find('#txtCaptchavote').val());
        $('#SexyAlertBox-Box').find('#txtCaptchavote').val(text);
        var code = $.trim($('#SexyAlertBox-Box').find('#captchaID').val());


        if(text != ''){
            var answerid = [], index = 0;
            $("input[name='answer_"+question_id+"']:checked").each(function(e){
                answerid[index++] = $(this).val();
            });
            $("#vne_captcha_box_"+question_id).fadeOut("fast",function(){
                //$(this).empty();
                if(vneVote.sendingRequest != null || answerid.lenght == 0){
                    return false;
                }
                vneVote.sendingRequest = $.getJSON(vneVote.voteWebServer + "/vote/insertvote?callback=?",
                {
                    siteid: $('#siteid').val(),
                    questionid: $('#objid_'+question_id).val(),
                    answerid: answerid.join(','),
                    text: text,
                    code: code,
                    deviceenv: defaults.platform,
                    cookie_aid:$.cookie('fosp_aid')

                },
                function(json){
                    vneVote.sendingRequest=null;
                    if ( json.error == 0 ) {
                        //Set cookie
                        $.cookie('vne_vote_'+$('#objid_'+question_id).val(), 1, {
                            expires: 1
                        });
                        $("input[name='answer_" + question_id + "']:checked").removeAttr('checked');
                        $('#vne_result_box_'+question_id).html(json.data);
                        vneVote.animateResults(question_id);
                        //Close captcha
                        Sexy.display(0);
                    } else if ( json.error == 10 ) {
                        $.getJSON(vneVote.voteWebServer + '/captcha/show?callback=?', {deviceenv: defaults.platform}, function(json){
                            //Set message error
                            $('#SexyAlertBox-Box #captcha_error').html('Mã xác nhận không đúng');
                            //F5 captchar
                            $('#SexyAlertBox-Box').find('#showCaptcha'+question_id).html(json.html);
                            //reset
                            $('#SexyAlertBox-Box #txtCaptchavote').val('');
                        });
                    } else {
                        alert(json.message);
                    }
                });
            });
        }
        else{
            $('#SexyAlertBox-Box #captcha_error').html('Mã xác nhận không đúng');
        }
    };
    this.showAlertBox = function(msg, title){
        title = title || 'Thông báo'
        if('function' == typeof(Sexy.notice)){
            var mobile_web = defaults.platform!==1 ? msg:'<div class="content_form">'+msg+'</div>';
            var tpl = '<div id="login-vne4" class="login-form"><div class="ttOline">' + title + '</div><div class="content_ligbox"><div class="complete-form">' + mobile_web + '</div><div class="close-lb"></div><div class="clear"></div></div></div>';
            Sexy.notice(tpl);
        }else{
            alert(msg);
        }
    };
    this.insertVote = function (question_id) {
        var answerid = $("input[name='answer_"+question_id+"']:checked");
        if(answerid.length == 0){
            vneVote.showAlertBox('Hãy chọn 1 mục trước khi biểu quyết');
            return false;
        }
        else
        {
            if($.cookie('vne_vote_'+$('#objid_'+question_id).val()) != null)
            {
                vneVote.animateResults(question_id);
            }
            else
            {
                $.getJSON(vneVote.voteWebServer + '/captcha/show?callback=?', {deviceenv: defaults.platform}, function(json){
                    $('#showCaptcha'+question_id).html(json.html);
                    Sexy.notice($('#vne_captcha_box_'+question_id).html());
                });
            }


        }
    };

    this.getVote = function () {
         var arrObjectID = [];
         var siteid = 0;
        $('div[data-component-type="vote"]').each(function(){
            siteid = $(this).attr('data-component-siteid');
            arrObjectID.push($(this).attr('data-component-value'));
        });

        var e=window.document,k=e.getElementsByTagName("head")[0]||e.body;
            var m=e.createElement("script");
            m.src=vneVote.voteWebServer + '/vote/index?siteid=' + siteid + '&objectid=' + arrObjectID.join(';')+'&deviceenv=' + defaults.platform;
            m.async=!0;
            m.charset="UTF-8";
            k.appendChild(m);

    };
    this.displayVote = function (objectid, data) {
        $('div[data-component-value='+objectid+']').html(data);
    };
};
vneVote.initVote();
