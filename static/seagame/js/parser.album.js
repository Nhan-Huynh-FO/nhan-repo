define(['albumFullScreen', 'carousel'], function(AlbumFullScreen){
    var ParserAlbum = function(options) {
        var siteId = 0;
        var vneUrl = 'http://vnexpress.net';
        var baseUrl = '';
        var platform = 1;

        function parse() {
            var strArticleId = '';
            var owlConfig = {
                autoPlay: 4000,
                items: 3,
                itemsDesktop: [1199, 3],
                itemsTablet: [600, 2], //2 items between 600 and 0
                itemsDesktopSmall: [900, 3], // betweem 900px and 601px
                itemsMobile: [479, 2],
                pagination: false
            };
            var slideConfig = {
                container: '',
                cssUrl: baseUrl + '/slideshow/css'
            };

            $('div[data-component-type="album"]').each(function() {
                var articleId = parseInt($(this).attr('data-component-value'));
                if (articleId > 0) {
                    strArticleId += ',' + articleId;
                    $(this).removeAttr('data-component-type');
                }
            });
            if (strArticleId.length > 0 || $('.list_slide .owl-carousel').size() > 0) {
                $.getJSON(vneUrl + '/parser_v3.php?id=' + strArticleId + '&ft=album&si=' + siteId + '&callback=?', function(response) {
                    if (!response.error) {
                        $.each(response.data, function(key, value) {
                            var container = $('#album-' + key).parents('.embed-container');
                            container.css({'height': 'auto', 'padding-bottom': '0'}).html(value);
                            var owl = container.find('.owl-carousel');
                            var next = container.find('.next_slider');
                            var prev = container.find('.prev_slider');

                            owl.owlCarousel(owlConfig);
                            next.click(function() {
                                owl.trigger('owl.next');
                            });
                            prev.click(function() {
                                owl.trigger('owl.prev');
                            });
                            if (platform != 1) {
                                next.show();
                                prev.show();
                                slideConfig.container = container.find('.list_slide');
                                new AlbumFullScreen.init(slideConfig);
                            } else {
                                next.hide();
                                prev.hide();
                                owl.find('a').unbind('click').click(function() {
                                    buildSlideMobile(owl);
                                    return false;
                                });
                            }
                        });
                    }
                });
                //parse chum slide
                $('.list_slide .owl-carousel').each(function() {
                    if (!$(this).hasClass('owl-theme')) {
                        var self = $(this);
                        var next = $(this).prevAll('.next_slider');
                        var prev = $(this).prevAll('.prev_slider');

                        $(this).owlCarousel(owlConfig);
                        next.click(function() {
                            self.trigger('owl.next');
                        });
                        prev.click(function() {
                            self.trigger('owl.prev');
                        });
                        if (platform != 1) {
                            next.show();
                            prev.show();
                            //init slide show mobile
                            slideConfig.container = self.parent();
                            //init slide show album
                            new AlbumFullScreen.init(slideConfig);
                        } else {
                            next.hide();
                            prev.hide();
                            $(this).find('a').unbind('click').click(function() {
                                buildSlideMobile(self);
                                return false;
                            });
                        }
                    }
                });
            }
        };
        function buildSlideMobile(obj) {
            var container = $('#slideshow_popup'),
                hrefClass = 'slide_mobile',
                classSwipebox = $('.' + hrefClass);

            var beginHTML = '<div id="main"><div class="wrap"><section id="exemple">';
            var endHTML = '<div class="clear">&nbsp;</div></section></div></div>';
            var bodyHTML = '';
            var total = $('img', $(obj)).size();

            $('img', $(obj)).each(function() {
                var href = $(this).parent().attr('href');
                href = href.replace(/(.[a-zA-Z]+)$/, '_m_460x0$1');
                var caption = caption_full = $(this).attr('data-component-caption');
                var show = 0;
                if (caption_full != '') {
                    var wordCount = caption_full.split(' ').length;
                    if (wordCount > 45) {
                        show = 1;
                        caption = cropWord(caption_full, 20);
                    } else {
                        show = 0;
                    }
                }
                bodyHTML += '<div class="box">'
                    + '<a class="'+hrefClass+'" href="' + href + '" rel="' + caption + '" relfull="' + caption_full + '" show="' + show + '" total="' + total + '"></a>'
                    + '</div>';
            });
            bodyHTML = beginHTML + bodyHTML + endHTML;
            container.html(bodyHTML).show();
            classSwipebox.swipebox();
        };
        function cropWord(str, numWord) {
            var wordCount = str.split(' ');
            var tmp = '';
            if (wordCount.length <= numWord) {
                return str;
            }
            for (var i = 0; i < numWord; i++) {
                tmp += wordCount[i] + (i < numWord - 1 ? ' ' : '...');
            }
            return tmp;
        };

        function init(options) {
            siteId = options.siteId;
            vneUrl = options.vneUrl;
            baseUrl = options.baseUrl;
            platform = options.platform;
        }
        init(options);

        return {
            parse: parse
        };
    };
    return ParserAlbum;
});