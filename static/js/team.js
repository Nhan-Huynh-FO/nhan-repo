var Team = {
    baseUrl : '',
    score   : 0,
    teamId  :0,
    currLeagueID    : 0,
    currSeasonID    : 0,
    boxFixtureEleID : '',
    tabContentEleID : '',
    ulCapDauEleID   : '',
    selectSeasonEleID   : '',
    maxLength       : 0,
    loadingBoxFixture   : false,
    loadingTabContent   : false,
    loadingListFixture  : false,
    loadingListSeason   : false,
    error   : false,
    errorMsg: '',
    arrLeagueSeasons    : null,
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
        this.boxFixtureEleID = options.boxFixtureEleID;
        this.tabContentEleID = options.tabContentEleID;
        this.ulCapDauEleID = options.ulCapDauEleID;
        this.selectSeasonEleID = options.selectSeasonEleID;
        this.teamId = options.teamId,
        this.maxLength = null;
        this.loadingBoxFixture = false;
        this.loadingTabContent = false;
        this.loadingListFixture = false;
        this.loadingListSeason = false;
        this.error = false;
        this.errorMsg = '';
        this.__hoverFixture();
        $.jCache.maxSize = 30;
    },
    __loadListFixture: function(leagueid, seasonid){
        if(this.loadingListFixture)
            return false;
        if('undefined' == typeof leagueid)
            leagueid = 0;
        if('undefined' == typeof seasonid)
            seasonid = 0;
        var keyCache = 'league:' + leagueid + ':season:' + seasonid,
            cachedContent = null,
            boxId = $('#befor_load_ajax');
        /* Get content by jcache */
        try
        {
            cachedContent = $.jCache.getItem(keyCache);
        } catch (ex) {
            cachedContent = null;
        }
        /* Miss cache */
        if(cachedContent == null || typeof cachedContent == 'undefined')
        {
            $.ajax({
                type: 'get',
                url: this.baseUrl,
                data:{
                    ajax: 'load-list-fixture',
                    leagueid: leagueid,
                    seasonid: seasonid
                },
                dataType: 'json',
                async: false,
                beforeSend: function(){
                    Team.loadingListFixture = true;
                    boxId.show();
                    boxId.html($('#image_load_ajax').html());
                }
            }).error(function(){
                Team.error = true;
                Team.errorMsg = 'Undefined error msg';
            })
            .success(function(response){
                if ('undefined' == typeof response || response == null){
                    Team.error = true;
                    Team.errorMsg = 'Has not response';
                }
                else
                {
                    if (response.error)
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                    }
                    else
                    {
                        cachedContent = response;
                        $.jCache.setItem(keyCache, response);
                    }
                }
            }).complete(function(){
                Team.loadingListFixture = false;
            });
        }

        if(this.error)
        {
            console.log(this.errorMsg)
        }
        else
        {
            boxId.hide();
            $('#' + this.ulCapDauEleID).html(cachedContent.html);
            this.listFixture.data = cachedContent.extend;
            this.listFixture.next = 1;
            this.listFixture.loadingData = false;
        }
        return true;
    },
    __hoverFixture: function(){
        $('#' + this.boxFixtureEleID).find('li').hover(function(){
            $(this).addClass('thisweek');
        }, function(){
            $(this).removeClass('thisweek');
        });
    },
    __changeBoxFixture: function(type){
        if(this.loadingBoxFixture)
            return false;

        var keyCache = 'box' + this.score + type,
            cachedContent = null,
            $box = $('#' + this.boxFixtureEleID + '_'+ this.tabId);
        /* Get content by jcache */
        try
        {
            cachedContent = $.jCache.getItem(keyCache);
        } catch (ex) {
            cachedContent = null;
        }
        /* Miss cache */
        if(cachedContent == null || typeof cachedContent == 'undefined')
        {
            $.ajax({
                type: 'get',
                url: this.baseUrl,
                data:{
                    ajax: 'load-box-fixture',
                    score: this.score,
                    teamId:this.teamId,
                    type: type
                    //matchIDs: this.arrMatchIDs[this.currSegment]
                },
                dataType: 'json',
                async: false,
                beforeSend: function(){
                    Team.loadingBoxFixture = true;
                    $box.html('Đang tải dữ liệu...');
                }
            }).error(function(){
                Team.error = true;
                Team.errorMsg = 'Undefined error msg';
            })
            .success(function(response){
                if ('undefined' == typeof response || response == null){
                    Team.error = true;
                    Team.errorMsg = 'Has not response';
                }
                else
                {
                    if (response.error)
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                    }
                    else
                    {
                        cachedContent = response;
                        $.jCache.setItem(keyCache, response);
                    }
                }
            }).complete(function(){
                Team.loadingBoxFixture = false;
            });
        }

        if(this.error)
        {
            console.log(this.errorMsg)
            this.currSegment -= step;
        }
        else
        {
            $box.fadeOut('fast', function(){
                $box.html(cachedContent.html).fadeIn('fast');
                Team.__hoverFixture();
            });
        }
        return true;
    },
    nextBoxFixture: function(obj){
        var base_url    = $(obj).data('url'),
            score       = $(obj).data('score'),
            type         = $(obj).data('type');
        if (base_url !='undefined') {
            this.baseUrl = base_url;
        }
        this.score = score;
        this.__changeBoxFixture(type);
    },
    changeTabContent: function(obj){
        var $window     = $(window),
            $document   = $(document),
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
        if(this.loadingTabContent)
            return false;
        var $parent = $(obj).parent();
        if ($parent.hasClass('active'))
            return false;
        var tabId = $parent.attr('rel');
        this.tabId = tabId;
        if(tabId == 'fixture')
        {
            $window.scroll(function(){
                var mayLoadContent = $document.height() - ($document.scrollTop() + $window.height()) <= 300 ? true : false;
                if(mayLoadContent && !Team.listFixture.loadingData){
                    Team.expandFixtureList();
                }
            });
        }
        var $tabContentObj = $('#' + tabId);
        if($tabContentObj.length)
        {
            $parent.parent().find('li').removeClass('active');
            $parent.addClass('active');
            $('div.tab_content_doibong').fadeOut('fast');
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
                    ajax: tabId
                },
                dataType: 'json',
                async: false,
                beforeSend: function(){
                    Team.loadingTabContent = true;
                }
            }).error(function(){
                Team.error = true;
                Team.errorMsg = 'Undefined error msg';
            })
            .success(function(response){
                if ('undefined' == typeof response || response == null){
                    Team.error = true;
                    Team.errorMsg = 'Has not response';
                }
                else
                {
                    if (response.error)
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                    }
                    else
                    {
                        $('#' + Team.tabContentEleID).append(response.html);
                        $parent.parent().find('li').removeClass('active');
                        $parent.addClass('active');
                        $('div.tab_content_doibong').fadeOut('fast');
                        setTimeout(function(){
                            $('#' + tabId).fadeIn('fast')
                        }, 400);
                        if(tabId == 'fixture')
                        {
                            Team.arrLeagueSeasons = response.ext;
                        }
                    }
                }
            }).complete(function(){
                Team.loadingTabContent = false;
                if(Team.error)
                    console.log(Team.errorMsg);
                else
                {
                    if(tabId == 'fixture')
                    {
                        Team.__loadListFixture();
                    }
                }
            });
        }
        return true;
    },
    changeLeague: function(leagueid){
        this.currLeagueID = leagueid;
        this.currSeasonID = 0;
        var $selectSeason = $('#' + this.selectSeasonEleID);
        if(leagueid == 0)
        {
            $selectSeason.parent().hide();
            this.__loadListFixture();
        }
        else
        {
            if(this.loadingListSeason)
                return false;
            var keyCache = 'league:' + leagueid;
            var cachedContent = null;
            /* Get content by jcache */
            try
            {
                cachedContent = $.jCache.getItem(keyCache);
            } catch (ex) {
                cachedContent = null;
            }
            /* Miss cache */
            if(cachedContent == null || typeof cachedContent == 'undefined')
            {
                $.ajax({
                    type: 'get',
                    url: this.baseUrl,
                    data:{
                        ajax: 'load-list-season',
                        arrSeasonIDs: this.arrLeagueSeasons[leagueid]
                    },
                    dataType: 'json',
                    async: false,
                    beforeSend: function(){
                        Team.loadingListSeason = true;
                    }
                }).error(function(){
                    Team.error = true;
                    Team.errorMsg = 'Undefined error msg';
                })
                .success(function(response){
                    if ('undefined' == typeof response || response == null){
                        Team.error = true;
                        Team.errorMsg = 'Has not response';
                    }
                    else
                    {
                        if (response.error)
                        {
                            Team.error = true;
                            Team.errorMsg = response.msg;
                        }
                        else
                        {
                            cachedContent = response;
                            $.jCache.setItem(keyCache, response);
                        }
                    }
                }).complete(function(){
                    Team.loadingListSeason = false;
                });
            }

            if(this.error)
            {
                console.log(this.errorMsg)
            }
            else
            {
                var arrOptions = '<option value="0">Chọn mùa đấu</option>';
                var sortedData = cachedContent.data.sort(function(a, b) {
                    if (a.name === b.name) {
                        return 0;
                    } else if (b.name > a.name) {
                        return 1;
                    }
                    return -1;
               });
                $.each(sortedData, function(a, b){
                    arrOptions += '<option value="' + b.id + '">' + b.name + '</option>';
                })

                $selectSeason.html(arrOptions).parent().show();
            }
        }
        return true;
    },
    changeSeason: function(seasonid){
        this.currSeasonID = seasonid;
        if(!seasonid)
            return false;
        this.__loadListFixture(this.currLeagueID, seasonid);
        return true;
    },
    expandFixtureList: function(){
        if(this.listFixture.loadingData || this.listFixture.next >= this.listFixture.data.length)
            return false;
        var keyCache = 'league:' + this.currLeagueID + ':season:' + this.currSeasonID + ':step:' + this.listFixture.next;
        var cachedContent = null;
        /* Get content by jcache */
        try
        {
            cachedContent = $.jCache.getItem(keyCache);
        } catch (ex) {
            cachedContent = null;
        }

        if('undefined' == typeof cachedContent || cachedContent == null)
        {
            $.ajax({
                url: this.baseUrl,
                data: {
                    ajax: 'expand-fixture-list',
                    arrMatchIDs: this.listFixture.data[this.listFixture.next]
                },
                dataType: 'json',
                async: false,
                type: 'get',
                beforeSend: function(){
                    Team.listFixture.loadingData = true;
                }

            }).error(function(){
                Team.error = true;
                Team.errorMsg = 'Undefined error msg';
            }).success(function(response){
                if('undefined' != typeof response)
                {
                    if(!response.error)
                    {
                        cachedContent = response;
                        $.jCache.setItem(keyCache, response);
                    }
                    else
                    {
                        Team.error = true;
                        Team.errorMsg = response.msg;
                    }
                }
            });
        }

        if(this.error){
            throw new Error(this.errorMsg);
        }
        else{
            $('#' + this.ulCapDauEleID).append(cachedContent.html);
            this.listFixture.next++;
            this.listFixture.loadingData = false;
        }
        return true;
    },
    nextTabContent: function(container, tabId) {
        var $container = $('#'+ container);
        this.tabId = tabId;
        $container.parent().find('li').removeClass('active');
        $container.addClass('active');
        $('div.tab_content_doibong').fadeOut('fast');
        setTimeout(function(){
            $('#' + tabId).fadeIn('fast')
        }, 200);
    }
}