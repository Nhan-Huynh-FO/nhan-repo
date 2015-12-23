var tableRanking = {
    url : '',
    leagueId: 0,
    seasonId: 0,
    data:{
        leagues:[],
        seasons:[]
    },
    init: function(options){
        this.url = options.url;
        this.leagueId = options.leagueId;
        this.seasonId = options.seasonId;
        this.data = options.data;
    },
    changeTable: function(leagueId, seasonId){
        var newLink = this.url + '/bang-xep-hang/';
        newLink += this.data.leagues[leagueId];
        if(typeof seasonId != 'undefined')
            newLink += '-' + this.data.seasons[seasonId];
        newLink += '-' + leagueId;
        if(typeof seasonId != 'undefined')
            newLink += '-' + seasonId;
        else
            newLink += '-' + 0;
        newLink += '.html';
        window.location.href = newLink;
    }
}

var olympicRanking = {
    url: '',
    year: 0,
    currSort: '',
    init: function(options){
        this.url = options.url;
        this.year = options.year;
        this.currSort = options.currSort;
    },
    sort: function(by){
        if('undefined' == typeof by)
            by = 'gold';
        if(by == this.currSort)
            return false;
        this.currSort = by;
        var keyCache = 'olympic-' + this.currSort;
        var cachedContent = null;
        /* Get cache by jcache */
        try{
            cachedContent = $.jCache.getItem(keyCache);
        }
        catch(ex){
            cachedContent = null;
        }
        
        /* Miss cache  */
        if('undefined' == typeof cachedContent || null == cachedContent)
        {
            $.ajax({
                url: this.url + '/bang-xep-hang/olympic',
                type: 'get',
                data: 
                {
                    nam: this.year,
                    sort: this.currSort
                },
                dataType: 'json',
                async: false
            }).success(function(response){
                cachedContent = response;
                $.jCache.setItem(keyCache, response);
            });
        }
        
        if(!cachedContent.error)
        {
            /* Get table DOM */
            var table = $('#cate_bang_xep_hang').find('table');
            /*Delete row ranking*/
            table.find('tr.bg_le').remove();
            table.find('tr.bg_chan').remove();
            /* Append new DOM */
            table.append(cachedContent.html);
        }
        else
        {
            throw new Error(cachedContent.msg);
        }
        
        return true;
    }
}