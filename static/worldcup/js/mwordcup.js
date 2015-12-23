/**
 * Jcache
 */
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(4(c){2.h="(i)(0.0.1)";2.d=j;2.7=[];2.5=0;2.3=[];2.k=4(a,b){"8"!=9 b&&("8"==9 2.3[a]&&2.5++,2.7.l(a),2.3[a]=b,2.5>2.d&&2.e());6 b};2.f=4(a){m("8"!=9 2.3[a]){2.5--;g b=2.3[a];n 2.3[a]}6 b};2.o=4(a){6 2.3[a]};2.p=4(a){6"8"!=9 2.3[a]};2.e=4(){2.f(2.7.q())};2.r=4(){g a=2.5;2.7=[];2.5=0;2.3=[];6 a};c.s=2;6 c})(t);',30,30,'||this|items|function|cache_length|return|keys|undefined|typeof||||maxSize|removeOldestItem|removeItem|var|version|beta|10|setItem|push|if|delete|getItem|hasItem|shift|clear|jCache|jQuery'.split('|'),0,{}))
var thethao = {
    device      : device_env,
    article_id  : article_id,
    location_href : window.location.href,
    application_env : application_env,
    init : function() {
        if (typeof(ArticleShare) != 'undefined') {
            ArticleShare.renderSocialPlugins();
            ArticleShare.SocialButtonLike();
        }
        common.initCommon();
        common.switchToDesktop();
        //slideshow photo
        if (typeof(common.initLinkSlideshow) != 'undefined' && this.application_env == 'production') {
            common.initLinkSlideshow();
        }
        $('.tab_main_mobile').click(
            function(){
                if($('.list_tab_mobile').css("display") == "none"){
                    $('.list_tab_mobile').fadeIn(200);
                }
                else{
                    $('.list_tab_mobile').hide();
                }
            }
        );
        if ($("#top_fixture_worldcup").size() > 0) {
            this.loadFixture($("#top_fixture_worldcup"));
        }
        this.counDown();
        // rut gon du doan
        if ($('.block_xemthem_thele').size() > 0) {
            $('.block_xemthem_thele a').toggle(
                function(){
                    $('.block_fck_thele').addClass('expan');
                    $('.block_xemthem_thele a').addClass('active');
                    $('.block_xemthem_thele a').text('Rút gọn');
                },
                function(){
                    $('.block_fck_thele').removeClass('expan');
                    $('.block_xemthem_thele a').removeClass('active');
                    $('.block_xemthem_thele a').text('Xem thêm');
                    $("html, body").animate({
                        scrollTop: $('.block_xemthem_thele').offset().top
                    }, 900);
                }
            );
            $('#spam_nha_mang').on('click', function(){
                $('.block_fck_thele').addClass('expan');
                $('.block_xemthem_thele a').addClass('active');
                $('.block_xemthem_thele a').text('Rút gọn');
                $("html, body").animate({
                    scrollTop: $('#du_doan_spam').offset().top
                }, 900);
            });
        }
        
        /* Scroll box huychuong vie*/
        if ($("#thanhtich_doanvietnam .scroll-pane").size() > 0)
        {
            $("#thanhtich_doanvietnam .scroll-pane").niceScroll("#thanhtich_doanvietnam .content_scoller",{touchbehavior:false,autohidemode:false});
        }

        /* Scroll top story */
        if ($('#box_news_top .box_sub_hot_news .scroll-pane').size() > 0) {
            $("#box_news_top .box_sub_hot_news .scroll-pane").niceScroll(".box_sub_hot_news .content_scoller",{touchbehavior:false,autohidemode:false});
        }
        if ($("#cacdoibong .scroll-pane").size() > 0)
        {
            $("#cacdoibong .scroll-pane").niceScroll("#cacdoibong .content_scoller",{touchbehavior:false,autohidemode:false});
            $("select#danh_sach_doi_bong").change(function () {
                $("select#danh_sach_doi_bong option:selected").each(function () {
                    id = $("select#danh_sach_doi_bong option:selected").val()
                });
                $("ul.list_doibong").hide();
                $("ul#team" + id).fadeIn("fast")
            }).trigger("change")
        }

        if (!this.article_id) {
            common.clock();
        }
    },
    viewTab : function(container, tab) {
        var title   = ("#" + container + " .title_tab li"),
            select  = (title + " a." + tab);

        $(title).removeClass("active");
        $(select).parent().addClass("active");
        $("#" + container + " .content_tab").hide();
        $("#" + container + " ." + tab + "").fadeIn(400)
    },
    checkSearch : function () {
        var q = $('#form-search .txt_input').val();
        return q ? true : false;
    },
    loadCaptcha: function(width, height) {
        width = typeof(width) != 'undefined' ? width : 60;
        height = typeof(height) != 'undefined' ? height : 28;
        $('#txtCode').val('');
        $.ajax({
            type: 'GET',
            url: base_url + '/captcha/show/width/' + width + '/height/' + height,
            dataType: 'json',
            success: function(response) {
                if (response != false) {
                    $('#showCaptcha').html(response.html);
                }
            }
        });
    },
    //check string is blank or not
    blank: function(str) {
        return /^\s*$/.test(str || ' ');
    },
    counDown: function() {
        // set the date we're counting down to
        var target_date = new Date("June 13, 2014 3:00:00");
        var now = new Date();
        var countdown = document.getElementById("countdown");
        if ((countdown) != null) {
            if(now.getTime() < target_date.getTime())
            {
                // variables for time units
                var days, hours, minutes, seconds;

                // update the tag with id "countdown" every 1 second
                setInterval(function () {
                    // find the amount of "seconds" between now and target
                    var current_date = new Date().getTime();
                    var seconds_left = (target_date - current_date) / 1000;

                    // do some time calculations
                    days = parseInt(seconds_left / 86400);
                    seconds_left = seconds_left % 86400;

                    hours = parseInt(seconds_left / 3600);
                    seconds_left = seconds_left % 3600;

                    minutes = parseInt(seconds_left / 60);
                    seconds = parseInt(seconds_left % 60);

                    // format countdown string + set tag value
                    countdown.innerHTML ="<span  class='ngay'>"+ days + "<label>Ngày</label></span> <span class='gio'>" + hours + "<label>Giờ</label> </span> <span class='phut'>"
                    + minutes + "<label>Phút</label></span> <span class='giay'> " + seconds + "<label>Giây</label> </span>";

                }, 1000);

            }
            else
            {
                $('#countdown').hide();
            }
        }
    },
    carousel: function(container)
    {
        var owl = $(container);
        owl.owlCarousel({
            autoPlay: 6000,
            items : 2,
            itemsDesktop : [1199,2],
            itemsTablet: [600,2], //2 items between 600 and 0
            itemsDesktopSmall : [900,2],// betweem 900px and 601px
            itemsMobile		:[479,2],
            pagination:false
        });
        $(".block_slide_300 .next_slider").click(function(){
            owl.trigger('owl.next');
        });
        $(".block_slide_300 .prev_slider").click(function(){
            owl.trigger('owl.prev');
        });
    },
    loadFixture: function(container)
    {
        var url  = base_url + '/box-lich-thi-dau';
        $.ajax({
            type: 'get',
            url: url,
            data:{
            },
            dataType: 'json',
            async: false,
            beforeSend: function(){
                container.html();
            },
            success: function(response) {
                if (response.error)
                {
                    Team.error = true;
                    Team.errorMsg = response.msg;
                    container.hide();
                }
                else if(response.html != "")
                {
                    container.html(response.html);
                }
                else
                {
                    container.hide();
                }
            }
        });
    },
    trackingImpression: function()
    {
        var htmlTracking    = '<scr'+'ipt src="http://bs.serving-sys.com/BurstingPipe/adServer.bs?cn=tf&c=19&mc=imp&pli=10045207&PluID=0&ord='+timeTemp+'&rtu=-1"></scr' + 'ipt>';
        $("#trackingLandingPage").html(htmlTracking);
    }
};
/**
 * Calendar parser
 */
var CalendarWorldcup = {
    calendar_parser_base_url:base_url,
    calendar_parser_url:'http://st.f2.vnecdn.net/responsive/j/v15/utils/calendar',
    calendar_parser_id:'calendar',
    datepickerloaded: 0,
    calendar_text:'',
    parse: function()
    {
        if($('#'+this.calendar_parser_id).length > 0 ) {
            //load static
            this.addCss(this.calendar_parser_url+'/datepicker/css/datepicker.css');
            this.addCss(this.calendar_parser_url+'/datepicker/css/layout.css');
            this.addHtml();
            var self = this;
            $.getScript( this.calendar_parser_url+"/datepicker/js/datepicker.js", function() {
                self.action();
                self.datepickerloaded = 1;
            });
        }
        else {
            return false;
        }
    },
    addCss: function(cssFile) {
        var g = document.createElement('link');
        g.setAttribute('href', cssFile);
        g.setAttribute('type', 'text/css');
        g.setAttribute('rel', 'stylesheet');
        document.getElementsByTagName('head')[0].appendChild(g);
    },
    addHtml: function() {
        var nowDate = new Date();
        nowDate = [nowDate.getDate(), nowDate.getMonth()+1, nowDate.getFullYear()].join('/');
        var span =  document.createElement('div');
        span.setAttribute('id', 'vne_ttn_current_date');
        span.setAttribute('style', 'display:none');

        var widgetField =  document.createElement('div');
        widgetField.setAttribute('id', 'widgetField');

        var widgetCalendar =  document.createElement('div');
        widgetCalendar.setAttribute('id', 'widgetCalendar');

        var widget =  document.createElement('div');
        widget.setAttribute('id', 'widget');

        widgetField.appendChild(span);

        widget.appendChild(widgetField);
        widget.appendChild(widgetCalendar);

        document.getElementById(CalendarWorldcup.calendar_parser_id).appendChild(widget);
    },
    action: function(){
        var currentdate = $('#calendar_currentdate').val() == '' ? new Date() : new Date($('#calendar_currentdate').val());
        $('#widgetCalendar').DatePicker({
            flat: true,
            format: 'm/d/Y',
            date: (currentdate.getMonth() + 1) + '/' + currentdate.getDate() + '/' +  currentdate.getFullYear(),
            calendars: 1,
            mode: 'single',
            starts: 1,
            onChange: function(formated) {
                 // Xử lý ngày tháng
                $('#widgetField div').get(0).innerHTML = formated; //chinh gach noi - ngohai
                $('#widgetCalendar').find(".btn_calenda_dong").bind('click', function(){
                    $('#widgetCalendar').stop().animate({
                        height: 0
                    },500);
                    state = false;
                });
                var valueDate = $('#widgetField div').html();
                var d = new Date(valueDate);
                var weekday = new Array(7);
                weekday[0] = "Chủ nhật";
                weekday[1]=  "Thứ hai";
                weekday[2] = "Thứ ba";
                weekday[3] = "Thứ tư";
                weekday[4] = "Thứ năm";
                weekday[5] = "Thứ sáu";
                weekday[6] = "Thứ bảy";
                var n = weekday[d.getDay()];
                //show table
                var table_id = d.getDate()+'-'+(d.getMonth()+1)+'-'+d.getFullYear();
                $('.date_time').html(n+', '+d.getDate()+'/'+(d.getMonth()+1)+'/'+d.getFullYear());
                var showList = $('#list_'+ table_id);
                //console.log(divShow.length);
                if(showList.length)
                {
                    $('.list_win').hide();
                    showList.show();
                    $('.no_data').hide();
                }
                else
                {
                    $('.list_win').hide();
                    $('.no_data').show();

                }
                $('#widgetCalendar').stop().animate({
                    height: state ? 0 : $('#widgetCalendar div.datepicker').get(0).offsetHeight
                }, 500);
                state = !state;
            }
        });
        var state = false;
        $('#calendar').css('cursor', 'pointer').on('click', function(){
            $('#widgetCalendar').stop().animate({
                height: state ? 0 : $('#widgetCalendar div.datepicker').get(0).offsetHeight
                }, 500);
            state = !state;
            return false;
        });
		$('.date_time').css('cursor', 'pointer').click(function() {
			$('#calendar').trigger('click');
		});
        $('#widgetCalendar div.datepicker').css('position', 'absolute');
    }
};
var Team = {
    baseUrl : '',
    score   : 0,
    teamId  :0,
    maxLength       : 0,
    loadingBoxFixture   : false,
    tabContentEleID : '',
    error   : false,
    errorMsg: '',
    tabId   : 'info',
    type    :'',
    device  : device_env,
    listFixture: {
        data: null,
        next: 0,
        loadingData: false
    },
    init: function(options){
        this.baseUrl = options.baseUrl;
        this.currSegment = options.currSegment;
        this.teamId = options.teamId,
        this.tabContentEleID = options.tabContentEleID;
        this.maxLength = null;
        this.loadingBoxFixture = false;
        this.error = false;
        this.errorMsg = '';
        $.jCache.maxSize = 30;
    },
    changeTabContent: function(obj){
        var $window     = $(window),
            base_url    = $(obj).data('url');
        if (base_url !='undefined') {
            this.baseUrl = base_url;
        }
        if (this.device == 1)
        {
            $('.list_tab_mobile').hide();
            var title = $(obj).data('title');
            $('.tab_main_mobile').text(title);
        }
        $window.unbind();
        if ($(obj).hasClass('active'))
            return false;
        var tabId = $(obj).parent().attr('rel');
         this.tabId = tabId;
        var $tabContentObj = $('#' + tabId);
        if($tabContentObj.length && (tabId != 'tab_schedule'))
        {
            $($(obj)).parents('.tabs').find('.active').removeClass('active');
            $($(obj)).addClass('active');
            $('div.content').removeClass('active');
                        $('#'+ tabId).addClass('active');
            setTimeout(function(){
                $('#' + tabId).fadeIn('fast');
            }, 200);
        }
        else
        {
            $.ajax({
                type: 'get',
                url: this.baseUrl,
                data:{
                },
                dataType: 'json',
                //async: false,
                beforeSend: function(){
                },
                success:function(response) {
                    if (response.error)
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                    }
                    else
                    {
                        $($(obj)).parents('.tabs').find('.active').removeClass('active');
                        $($(obj)).addClass('active');
                        $('div.content').removeClass('active');
                        $('#'+ tabId).addClass('active');
                        setTimeout(function(){
                            $('#' + tabId).fadeIn('fast');
                        }, 400);
                        $('#tab_schedule').html(response.html);
                    }
                }
            });
        }
        return true;
    },
    nextBoxFixture: function(obj){
        var base_url    = $(obj).data('url'),
            score       = $(obj).data('score'),
            type        = $(obj).data('type'),
			team_id 	= $(obj).data('id');
        if (base_url !='undefined') {
            this.baseUrl = base_url;
        }
        this.score = score;
		$.ajax({
            type: 'get',
            url: this.baseUrl,
            data:{
                score: this.score,
                type: type,
				teamId: team_id
            },
            dataType: 'json',
            async: false,
            beforeSend: function(){
            },
            success: function(response) {
                if (response.error)
                {
                    Team.error = true;
                    Team.errorMsg = response.msg;
                }
                else
                {
                    $('.slider_top_team').html(response.html);
                }
            }
        });
    },
     nextTabContent: function(container, tabId) {
        var $container = $('#'+ container);
        $container.parents('.tabs').find('.active').removeClass('active');
        $container.addClass('active');
        $container.focus();
        $('div.content').removeClass('active');
        $('#'+ tabId).addClass('active');
        setTimeout(function(){
            $('#' + tabId).fadeIn('fast');
        }, 200);
    },
    changeTeam: function(teamid,flag){
		var selectId = flag==1?'box_player':'player_select';
        if(teamid == 0)
        {
            $('#' + selectId).hide();
        }
        else
        {
            $.ajax({
                type: 'get',
                url: base_url + '/du-doan/load-list-player',
                data:{
                    teamID:teamid
                },
                dataType: 'json',
                async: false,
                beforeSend: function(){
                },
                success: function(response) {
                    if (response.error)
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                        $('#' + selectId).hide();
                    }
                    else
                    {
						response.data.sort(function (a, b) {
						if (a.name > b.name)
						  return 1;
						if (a.name < b.name)
						  return -1;
						return 0;
						});
                        var arrOptions = '<option value="0">Chọn cầu thủ</option>';
                        $.each(response.data, function(index,value)
                        {
                            arrOptions += '<option value="' +value.id + '">' + value.name + '</option>';
                        });

                        $('#' + selectId).html(arrOptions).show();
                    }
                }
            });

        }
        return true;
    },
    nextBoxFixtureTop: function(obj){
        var base_url = $(obj).data('url'),
            score    = $(obj).data('score'),
            type     = $(obj).data('type'),
            self     = $(obj),
            dataEx   = $(obj).data('exclude'),
            loadingHTML = $('#image_load_ajax').html();
        if (base_url !='undefined') {
            this.baseUrl = base_url;
        }
        this.score = score;

        $.ajax({
            type: 'get',
            url: this.baseUrl,
            data:{
                score: this.score,
                type: type,
                exclude: dataEx
            },
            dataType: 'json',
            async: false,
            beforeSend: function(){
                $('#top_fixture_worldcup').html(loadingHTML);

            },
            success: function(response) {
                if (response.error)
                {
                    Team.error = true;
                    Team.errorMsg = response.msg;
                }
                else if(response.html != "")
                {
                    $('#top_fixture_worldcup').html(response.html);
                }
                else
                {
                    self.hide();
                }
            }
        });
    },
    mapScheduleBroadcast: function()
    {
        /**
         * Map match with lps tv
         */
        var arr_map_lps = {
            vtv3:{
                img:'<img src="'+img_url+'/icons/vtv3.gif">',
                match:[13292,13293,13296,13299,13300,13301,13335,13302,13304,13305,13306,13307,13310,13313,13314,13337,13318,13338,13320,13322,13324,13327,13328,13339,
                        ,13347,13349,13350,13351,13352,13353,13354,13356,13358,13359,13360,13361,13362]
            },
            vtv6:{
                img:'<img src="'+img_url+'/icons/vtv6.gif">',
                match:[13334,13294,13295,13332,13331,13298,13332,13333,13303,13336,13308,13309,13311,13312,13315,13316,13317,13319,13321,13323,13325,13326,13329,13330,13297,13348,
                    13355,13357]
            },
            sctv15:{
                img:'<img src="'+img_url+'/icons/sctv.gif">',
                match:[13292,13334,13293,13294,13295,13296,13332,13331,13298,13299,13332,13300,
                    13301,13333,13335,13302,13303,13304,13305,13336,13306,13307,13309,13310,13311,
                    13312,13313,13314,13315,13337,13316,13318,13338,13320,13322,13324,13327,13328,13339,13297,
                    ,13347,13349,13348,13350,13351,13352,13353,13354,13355,13356,13357,13358,13359,13360,13361,13362]
            },
            sctvthethao:{
                img:'<img src="'+img_url+'/icons/sctvthethao.gif">',
                match:[13317,13319,13321,13323,13325,13326,13329,13330,13308]
            },
            kplus:{
                img:'<img src="'+img_url+'/icons/kplus.gif">',
                match:[13292,13334,13293,13294,13295,13296,13332,13331,13298,13299,13332,13300,
                    13301,13333,13335,13302,13303,13304,13336,13306,13307,13309,13310,13311,13312,13313,
                    13314,13315,13337,13316,13317,13338,13320,13322,13324,13327,13328,13339,13297,
                    ,13347,13349,13348,13350,13351,13352,13353,13354,13355,13356,13357,13358,13359,13360,13361,13362]
            },
            kpluspm:{
                img:'<img src="'+img_url+'/icons/kpluspm.gif">',
                match:[13318,13319,13321,13323,13325,13326,13329,13330]
            },
            sports:{
                img:'<img src="'+img_url+'/icons/sport.gif">',
                match:[13305,13308]
            }
        };

        //parse source tv
        var dataMaps    = [];
        $('[data-schedule-match]').each(function() {
            var objectMap   = parseInt($(this).attr('data-schedule-match'));
            if ( $.inArray(objectMap, dataMaps) == -1 ) {
                dataMaps.push(objectMap);
            }
        });
        if ( dataMaps.length > 0 ) {
            $.each(dataMaps, function(index, match){
                var lps_tv  = [];
                $.each(arr_map_lps, function(obj, map){
                    if ( $.inArray(match, map.match) != -1 ) {
                        lps_tv.push(map.img);
                    }
                });
                if ( lps_tv.length > 0 ) {
                    $('[data-schedule-match="'+match+'"]').html(lps_tv.join(' '));
                }
            });
        }
    },
     nextListWinner: function(step)
    {
        var step_old = step - 1;
        $('#list_winner_'+ step_old).hide();
        $('#item_danhsach_'+ step).show();
        
    }
};
$(function(){
    thethao.init();
    Team.mapScheduleBroadcast();
    $(".btn_guess").click(function() {
        $("#goalteam1").focus();
    });    
});