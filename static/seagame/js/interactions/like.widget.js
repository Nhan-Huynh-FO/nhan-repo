var vneLike = new function () {
    this.cacheDuration = 15; /* Cache data in 15 minites*/
    this.storeDuration = 1440; /* 24* 60 Cache data in 24 h*/
    this.voteStaticServer = 'http://st.f3.vnecdn.net/responsive';
    this.interactionsServer = interactions_url;
    this.keyLocalLike = 'vne-like-local';
    this.debug = 0;
    this.isRequest = 0;
    
    this.sendingRequest= null;

    this.writeLog = function () {
        if (window.console != undefined && window.console != null && vneLike.debug == 1) {
            window.console.log(arguments);
        }
    };
    this.includeCss = function (cssFile) {
        var g = document.createElement('link');
        g.setAttribute('href', cssFile);
        g.setAttribute('type', 'text/css');
        g.setAttribute('rel', 'stylesheet');           
        document.getElementsByTagName('head')[0].appendChild(g);
        return g;
    };
    this.includeScript = function(jsFile) {
        var g = document.createElement('script');
        g.setAttribute('src', jsFile);
        g.setAttribute('type', 'text/javascript');
        g.setAttribute('charset', 'utf-8');
        document.getElementsByTagName('head')[0].appendChild(g);
    };
    
    this.cookies = function(key, value, options) {
        if (arguments.length > 1 && String(value) !== "[object Object]") {
            options = (typeof options === 'object') ? options : {};
            if (value === null || value === undefined) {
                options.expires = -1;
            }
            if (typeof options.expires === 'number') {
                var days = parseInt(options.expires), t = options.expires = new Date();
                options.expires.setDate(t.getDate() + days);
            }
            value = String(value);
            return (document.cookie = [encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '',
                options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''].join(''));
        }
        options = value || {};
        var result, decode = options.raw ? function(s) {
            return s;
        } : decodeURIComponent;
        return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
    }
    /*Write data to local storage or cookie*/
    this.readLocalData = function(key){
        return vneLike.cookies(key);
    }
    /*Read data in local storage or cookie*/
    this.writeLocalData = function(key, value, option){
        vneLike.cookies(key, value, option);
    }
    /*Format total like*/
    this.formatNumber = function(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    };
    /* Display toal like*/
    this.showTotal = function(total){
        return this.formatNumber(total);
    };
      
    this.requestURL = function (urlRequest, callbackFunction) {
        var idf = Math.floor(Math.random() * 1000000);
        var callbackRequestFunction = "likeFunction_" + idf;
        urlRequest += '&callback=' + callbackRequestFunction;
        vneVote.writeLog(urlRequest);
        if (callbackFunction != undefined) {
            window[callbackRequestFunction] = callbackFunction;
            var gFunction = vneVote.includeScript(urlRequest);
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
    this.initLike = function () {
        console.log('action like');
        try {
            vneLike.getLike();
        } catch (ex) {
            vneLike.writeLog(ex);
        }
    };
    
    this.getUserLike= function(objectID, siteID){
        // Read data in local
        var data = vneLike.readLocalData('vne-like-enable-'+  siteID +  '-' + objectID );
        if(vneLike.debug){
            vneLike.writeLog({
                method:'getUserLike', 
                objectid: objectID, 
                siteid: siteID, 
                value: data
            });
        }
        return data;
    }
    this.setUserLike= function(objectID, siteID, value){
        vneLike.writeLocalData('vne-like-enable-'+  siteID +  '-' + objectID, value, {
            expires: vneLike.cacheDuration, 
            path: '/'
        });
        //vneLike.writeLocalData('vne-like-enable-'+  siteID +  '-' + objectID, value);
        if(vneLike.debug){
            vneLike.writeLog({
                method:'setUserLike', 
                objectid: objectID, 
                siteid: siteID, 
                value: value
            });
        }
        return true;
    };
    this.showAlertBox = function(msg, title){
        title = title || 'Thông báo'
        if('function' == typeof(Sexy.notice)){
            var mobile_web = defaults.platform!==1 ? msg:'<div class="content_form">'+msg+'</div>';
            var tpl = '<div id="login-vne4" class="login-form"><div class="ttOline">' + title + '</div><div class="complete-form">' + mobile_web + '</div><div class="close-lb"></div><div class="clear"></div></div>';
            Sexy.notice(tpl);
        }else{
            alert(msg);
        }
    };
    this.addLike = function(obj){
        var key = obj.attr('id').replace('vne-like-anchor', 'vne-like');

        var arrData = obj.attr('id').match(/\d+/g);
        var siteID = arrData[0];
        var objectID = arrData[1];
        var objectType = obj.parent().attr('data-objecttype');
        var isLike = vneLike.getUserLike(objectID, siteID);
        var totalLike = parseInt(vneLike.readLocalData(key));
        if(isNaN(totalLike)){
            totalLike = parseInt(obj.attr('total-like'));
        }
        var keyEnable = 'vne-like-enable-' + objectID + '-' + siteID;
        var action = 'add';
        
        
        if(totalLike == null){
            totalLike = 0;
        }
        // User not yet like
        if(isLike == null){
            totalLike = totalLike+1;
            // Add like
            vneLike.setUserLike(objectID, siteID, 1);
            vneLike.displayLike(objectID, siteID, objectType, totalLike);
            if(vneLike.debug){
                vneLike.writeLog({
                    method:'addLike', 
                    action: 'addlike',
                    objectid: objectID, 
                    siteid: siteID
                });
            }
        }else if(parseInt(isLike) == 1){
            // User unlike
            vneLike.setUserLike(objectID, siteID, 0);
            
            if(totalLike > 0){
                totalLike = totalLike-1;
            }
            action = 'remove';
            vneLike.displayLike(objectID, siteID, objectType, totalLike);
            if(vneLike.debug){
                vneLike.writeLog({
                    method:'addLike', 
                    action: 'unlike', 
                    objectid: objectID, 
                    siteid: siteID
                });
            }
        }else if(parseInt(isLike) == 0){
            // Show error message
            vneLike.showAlertBox('Bạn đã sử dụng chức năng này. Bạn phải đợi 24h nữa mới được thao tác tiếp');
            return;
        };
        var cat_id = $('meta[name=tt_category_id]').attr("content");
        vneLike.sendingRequest = $.getJSON(vneLike.interactionsServer + "/like/" + action + "?callback=?",
        {
            siteid: siteID, 
            objectid: objectID,
            objecttype: objectType,
            cookie_aid: vneLike.cookies('fosp_aid'),
            category_id: (cat_id != undefined ? cat_id : null)
        },
        function(json){  
            vneLike.sendingRequest=null;
            vneLike.writeLocalData(key, totalLike, {
                expires: 1 /*1 day */
            });
        });
        return false;
    }
   
    /*Get total like of article*/
    this.getLike = function () {
            var arrObj = [], arrType = [], arrMatch = []; 
            var siteid;
            $.each($('div[data-component-type="like"]'), function (index, value){
                
                var divLike = $(value);
                siteid = divLike.attr('data-component-siteid');
                var objectid = divLike.attr('data-component-objectid');
                var object_type = divLike.attr('data-objecttype');
                var key = siteid + '-' + objectid;

                var totalLike = vneLike.readLocalData('vne-like-' + key);

                // Check total present at local

                divLike.html('<a id="vne-like-anchor-' + key + '" href="#" class="btn_vne_like_core"><span class="icon_hand">&nbsp</span></a><div class="arrow_total_like"><span id="like-total-' + key + '" class="txt_total_like"></span></div>');

                divLike.find('#vne-like-anchor-' + key).bind('click', function(event){
                    event.preventDefault();
                    vneLike.addLike($(this));
                    return false;
                });
                arrObj.push(objectid);
                arrType.push(object_type);
            });
            $.each($('div[data-component-type="likematch"]'), function (index, value){
                arrMatch.push($(this).attr('data-component-objectid'));
            });
            if(arrMatch.length > 0 || arrObj.length > 0){
                this.includeScript(vneLike.interactionsServer + '/like/?siteid=' + siteid + '&objectid=' + arrObj.join(';') + '&objecttype=' + arrType.join(';') + '&matchid=' + arrMatch.join(';'));
            }
    };
    
    this.displayLike = function(objectid, siteid, object_type,total){
        var divLike = $('div[data-component-objectid='+objectid+']');
        var anchor = divLike.find('#vne-like-anchor-' + siteid + '-' + objectid);
        anchor.attr('total-like', total);
        var dislike = defaults.platform!==1 ? 'dislike' :'unlike';
        if(vneLike.getUserLike(objectid, siteid) == 1){
            // Show unlike
            anchor.addClass(dislike);
            // Change icon when hover on
            divLike.find('a > span').hover(function(){
                $(this).removeClass().addClass('hover')
            },function(){
                $(this).removeClass().addClass('icon_hand');
            });
            if(vneLike.debug){
                vneLike.writeLog({
                    method:'displayLike', 
                    action: 'disable', 
                    objectid: objectid, 
                    siteid: siteid
                });
            }
        }else{
            anchor.removeClass(dislike);
            if(vneLike.debug){
                vneLike.writeLog({
                    method:'displayLike', 
                    action: 'enable', 
                    objectid: objectid, 
                    siteid: siteid
                });
            }
        }
        divLike.find('#like-total-' + siteid + '-' + objectid).html(vneLike.showTotal(total));
        divLike.show();
    }
    this.likeMatch = function(){
        var self = $(this);
        // Check user already like other team
        if(vneLike.cookies(self.attr('rel'))){
            return;
        }
        var arrInfo = self.attr('id').split('_');
        var isLike = vneLike.cookies(self.attr('id')) || 0;
        if(vneLike.isRequest){
            return;
        }
        vneLike.isRequest = true;
        vneLike.sendingRequest = $.getJSON(vneLike.interactionsServer + "/like/match?callback=?",
        {
            matchid: arrInfo[2], 
            team: arrInfo[3],
            status: 1 == isLike ? 0 : 1,
            siteid: self.attr('siteid') || 0,
            cookie_aid: vneLike.cookies('fosp_aid') || ''
        },
        function(json){  
            vneLike.isRequest = false;
            if(json.error == 0){
                // Get value from el
                var totalEl = $('#team_' + self.attr('id').replace('like_match_',''));                    
                
                var current = parseInt(totalEl.html()), total = parseInt(self.attr('total'));                
                var percen_vote = $('#percent_like_match_'+ self.attr('matchid'));
                
                // User user was liked
                if(isLike){
                    self.removeClass('dislike');
                    vneLike.cookies(self.attr('id'), 0, {expires: -365});
                    if(total > 0){
                        totalEl.html(Math.max(--current, 0));
                        self.attr('total', Math.max(--total, 0));
                    }
                }else{
                    self.addClass('dislike');
                    vneLike.cookies(self.attr('id'), 1, {expires: 1});

                    totalEl.html(++current);
                    self.attr('total', ++total);
                }
                
                // Update percent
                var totalL1 = $('#team_' + self.attr('matchid') + '_1').html(),
                    percent1 = total > 0 ? Math.round((totalL1 / total) * 100) : 50;
                
                //console.log(percent1,parseInt(100 - percent1));
                percen_vote.attr('style', 'width:' + parseInt(percent1) + '%');
            }else{
                alert(json.msg);
            }
            
        });
        
    };
   
    this.displayLikeMatch = function(matchid, totalFan1, totalFan2){
        
        var dislike1 = (vneLike.cookies('like_match_' + matchid + '_1')) ? 'dislike' : '';
        var dislike2 = (vneLike.cookies('like_match_' + matchid + '_2')) ? 'dislike' : '';
       
        var team1 = '<div class="btn_vote left social_share">'+'<div class="right">'+
                        '<div class="item_social"> <a href="javascript:;" matchid="'+matchid+'" id="like_match_'+matchid+'_1" rel="like_match_'+ matchid +'_2" total="'+(totalFan1 + totalFan2)+'" class="btn_quantam '+dislike1+'"></a>'+
                          '<div class="number_count"><span id="team_' + matchid + '_1">'+vneLike.formatNumber(totalFan1)+'</span></div>'+
                        '</div>'+'</div>'+'</div>';
        var team2 = '<div class="btn_vote right social_share">'+'<div class="left">'+
                        '<div class="item_social"> <a href="javascript:;" matchid="'+matchid+'" id="like_match_'+matchid+'_2" rel="like_match_'+ matchid +'_1" total="'+(totalFan1 + totalFan2)+'" class="btn_quantam '+dislike2+'"></a>'+
                          '<div class="number_count"><span id="team_' + matchid + '_2">'+vneLike.formatNumber(totalFan2)+'</span></div>'+
                        '</div>'+'</div>'+'</div>';
        var percent1 = (totalFan1 + totalFan2) > 0 ? Math.round((totalFan1 / (totalFan1 + totalFan2)) * 100) : 50;
        var percentResult ='<div class="view_percent_vote">'+
                      '<div class="process_vote">'+
                        '<div id="percent_like_match_'+matchid+'" style="width:'+parseInt(percent1)+'%" class="percen_vote">&nbsp;</div>'+
                      '</div>'+
                    '</div>';
        var result = team1 + percentResult + team2;
        $('div[data-component-objectid="' + matchid + '"]').each(function(){
            $(this).html(result);
            var siteid = $(this).attr('data-component-siteid') || 0;
            $('a[id^="like_match_' + matchid + '"]').each(function(){
                $(this).attr('siteid', siteid);
            });
        });
        $('a[id^="like_match_' + matchid + '"]').bind('click', vneLike.likeMatch);
    };
};
