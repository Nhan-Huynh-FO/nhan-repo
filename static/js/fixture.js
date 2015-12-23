(function($){
    $.fn.extend({
        customStyle : function(options) {
            if(!$.browser.msie || ($.browser.msie&&$.browser.version>6)){
                return this.each(function() {
                    var currentSelected = $(this).find(':selected');
                    $(this).after('<span class="customStyleSelectBox"><span class="customStyleSelectBoxInner">'+currentSelected.text()+'</span></span>').css({
                        position:'absolute',
                        opacity:0,
                        fontSize:$(this).next().css('font-size')
                        });
                    var selectBoxSpan = $(this).next();
                    var selectBoxWidth = parseInt($(this).width()) - parseInt(selectBoxSpan.css('padding-left')) -parseInt(selectBoxSpan.css('padding-right'));
                    var selectBoxSpanInner = selectBoxSpan.find(':first-child');
                    selectBoxSpan.css({
                        display:'inline-block'
                    });
                    selectBoxSpanInner.css({
                        width:selectBoxWidth,
                        display:'inline-block'
                    });
                    var selectBoxHeight = parseInt(selectBoxSpan.height()) + parseInt(selectBoxSpan.css('padding-top')) + parseInt(selectBoxSpan.css('padding-bottom'));
                    $(this).height(selectBoxHeight).change(function(){
                        selectBoxSpanInner.text($(this).val()).parent().addClass('changed');
                    });
                });
            }
        }
    });
})(jQuery);

var fixture = {
    date        : '',
    leagueid    : 0,
    url         : '',
    group       : 0,
    id          : '',
    gmt         : 0,
    selectGmtEleID : '',
    displayGmtEleID : '',
    loadingData : false,
    loadGMTType: 0,
    init: function(options){
        if (typeof options.id != 'undefined')
            this.id = options.id;
        if (typeof options.date != 'undefined')
            this.date = options.date;
        if (typeof options.leagueid != 'undefined')
            this.leagueid = options.leagueid;
        if (typeof options.url != 'undefined')
            this.url = options.url;
        if (typeof options.group != 'undefined')
            this.group = options.group;
        if (typeof options.gmt != 'undefined')
            this.gmt = options.gmt;
        if (typeof options.loadGMTType != 'undefined')
            this.loadGMTType = options.loadGMTType;
        this.displayGmtEleID = options.displayGmtEleID;
        this.selectGmtEleID  = options.selectGmtEleID;
        $('select.styled').customStyle();
        setTimeout(function(){fixture.autoChange()}, 300);
        $('#' + this.selectGmtEleID).val(this.gmt).attr('selected', 'selected');
    },
    gotoTop: function(){
        $('html,body').animate({
            scrollTop: $("#" + this.id).offset().top - 25
        },'slow');
    },
    autoChange: function(){
        var parts = window.location.href.split('#!');
        if(parts.length > 1)
        {
            var temp = parts[1].split('/');
            this.date = temp[4];
            this.leagueid = temp[6];
            this.gmt = temp[8];
            this.group = temp[10];
            $( "#datepicker" ).datepicker( "setDate", this.date );
            this.__changeContent(this.url + parts[1]);
        }
    },
    __changeContent: function(){
        var url = this.url + '/lich-thi-dau/chitiet/ngay/' + this.date + '/lid/' + this.leagueid + '/gmt/' + this.gmt + '/g/' + this.group;
        var keyCache = url;
        var cachedContent = null;
        /* Get content by jcache */
        try
        {
            cachedContent = $.jCache.getItem(keyCache);
        } catch (ex) {
            cachedContent = null;
        }

        if (cachedContent == null || typeof cachedContent == 'undefined')
        {
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                async: false,
                beforeSend: function(){
                    fixture.loadingData = true;
                    $('#' + fixture.id).html('Đang tải dữ liệu. Vui lòng chờ trong giây lát...');
                }
            }).success(function(response){
                cachedContent = response;
                $.jCache.setItem(keyCache, response);
            }).complete(function(){
                fixture.loadingData = false;
            });
        }

        if (!cachedContent.error)
        {
            $('#' + this.id).html(cachedContent.html);
            $('#' + this.displayGmtEleID).html(this.gmt == 0 ? 'GMT' : 'Hà Nội');
            $('#' + this.selectGmtEleID).val(this.gmt).attr('selected', 'selected');
            window.location.href = '#!' + url.replace(this.url, '');
            $('select.styled').customStyle();
        }
        this.gotoTop();
    },
    changeLeague: function(lid, group)
    {
        if (typeof group == 'undefined')
            group = this.group;
        if (typeof lid == 'undefined')
            lid = this.leagueid;
        this.group = group;
        this.leagueid = lid;
        if (!this.loadingData){
            this.__changeContent();
            return true;
        }
        else
            return false;
    },
    changeDate: function(date)
    {
        this.date = date;
        if (!this.loadingData){
            this.__changeContent();
            return true;
        }
        else
            return false;
    },
    changeGmt: function(gmt)
    {
        this.gmt = gmt;
        if(!this.loadingData){
            if(this.loadGMTType == 0)
                this.__changeContent();
            else
                window.location.href = '?gmt=' + this.gmt;
            return true;
        }
        return false;
    },
    showHideBlockFixture: function(obj)
    {
        var contentCapdau = $(obj).parent().parent().find('.list_capdau');
        var bangdau = $(obj).attr('rel');
        if(contentCapdau.css('display') == 'block'){
            contentCapdau.slideUp();
            $(obj).find('span').removeClass('active').text('Xem lịch thi đấu bảng ' + bangdau);
        }
        else{
            contentCapdau.slideDown();
            $(obj).find('span').addClass('active').text('Ẩn lịch thi đấu bảng ' + bangdau);
        }
    }
};