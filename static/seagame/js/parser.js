define( function() {
    var Parsers = function(options) {
        var siteId = 0;
        var autoPlay = 0;
        var vneUrl = 'http://vnexpress.net';
        var baseUrl = '';
        function videoParser(){
            var playerId = 'fpt_player';
            var widthDefault = 480;
            var heightDefault = 270;
            //loop get video
            $('div[data-component-type="video"]').each(function(index) {
                try {
                    var videoIdComponent = this.getAttribute('data-component-value');
                    var videoIdType = this.getAttribute('data-component-typevideo') ? this.getAttribute('data-component-typevideo') : 1;
                    var linkVideo = vneUrl + '/parser_v3.php?id=' + videoIdComponent + '&amp;t=' + videoIdType + '&amp;ft=video' + '&amp;si=' + siteId + '&amp;ap=' + autoPlay + '&amp;ishome=0';
                    var object = '<div class="embed-container"><iframe width="' + widthDefault + '" height="' + heightDefault + '" src="' + encodeURI(linkVideo) + '" frameborder="0" allowfullscreen></iframe></div>';
                    $(this).replaceWith(object);
                }
                catch (e)
                {
                    console.log('error video parser ...');
                }
            });
        };

        function audioParser() {
            var playerID = 'fpt_player';
            $('div[data-component-type="audio"]').each(function() {
                try {
                    var audioLinkComponent = this.getAttribute('data-component-value');
                    if (audioLinkComponent) {
                        var title = this.getAttribute('data-title');
                        if(!title)
                        {
                            title = '';
                        }
                        var linkAudio = vneUrl + '/parser_v3.php?al=' + audioLinkComponent + '&amp;ft=audio' + '&amp;title=' + title;
                        var object = '<div class="embed-container audio"><iframe width="480" height="69" src="' + encodeURI(linkAudio) + '" frameborder="0" allowfullscreen></iframe></div>';
                        $(this).html(object).show().removeAttr('data-component-type').removeAttr('data-component-value').removeAttr('data-component');
                    }
                } catch (e) {
                    console.log('error audio parser');
                }
            });
        };
        function contentParser() {
            var $arrIframe = $(".fck_detail iframe");
            $arrIframe.each(function() {
                var w = $(this).width();
                var h = $(this).height();
                var percent = (h / w) * 100;
                if ($(this).parents(".embed-container").size() == 0) {
                    $(this).wrap('<div class="embed-container" style="padding-bottom: '+percent+'%;"></div>');
                }
            });
            var $arrTable = $(".embed-container");
            $arrTable.each(function() {
                if ($(this).parents('table').size() > 0) {
                    $(this).parents('table').removeAttr('width').attr('style', 'width: 100%');
                }
            });
            // set padding bottom = 0 with iframe class fptplay_embed
            var fptDom = $('.fptplay_embed');
            var fptDomSrc = fptDom.attr('src');
            if(typeof(fptDomSrc) != 'undefined' && fptDomSrc.indexOf('#width=') < 0)
            {
                var playerWidth = $('#left_calculator').width();
                var playerHeight = Math.ceil((playerWidth*3)/5);
                var playerUrl = fptDom.attr('src')+'#width='+playerWidth+'&height='+playerHeight;
                var playerPercent = (playerHeight / playerWidth) * 100;
                fptDom.parent().attr('style', 'padding-bottom:'+playerPercent+'%');
                fptDom.attr({src : playerUrl, width: playerWidth, height: playerHeight});
            }
        };

        function voteParser() {
            if ($('div[data-component-type="vote"]').length > 0) {
                addJsScript(baseUrl + '/interactions/vote.widget.js');
            }
        };
        function ratingParser () {
            if ($('div[data-component-type="rating"]').length > 0) {
                addJsScript(baseUrl + '/interactions/rating.widget.js');
            }
        };
        function likeParser () {
            if ($('div[data-component-type="like"]').length > 0 || $('div[data-component-type="likematch"]').length > 0) {
                addJsScript(baseUrl + '/interactions/like.widget.js');
            }
        };
        function addJsScript(jsFile) {
            var g = document.createElement('script');
            g.setAttribute('src', jsFile);
            g.setAttribute('type', 'text/javascript');
            g.setAttribute('charset', 'utf-8');
            document.getElementsByTagName('head')[0].appendChild(g);
        };

        function init(options) {
            siteId = options.siteId;
            autoPlay = options.autoPlay;
            vneUrl = options.vneUrl;
            baseUrl = options.baseUrl;
        };
        init(options);

        return {
            videoParser: videoParser,
            audioParser: audioParser,
            voteParser: voteParser,
            ratingParser: ratingParser,
            likeParser: likeParser,
            contentParser: contentParser
        };
    };
    return Parsers;
});