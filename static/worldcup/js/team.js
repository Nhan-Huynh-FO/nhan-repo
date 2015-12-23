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
                $('#' + tabId).fadeIn('fast')
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
                            $('#' + tabId).fadeIn('fast')
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
            $('#' + tabId).fadeIn('fast')
        }, 200);
    },
    changeTeam: function(teamid){
        this.currTeamID = teamid;
        this.currPlayerID = 0;
        if(teamid == 0)
        {
            $('#player_select').hide();
        }
        else
        {            
            $.ajax({
                type: 'get',
                url: this.baseUrl + '/du-doan/load-list-player',
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
                        $('#player_select').hide();
                    }
                    else
                    {
                        var arrOptions = '<option value="0">Chọn cầu thủ</option>';
                        $.each(response.data, function(index,value)
                        {
                            arrOptions += '<option value="' +value.id + '">' + value.name + '</option>';
                        })

                        $('#player_select').html(arrOptions).show();
                    }
                }
            });
            
        }
        return true;
    },
    nextBoxFixtureTop: function(obj){
        var base_url    = $(obj).data('url'),
            score       = $(obj).data('score'),
            type         = $(obj).data('type');
        if (base_url !='undefined') {
            this.baseUrl = base_url;
        }
        this.score = score;

        $.ajax({
            type: 'get',
            url: this.baseUrl,
            data:{
                score: this.score,
                type: type
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
                    $('.block_slder_top').html(response.html);
                }
            }
        });
    }
}