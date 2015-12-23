function Idle(config) {
    idler = this;
    idler.mouseTimeout = 0;
    idler.state = 1;
    this.Start = function () {
        $(document).bind('mousemove click', idler.Move);
        idler.Move();
    };
    this.Stop = function () {
        $(document).unbind('mousemove click', idler.Move);
        window.clearTimeout(idler.mouseTimeout);
    };
    this.Move = function () {
        if (idler.state == 0)
            config.onactive();
        idler.state = 1;
        window.clearTimeout(idler.mouseTimeout);
        idler.mouseTimeout = setTimeout(idler.Idled, config.timeout);
    };
    this.Idled = function () {
        idler.state = 0;
        config.onidle();
    }
}

var sh_fullscreen = {
    params:{
        title:'',
        lead:'',
        time:'',
        cssurl:'',
        pagelink:'',
        width:'',
        height:''
    },
    framesize:{
        width:0,
        height:0
    },
    current:0,
    imgarr:'',
    imgleng:0,
    imgsize:'',
    timeplay:'',
    thumbmargin:0,
    loadedslide:0,
    sliding:0,
    sh_type:'',
    container_width:0,
    createhtml:function () {
        //console.log(this.imgarr);
        $("body").append('<div id="popupslide" style="display:none;"></div>');
        $("#popupslide").append('<link href="' + this.params.cssurl + '/slideshow/slide_show_gal.css" media="screen" rel="stylesheet" type="text/css"/>');
        var $html = '<div id="slide_show_gal"><div class="icon_close"><a href="#"></a></div><div class="content_gal"><div id="slide_gal" class="slide_gal sh_slide_left"><div id="galleria" class="galleria-container"></div>' +
            // next and prev
            '<div class="galleria-image-nav"><div class="galleria-image-nav-right"></div><div class="galleria-image-nav-left"></div></div>' +
            // zoom in
            '<div class="galleria-bar"><div class="galleria-fullscreen" style="opacity: 1;"></div></div>' +
            '</div><div class="info_right sh_slide_right"><div class="bg_top"></div><div class="wrap_info"><div class="detail"><div class="title"><h2>' + this.params.title + '</h2><p class="time">' + this.params.time + '</p></div><p class="lead">' + this.params.lead + '</p><p class="quaylai"><a href="#">Quay lại bài viết</a></p></div>' +
            '<!--<div class="banner_300 w300"><a href="#"><img src="' + this.params.cssurl + '/slideshow/images/graphics/ads_300x250.jpg" align="absmiddle" alt=""/></a></div>--></div><div class="bg_bottom"></div></div><div class="clear"></div></div><div class="bg_opacity_slide" style="display:none;"></div><div class="clear"></div>' +
            /////////////////// SLIDE THUMB //////////
            '<div id="slide_thumbs" style=""><div id="close_thumbs"></div><div class="galleria-thumbnails-container galleria-carousel"><div class="galleria-thumbnails-list slides_container"></div></div><div class="galleria-counter">Tất cả ảnh ( <span class="galleria-total">9</span> ) </div></div>' +
            ///////////////////footer
            '<div class="footer_slideshow"><div class="footer_gal"><div class="sh_slide_left w450 logo"><div class="logo_slide sh_slide_left"><a href="' + this.params.vneurl + '"><img src="' + this.params.cssurl + '/slideshow/images/graphics/img_200x39.jpg" align="absmiddle" alt=""/></a></div><div class="link_func sh_slide_left"><ul><li class="viewall"><a href="#">Xem tất cả</a><div id="thumb-more"></div></li><li>|</li><li class="play_slide"><div class="galleria-play">Play</div></li></ul></div></div><div class="sh_slide_right list_share"><ul>' +
            '<li><iframe src="//www.facebook.com/plugins/like.php?href=' + this.params.pagelink + '&amp;send=false&amp;layout=button_count&amp;width=60&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></li>' +
            '<li><iframe allowtransparency="true" frameborder="0" scrolling="no" src="https://platform.twitter.com/widgets/tweet_button.html" style="width:85px; height:20px;"></iframe></li>' +
            '<li><g:plusone size="medium" annonation="none"></g:plusone></li>' +
            '</ul></div></div></div></div>';
        $("#popupslide").append($html);


        //JS for FullScreen Slideshow
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = this.params.jsurl + '/slideshow/jquery.fullscreen.js';
        document.body.appendChild(script);
        var script_2 = document.createElement("script");
        script_2.type = "text/javascript";
        script_2.src = this.params.jsurl + '/slideshow/jquery.scrollview.js';
        document.body.appendChild(script_2);
        var script_3 = document.createElement("script");
        script_3.type = "text/javascript";
        script_3.src = this.params.jsurl + '/slideshow/slides.jquery.js';
        document.body.appendChild(script_3);
    },
    run:function () {
        this.createhtml();
        this.setsize();
        this.getImage();
        var tempthis = this;

        $('#slide_gal').css('width', $('.galleria-container').css('width'));
        $("#popupslide .icon_close, #popupslide .quaylai").click(function () {
            sh_fullscreen.close();
            tempthis.pause();
            $("#popupslide .play_slide .galleria-play").removeClass('pause').text('Play');
        });
        // next And prev button
        $("#popupslide .galleria-image-nav-left").bind("click", function () {
            sh_fullscreen.showPrev();
        });
        $("#popupslide .galleria-image-nav-right").bind("click", function () {
            sh_fullscreen.showNext();
        });

        // play + pause  slide
        $("#popupslide .galleria-play").bind("click", function () {
            //sh_fullscreen.hideThumb();
            if ($(this).text() == 'Play') {
                sh_fullscreen.play();
                $("#popupslide .play_slide .galleria-play").addClass('pause').text('Pause');
            } else {
                sh_fullscreen.pause();
                $("#popupslide .play_slide .galleria-play").removeClass('pause').text('Play');
            }
        });
        window.onresize = function () {
            //sh_fullscreen.onresize();
            if(!($('#slide_show_gal').hasClass('fullscreen'))){
                sh_fullscreen.setsize();
            }
            sh_fullscreen.resizeBox();
        }
        $(".viewall a").click(function () {
            sh_fullscreen.showThumb();
        });
        // fullscreen button
        $("#popupslide .sh_slide_left").mouseenter(function () {
            $("#popupslide .galleria-bar").show();
        }).mouseleave(function () {
                $("#popupslide .galleria-bar").hide();
            });
        var tempThis = this;
        $("#popupslide .galleria-fullscreen").bind("click", function () {
            sh_fullscreen.fullscreen();
            tempThis.pause();
        });
        this.container_width = $('.galleria-container').css('width');
        $("#galleria").mouseenter(function () {
            if ($('#galleria-info').css('display') == 'none' && $('#slide_show_gal').attr('class') != 'fullscreen') {
                $('#galleria-info').fadeIn();
            }
        });
    },
    hideshare:function () {
        $("#popupslide .list_share").hide();
    },
    // get and save and set event image to array
    getImage:function () {
        var mypic = new Array();
        var mysize = new Array();
        var i = 0;
        var caption = '';
        // parser caption to image
        $("#article_content .tplCaption").each(function (index) {
            var caption = $.trim($(this).find(".Image").text());
            $(this).find("img").attr("data-component-caption", caption);
        });
        var $thumbwidth = $(window).width() - 55;
        //var numthumb = parseInt($thumbwidth / 155);
        var $thumhtml = '<div class="galleria-thumb-item">';
        var css_path = this.params.cssurl;
        var length_img = $("#article_content img").length;
        var thumbreset = 0;
        $("#article_content .item_slide_show img").each(function (index) {

            if (thumbreset <= $thumbwidth - 10) {
                var flag = 0;
            }
            else {
                var flag = 1;
                thumbreset = 0;
            }
            // slide image
            if (flag == 1) {
                $thumhtml += '</div><div class="galleria-thumb-item">';
            }
            $thumhtml += '<div class="galleria-image" onClick="sh_fullscreen.activeImg(this)"><img rel="' + index + '" src="' + $(this).attr("src") + '"></div>';


            mypic[index] = $(this).clone();
            $(this).css("cursor", 'url(' + css_path + '/slideshow/images/icons/zoom_cursor.png),auto');
            $(this).bind("click", function () {
                sh_fullscreen.showImage(index);
                if (index == 0) {
                    $('.galleria-image-nav-left').hide();
                    $('.galleria-image-nav-right').show();
                } else if (index + 1 == length_img) {
                    $('.galleria-image-nav-right').hide();
                    $('.galleria-image-nav-left').show();
                    // hien thi thumbnail nhu google o.O
                    sh_fullscreen.showThumb();
                }
            });
            var width = $(this).width();
            var height = $(this).height()
            mysize[index] = {width:width, height:height}
            var realwidth = (width >= height) ? 150 : (width / height * 150);
            thumbreset = thumbreset + realwidth + 20; //10 = margin
            i++;
        });
        $thumhtml += '</div>';
        $("#popupslide .galleria-thumbnails-list").append($thumhtml);
        this.imgleng = i - 1;
        this.imgarr = mypic;
        this.imgsize = mysize;
    },
    onresize:function () {
        if (this.sliding == 0) {
            return false;
        }
        if (this.type == 'fullscreen') {
            this.removeFullscreen();
        }
        else {
            this.setsize();
            this.showImage(this.current, 'onresize');
        }

    },
    play:function () {
        var temp = this;
        var timeplay = setTimeout(function () {
            temp.showNext();
            sh_fullscreen.play();
        }, 5000);
        this.timeplay = timeplay;
    },
    pause:function () {
        clearTimeout(this.timeplay);
    },
    // close slideshow
    close:function () {
        this.sliding = 0;
        if($('#slide_show_gal').hasClass('fullscreen')){
            $('#slide_show_gal').removeClass('fullscreen');
            sh_fullscreen.resizeBox();
            $('.info_right').show();
            $('.footer_slideshow').show();
        }
        $("#popupslide").hide();
        this.pause();
    },
    // set size for main frame.
    setsize:function () {
        var width = $(window).width();
        var height = $(window).height();
        var right = 330;

        var gw = width - right - 2 * 40 - 10 - 2 * 20; // 40: padding left + right; 10 padding width box right
        var gh = height - 20 - 80 - 10;
        if (gw < 120) {
            gw = 120;
            var popupw = 120 + right + 2 * 40 + 10 + 2 * 20;
            $("#popupslide").width(popupw);
        }
        else {
            $("#popupslide").width("100%");
        }
        gh = (gh < 50) ? 50 : gh;
        this.framesize = {width:gw, height:gh};

        $("#galleria").width(gw).height(gh-50);
        $("#popupslide .info_right").height(gh-50);
        /// remove like share
        if (width < 850) {
            $("#popupslide .list_share").hide();
        }
        else {
            $("#popupslide .list_share").show();
        }
        // set framesize;
        this.framesize.width = gw;
        this.framesize.height = gh;
        // nav margin
        $("#popupslide .galleria-image-nav").css("top", (gh / 2 - 50));
    },
    activeImg:function (obj) {
        $('.galleria-image').attr('class', 'galleria-image');
        $(obj).attr('class', $(obj).attr('class') + ' active');
    },
    // set image for main frame
    // wsize = 1 only change size, wsize = 2 only change image size
    showImage:function (index, type) {
        var idle = new Idle({
            onidle:function () {
                sh_fullscreen.idleTime();
            },
            onactive:function () {
                sh_fullscreen.activeTime();
            },
            timeout:3000
        });
        idle.Start();

        if (this.sliding == 0) {
            this.setsize();
        }
        this.sliding = 1;
        this.current = index;
        // check size anh doc hay ngang
        var image = this.imgarr[index];
        var tyle = this.framesize.width / this.framesize.height;
        var frame_width = this.framesize.width;
        var frame_height = this.framesize.height;
        //console.log(this.imgsize);
        var $src = image.attr("src");
        var imgw = '';
        var imgh = '';
        if (this.type == 'fullscreen') {
            $src = $src.replace("_500x0.", ".");
            this.scrollview();

        }
        else {
            if (this.imgsize[index].width / this.imgsize[index].height > tyle) {
                /// anh fix width
                imgw = frame_width;
                if (frame_width > this.imgsize[index].width) {
                    var $src = $src.replace("_500x0.", "_1000x0.");
                }
            }
            else {
                // anh fix height
                imgh = frame_height;
                if (frame_height > this.imgsize[index].height) {
                    var $src = $src.replace("_500x0.", "_0x700.");
                }
            }
        }
        if (this.type == 'fullscreen') {
            $("#popupslide #galleria").html('<div style="width:100%;height:100%" class="move_img"><img src = "' + $src + '"></div>').fadeIn(500, "linear");
            this.scrollview();
            $('.footer_slideshow').hide();
            //this.imgCenter($("#popupslide #galleria img"));
        }
        else if (!type) {
            // set current index image
            // add image to frame
            if ($("#popupslide #galleria img").length > 0) {
                $("#popupslide #galleria").find('img').hide();
                $("#popupslide #galleria img").attr('src', $src).fadeIn(500);
            } else {
                $("#popupslide #galleria").append('<img src="' + $src + '" />').fadeIn(500);
            }
            $("#galleria").find("img").css("max-width", frame_width).css("max-height", frame_height);
            $("#popupslide").show();
        }
        var pricedata = (typeof(image.attr("data-component-price")) != "undefined" ) ? '<div class="galleria-info-price">Giá: ' + image.attr("data-component-price") + '</div>' : "";
        //var titledata = (typeof(image.attr("alt")) != "undefined" ) ? '<div class="galleria-info-title">' + image.attr("alt") + '</div>' : "";
        var descdata = (typeof(image.attr("data-component-caption")) != "undefined" ) ? '<div class="galleria-info-description">' + image.attr("data-component-caption") + '</div>' : "";
        var info = '<div id="galleria-info" class="galleria-info"><div class="galleria-info-text">' /*+ titledata */+ descdata + pricedata + '</div></div>'
        if ($("#galleria-info").length) {
            $("#galleria-info").remove();
        }
        $("#galleria").append(info);
        if($("#galleria-info").find('.galleria-info-text').html() == ''){
            $("#galleria-info").remove();
        }
        // fix width height

        if (this.type == 'fullscreen') {
            //$("#galleria-info").hide();
            /* $("#galleria").find("img").bind("load", function(){
             var windowheight = $(window).height();
             var imgheight = $(this).height();
             // anh nho hon chieu cao window
             if (imgheight < windowheight){
             $(this).css("margin-top",(windowheight-imgheight)/ 2);
             }
             }); */
        }
        else {
            $("#galleria").find('img').on('load', function(){
                var hBoxLeft = $('#galleria').height(),
                    hImg = this.height,
                    marTop = hBoxLeft/2 - hImg/2;
                $("#galleria").find("img").css("max-width", frame_width).css("max-height", frame_height).css("margin-top", marTop+'px');
            });
        }
        $("#popupslide").show();
    },
    // next or prev button
    showNext:function () {
        var length_img = $("#article_content img").length;
        if (this.current == this.imgleng) {
            this.showImage(0);
            $('.galleria-image-nav-left').hide();
            $('.galleria-image-nav-right').show();
        }
        else {
            this.showImage(parseInt(this.current) + 1);
            if (this.current == 1) {
                $('.galleria-image-nav-left').show();
            } else if (this.current == this.imgleng) {
                $('.galleria-image-nav-right').hide();
                //sh_fullscreen.showThumb();
            }
        }
    },
    showPrev:function () {
        var length_img = $("#article_content img").length;
        if (this.current == 0) {
            this.showImage(this.imgleng);
        }
        else {
            if (this.current + 1 == length_img) {
                $('.galleria-image-nav-right').show();
            } else if (this.current == 1) {
                $('.galleria-image-nav-left').hide();
            }
            this.showImage(parseInt(this.current) - 1);
        }
    },
    removeFullscreen:function () {
        $("#slide_show_gal").removeClass("fullscreen");
        $("#galleria").removeAttr("style");
        $("#popupslide .galleria-fullscreen").removeClass("zoomin");
        //
        $('.info_right.sh_slide_right').show();
        $('#slide_gal').css('width', this.container_width);

        $('.galleria-play').removeClass('pause').text('Play');
        this.type = '';
        this.setsize();
        this.showImage(this.current);
        $('.footer_slideshow').show();
        // destroy
        $("#galleria").scrollview().destroy();
    },
    scrollview:function () {
        $("#galleria .move_img").scrollview({
            grab:this.params.cssurl + '/slideshow/images/icons/openhand.cur',
            grabbing:this.params.cssurl + '/slideshow/images/icons/closedhand.cur'
        });
    },
    fullscreen:function (index) {
        //$('#galleria-info').hide();
        //$('.galleria-info-text').css('display', 'none');
        if (this.type == 'fullscreen') {
            this.type = '';
            return this.removeFullscreen();
        }
        $("#popupslide .galleria-fullscreen").addClass("zoomin");
        //
        $('.info_right.sh_slide_right').hide();
        $('#slide_gal').css('width', '');

        this.type = 'fullscreen';
        index = (!index) ? this.current : index;
        $("#slide_show_gal").addClass("fullscreen");

        var width = $(window).width();
        var height = $(window).height();

        //$("#galleria").attr("style","vertical-align: middle; height: "+height+"px; width: "+width+"px; display: table-cell; overflow: inherit;");
        $("#galleria").attr("style", "width: 100%; height: 100%;");
        this.showImage(index, 'fullscreen');
    },

    // hide thumb when click thumb pic
    hideThumb:function () {
        //$("#slide_thumbs").hide();
        $('#slide_thumbs').animate({
            bottom:'-250px'
        }, 500);
        $('.footer_gal li.viewall a').removeClass('hideslideshow');
        $('.footer_gal li.viewall a').text('Xem tất cả');
        $('#thumb-more').css('background-position', 'right top 0px');
        $("#slide_thumbs").show();
        $('.bg_opacity_slide').hide();
    },
    // show thumb when click thumb pic
    displayThumb:function () {
        $('#slide_thumbs').css('bottom', '-250px');
        $("#slide_thumbs").show();
        $('#slide_thumbs').animate({
            bottom:'77px'
        }, 500);
        $('.bg_opacity_slide').show();
    },
    showThumb:function () {
        if ($('.footer_gal li.viewall a').hasClass('hideslideshow')) {
            $('.footer_gal li.viewall a').text('Thu gọn');
            $('#thumb-more').css('background-position', 'right top -4px');
            $('#thumb-more').css('margin-top', '6px');

            this.hideThumb();
            return false;
        }
        $('.footer_gal li.viewall a').addClass('hideslideshow');
        $('.footer_gal li.viewall a').text('Thu gọn');
        $('#thumb-more').css('background-position', 'right top -4px');
        $('#thumb-more').css('margin-top', '6px');

        this.displayThumb();
        if (this.loadedslide == 0) {
            this.slideThumb();
            this.loadedslide = 1;
        }
    },
    slideThumb:function () {
        $('#slide_thumbs').slides({
            preload:true,
            generatePagination:false,
            generateNextPrev:true,
            autoHeight:true,
            play:0,
            next:'galleria-thumb-nav-right',
            prev:'galleria-thumb-nav-left'
        });
        $("#popupslide .galleria-total").text(this.imgleng + 1);
        $("#close_thumbs").bind("click", function () {
            sh_fullscreen.hideThumb();
        });
        $("#popupslide .galleria-image").find("img").bind("click", function () {
            sh_fullscreen.showImage($(this).attr("rel"));
            sh_fullscreen.hideThumb();
            $('.bg_opacity_slide').hide();
        });
        // resize image
    },
    resizeImage:function (img, nsize) {
        var nWidth = img.width;
        var nHeight = img.height;
        nScale = nWidth / nHeight;
        //Check:
        if (nScale > 1) {
            nWidth = nsize;
            nHeight = (nWidth * img.height) / img.width;
            //fix vertical-align:
            nMargin = (nWidth - nHeight) / 2;
            //Set style to image:
            img.width = nWidth;
            img.height = nHeight;
            img.style.margin = nMargin + 'px 0px';
        }
        else {
            //Return size:
            nHeight = nsize;
            nWidth = (nHeight * img.width) / img.height;
            //fix align:
            nMargin = (nHeight - nWidth) / 2;
            //Set style to image:
            img.width = nWidth;
            img.height = nHeight;
            img.style.margin = '0px ' + nMargin + 'px';
        }
    },
    idleTime:function () {
        if (this.type != 'fullscreen') {
            if ($('.galleria-image-nav-right').css('display') == 'none') return false;

            $('#galleria-info').fadeOut();
            if ($('.footer_gal li.viewall a').text() == 'Thu gọn') {
                //sh_fullscreen.hideThumb();
                $('.footer_gal li.viewall a').addClass('idleTimer');
            }
        }
    },
    activeTime:function () {
        //$('#galleria-info').fadeIn();
        //if($('.footer_gal li.viewall a').hasClass('idleTimer')){
        //    sh_fullscreen.showThumb();
        //    $('.footer_gal li.viewall a').removeClass('idleTimer')
        //}
    },
    imgCenter:function (img) {
        var w_parent = $('#galleria').outerWidth(),
            h_parent = $('#galleria').outerHeight(),
            w_img = img.width(),
            h_img = img.height(),
            h_caption = $('#galleria-info').outerHeight();
        img.css({
            'margin-top':h_parent / 2 - h_img / 2 - h_caption,
            'margin-left':w_parent / 2 - w_img / 2
        });
    },
    resizeBox : function(){
        var wBoxParent = $('.content_gal').width(),
            wBoxRight = $('.info_right').width();
        var frame_width = this.framesize.width;
        var frame_height = this.framesize.height;
        var hBoxLeft = $('#galleria').height(),
            hImg = $('#slide_show_gal img').height(),
            marTop = hBoxLeft/2 - hImg/2;
        if(!($('#slide_show_gal').hasClass('fullscreen'))){
            $('#slide_gal').width(wBoxParent - wBoxRight -60);
            $("#galleria").find("img").css("max-width", frame_width).css("max-height", frame_height).css("margin-top", marTop+'px');
        }else{
            $("#galleria").find("img").css("margin-top", marTop+'px');
        }
    },
    getWidthHeightElementNone : function(el){
        el.on('load', function(){
            return this.height;
        });
    }
}