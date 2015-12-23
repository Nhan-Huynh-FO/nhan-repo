define(function(){
    var AlbumFullscreen = (function(options) {
        var container;
        var cssUrl;
        var windowSize = {};
        var frameSize = {};
        var current = 0;
        var imgArr = '';
        var imgLeng = 0;
        var expandStatus = 0;
        var loadedSlide = 0;
        var articleId = 0;
        function init(options) {
            container   = options.container;
            cssUrl      = options.cssUrl;
            if ($('#popupslide').size() < 1) {
                $("body").append('<div id="popupslide" style="display:none;"></div>');
                $("#popupslide").append('<link href="' + cssUrl + '/slideshow/slide_show_gal.css" media="screen" rel="stylesheet" type="text/css"/>');
                var $html = '<div id="slide_show_gal" class="fullscreen"><div class="icon_close"><a href="javascript:;"></a></div><div class="content_gal"><div id="slide_gal" class="slide_gal sh_slide_left"><div id="galleria" class="galleria-container"></div><div class="showthumb"></div><div class="galleria-share-facebook" style="display: none;"></div>' +
                    // next and prev
                    '<div class="galleria-image-nav"><div class="galleria-image-nav-right">&nbsp;</div><div class="galleria-image-nav-left">&nbsp;</div></div>' +
                    // zoom in
                    '</div><div class="clear"></div></div><div class="bg_opacity_slide" style="display:none;"></div><div class="clear"></div>' +
                    /////////////////// SLIDE THUMB //////////
                    '<div id="slide_thumbs" style=""><div id="close_thumbs"></div><div class="galleria-thumbnails-container galleria-carousel"><div class="galleria-thumbnails-list slides_container"></div></div><div class="galleria-counter">Tất cả các ảnh (<span class="galleria-total">0</span>) </div></div>';

                $("#popupslide").append($html);

                //////////// touch for slide ////
                var script_2 = document.createElement("script");
                script_2.type = "text/javascript";
                script_2.src = cssUrl + '/slideshow/js/jquery.touchSwipe.min.js';
                document.body.appendChild(script_2);
                /////////// thumb ///////////
                var script_3 = document.createElement("script");
                script_3.type = "text/javascript";
                script_3.src = cssUrl + '/slideshow/js/jcarousellite_1.0.1.min.js';
                document.body.appendChild(script_3);
            }

            buildImage();
            resizeBox();

            $(window).resize(function() {
                resizeBox();
            });

            var btClose = '#popupslide .icon_close';
            var btShowThumb = '#slide_show_gal .showthumb';

            $(btClose)
                .unbind('click')
                .bind('click', function (event) {
                    close();
                    loadedSlide = 0;
                });

            $(btShowThumb)
                .unbind('click')
                .bind('click', function (event) {
                    showThumb();
                });

            $(document)
                .unbind('keydown')
                .bind('keydown', function(event){
                    if (event.keyCode == 39){showNext(event);}
                    if (event.keyCode == 37){showPrev(event);}
                    if (event.keyCode == 27){close();}
                });
            // next And prev button
            $("#popupslide .galleria-image-nav-left")
                .unbind('click')
                .bind('click', function (event) {
                    showPrev(event);
                });

            $("#popupslide .galleria-image-nav-right")
                .unbind('click')
                .bind('click', function (event) {
                    showNext(event);
                });
        }

        function buildImage() {
            var images = $(container).find('img');
            var blockid = $(container).parents('.block').attr('data-block-id');
            $(images).each(function (index) {
                $(this).css('cursor', 'url(' + cssUrl + '/slideshow/images/icons/zoom_cursor.png),auto');
                // gan id vao cho img
                $(this).attr('id', 'vne_slide_image_' + blockid + '_' + index);
                $(this).click(function (e) {
                    e.preventDefault();
                    showImage(index, e);
                });
            });
        }

        function getImage(evt) {
            var obj = $(evt.target);
            var myPic = new Array();
            var i = 0;

            var images = obj.parents('.' + $(container).attr('class')).find('img');
            ////////////// get META //////////////
            var name = 'tt_article_id';
            var b = document.getElementsByTagName("meta");
            if (b.length > 0) {
                for (var c = 0; c < b.length; c++) {
                    if (b[c].getAttribute("name") != null && b[c].getAttribute("name").toLowerCase() == name.toLowerCase()) {
                        articleId =  b[c].getAttribute("content");
                    }
                }
            }
            var $thumhtml = '<div class="galleria-thumb-item"><div id="galleria-thumb-jcarousellite">';
            $thumhtml += '<ul>';
            $(images).each(function (index) {
                $thumhtml += '<li class="galleria-image galleria-image-' + index + '"><img rel="' + index + '" src="' + $(this).attr("src") + '"></li>';
                myPic[index] = $(this).clone();
                i++;
            });
            $thumhtml += '</ul>';
            $thumhtml += '</div>';
            $thumhtml += '<div class="galleria-thumb-nav-left"></div><div class="galleria-thumb-nav-right"></div></div>';
            $("#popupslide .galleria-thumbnails-list").html($thumhtml);
            $("#popupslide .galleria-total").text(i);

            imgLeng = myPic.length;
            imgArr = myPic;
        }

        // next or prev button
        function showNext(e) {
            e.preventDefault();
            if (current < imgLeng) {
                showImage(parseInt(current) + 1);
            }
        }

        function showPrev(e) {
            e.preventDefault();
            if (current != 0) {
                showImage(parseInt(current) - 1);
            }
        }

        function showImage(index, evt) {
            if (evt) {
                getImage(evt);
                $('iframe').hide();
            }
            var self = this;
            $("html").css({'overflow-x': 'hidden', 'overflow-y' : 'hidden'});
            // nut loading
            $("#popupslide").prepend('<div class="galleria_loading"><img src="'+cssUrl+'/slideshow/images/icons/classic-loader.gif" alt="Loading"></div>');
            current = parseInt(index);
            //console.debug(current);
            // check size anh doc hay ngang
            var image = imgArr[index];
            var $src = image.attr('src');

            /*** RESIZE ANH **/
            $src = $src.replace('_180x108.', '_1200x0.');
            //console.debug($src);
            // them anh can load vao
            $('#galleria').html('<img style="max-width: 100%; max-height: 100%; display: none;" src="' + $src + '" />');
            var textinfo = image.attr('data-component-caption');
            var headerList = '';

            if (typeof(textinfo) != 'undefined') {
                textinfo = replacetext(textinfo);
                headerList = '<div id="galleria-info" data-show="1" class="galleria-info"><div class="galleria-info-text"><div class="galleria-info-description"><div class="text-too-lenght show_description_all"><span class="galleria-count-image-thumb">' + (current + 1) +'/' + imgLeng + '  |  </span>' + textinfo + '</div></div></div></div>';
            }
            else {
                headerList = '<div id="galleria-info" data-show="1" class="galleria-info"><div class="galleria-info-text"><div class="galleria-info-description"><div class="text-too-lenght show_description_all"><span class="galleria-count-image-thumb">' + (current + 1) +'/' + imgLeng + '</div></div></div></div>';
            }
            $('#galleria').append(headerList);
            ////////// kiem tra do cao ///////
            var fori = 1;
            var appint = setInterval(function(){
                var devheight = $(".text-too-lenght").height();
                //console.log(devheight + '- status: ' + self.expandstatus);
                if (devheight > 0 || fori > 5) {
                    if (devheight > 30){
                        $('.galleria-info-description').append('<div class="too-length-dot"> ...</div>');
                        if (expandStatus == 0) {
                            $('#galleria-info').prepend('<div class="view-more-too-lenght">&nbsp;</div>');
                            showViewMore();
                        } else {
                            $('#galleria-info').prepend('<div class="view-more-too-lenght show_icon_all">&nbsp;</div>');
                            $('.too-length-dot').hide();
                        }
                        $('#galleria-info .view-more-too-lenght').click(function() {
                            showViewMore();
                        });
                    }
                    clearInterval(appint);
                }
                fori ++;
            }, 100);

            // hien thi fullscreen
            $('#popupslide').show();

            // Not loaded yet, register the handler
            $('#galleria').find('img').load(function(){
                var hBoxLeft = frameSize.height;
                var hheight = $(this).height();
                var marTop = hBoxLeft/2 - hheight/2;
                //console.log(hwidth + '-' + hheight + '-' +hBoxLeft);
                if (marTop < 0) {marTop = 0;}

                // xoa nut loading + hien thi anh
                $('.galleria_loading').remove();
                $(this).css('margin-top', marTop+'px').fadeIn(500);
            });

            // neu la tablet
            if (device_env != 4){
                isTouchEnabled();
            }
            /// check nut next + prev
            if (current == 0) {
                $('.galleria-image-nav-left').hide();
            }
            else {
                $('.galleria-image-nav-left').show();
            }
            if (current == imgLeng){
                $('.galleria-image-nav-right').hide();
            }
            else {
                $('.galleria-image-nav-right').show();
            }
        }

        function resizeBox(){
            var documentWidth = $(window).width(); //retrieve current window width
            var documentHeight = $(window).height(); //retrieve current window height
            var framewidth = documentWidth - 80;
            var frameheight = documentHeight - 60;
            windowSize  = {"width" : documentWidth, "height" : documentHeight};
            frameSize   = {"width" : framewidth, "height" : frameheight};
            /////////// run resize khoang cach ///////////
            calculator();
        }

        function calculator() {
            $("#popupslide").find(".galleria-image-nav").css("top", (frameSize.height - 50) / 2);
            $("#galleria").height(frameSize.height);
        }


        // show text viewmore
        function showViewMore(){
            var status = parseInt($('#galleria-info').attr('data-show'));
            console.log(status);
            /// chua duoi ra
            if (status == 0) {
                $('#galleria-info').find('.text-too-lenght').addClass('show_description_all');
                $('#galleria-info').find('.view-more-too-lenght').addClass('show_icon_all');
                $('#galleria-info').find('.too-length-dot').hide();
                $('#galleria-info').find('.galleria-count-image-thumb').hide();
                $('#galleria-info').attr('data-show', 1);
                expandStatus = 1;
            } else {
                $('#galleria-info').find('.text-too-lenght').removeClass('show_description_all');
                $('#galleria-info').find('.view-more-too-lenght').removeClass('show_icon_all');
                $('#galleria-info').attr('data-show', 0);
                $('#galleria-info').find('.too-length-dot').show();
                $('#galleria-info').find('.galleria-count-image-thumb').show();
                expandStatus = 0;
            }

        }

        function isTouchEnabled() {
            //kiem tra thiet bi co touch ko ?
            var result = !!document.createTouch;
            //console.log(result);
            if (result) {
                //Enable swiping...
                $('#galleria').swipe({
                    //Generic swipe handler for all directions
                    swipe:function(event, direction, distance, duration, fingerCount) {
                        if (direction == 'left') {
                            showNext(event);
                        }
                        if (direction == 'right') {
                            showPrev(event);
                        }
                    },
                    tap:function(event, target) {
                        //testvar;
                        jQuery(target).trigger('click');
                    }
                });
            }
        }

        // hide thumb when click thumb pic
        function hideThumb() {
            //$("#slide_thumbs").hide();
            $('#slide_thumbs').animate({
                bottom:'-250px'
            }, 500).hide();
            $('.footer_gal li.viewall a').removeClass('hideslideshow');
            $('.footer_gal li.viewall a').text('Xem tất cả');
            $('#thumb-more').css('background-position', 'right top 0px');
            //$("#slide_thumbs").show();
            $('.bg_opacity_slide').hide();
            ///////// unbind slide //////
            $( "#galleria-thumb-jcarousellite" ).find("li").removeAttr("style");
        }

        // show thumb when click thumb pic
        function displayThumb() {
            //////////// update css ////////////
            $('#slide_thumbs').css('bottom', '-250px');
            $("#slide_thumbs").show();
            var bottomspace = 20;
            $('#slide_thumbs').animate({
                bottom: bottomspace
            }, 500);
            $('.bg_opacity_slide').show();

            /////////// slide phan xem tat ca //////////
            var visible = parseInt($(".galleria-thumb-item").width() / 160);
            visible = (visible < 1) ? 1 : visible;
            $("#galleria-thumb-jcarousellite").find(".galleria-image").removeClass("active");
            $("#galleria-thumb-jcarousellite").find(".galleria-image-" + current).addClass("active");
            console.log(visible, imgLeng);
            if (visible >= imgLeng)  {
                $(".galleria-thumb-nav-left").addClass("disabled");
                $(".galleria-thumb-nav-right").addClass("disabled");
                return false;
            }
            var owl = $('#galleria-thumb-jcarousellite').find('ul');
            owl.owlCarousel({
                autoPlay: false,
                items: 8,
                pagination: false
            });
            $('.galleria-thumb-nav-right')
                .unbind('click')
                .bind('click', function(){
                    owl.trigger('owl.next');
                });
            $('.galleria-thumb-nav-left')
                .unbind('click')
                .bind('click', function(){
                    owl.trigger('owl.prev');
                });
        }

        function showThumb() {
            $('#thumb-more').css('background-position', 'right top -4px');
            $('#thumb-more').css('margin-top', '6px');
            displayThumb();
            if (loadedSlide == 0) {
                slideThumb();
                loadedSlide = 1;
            }
        }

        function slideThumb() {
            $("#close_thumbs").bind("click", function () {
                hideThumb();
            });
            $("#popupslide #slide_thumbs .galleria-image").find("img")
                .unbind('click')
                .bind("click", function () {
                    showImage($(this).attr("rel"));
                    hideThumb();
                    $('.bg_opacity_slide').hide();
                });
            // resize image
        }

        // close slideshow
        function close() {
            $("html").css({'overflow-x': 'auto','overflow-y' : 'auto'})
            $("#popupslide").hide();
            $('iframe').show();
            hideThumb();
        }

        function replacetext(str){
            return (str + '').replace(/\\(.?)/g, function(s, n1) {
                switch (n1) {
                    case '\\':
                        return '\\';
                    case '0':
                        return '\u0000';
                    case '':
                        return '';
                    default:
                        return n1;
                }
            });

        }

        return {
            init: init
        };
    }());

    return AlbumFullscreen;
});