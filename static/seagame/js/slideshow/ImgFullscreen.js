/**
** @author :namtq
** @desc: slideshow site anh
*/
var sh_fullscreen = {
    params:{
        title:'',
        lead:'',
        width:'',
        height:'',
        masterial: '',
        started: 0,
        sharelink: ''
    },
    cssurl:'http://st.f1.vnecdn.net/responsive/js/utils/slideshow/css',
    //cssurl:'http://namtq1.st.v2.vnexpressdev.net/js/utils/slideshow/css',
    articleid: 0,
    windowsize: {},
    framesize: {},
    current:0,
    imgarr:'',
    imgleng:0,
    expandstatus: 0,
    loadedslide:0,
    detectdevide: 0,
    createhtml:function () {
        $("body").append('<div id="popupslide" style="display:none;"></div>');
        $("#popupslide").append('<link href="' + this.cssurl + '/slideshow/slide_show_gal.css" media="screen" rel="stylesheet" type="text/css"/>');
        var $html = '<div id="slide_show_gal" class="fullscreen"><div class="icon_close"><a href="javascript:;"></a></div><div class="content_gal"><div id="slide_gal" class="slide_gal sh_slide_left"><div id="galleria" class="galleria-container"></div><div class="showthumb"></div><div class="galleria-share-facebook"></div>' +
            // next and prev
            '<div class="galleria-image-nav"><div class="galleria-image-nav-right">&nbsp;</div><div class="galleria-image-nav-left">&nbsp;</div></div>' +
            // zoom in
            '</div><div class="clear"></div></div><div class="bg_opacity_slide" style="display:none;"></div><div class="clear"></div>' +
            /////////////////// SLIDE THUMB //////////
            '<div id="slide_thumbs" style=""><div id="close_thumbs"></div><div class="galleria-thumbnails-container galleria-carousel"><div class="galleria-thumbnails-list slides_container"></div></div><div class="galleria-counter">Tất cả ảnh (<span class="galleria-total">0</span>) </div></div>';
        $("#popupslide").append($html);

        //////////// touch for slide ////
        var script_2 = document.createElement("script");
        script_2.type = "text/javascript";
        script_2.src = this.cssurl + '/slideshow/js/jquery.touchSwipe.min.js';
        document.body.appendChild(script_2);
        /////////// thumb ///////////
        var script_3 = document.createElement("script");
        script_3.type = "text/javascript";
        script_3.src = this.cssurl + '/slideshow/js/jquery.flexslider-min.js';
        document.body.appendChild(script_3);
        
    },
    run:function () {
        this.createhtml();
        this.getImage();
        var tempthis = this;
        $("#popupslide .icon_close, #popupslide .quaylai").click(function () {
            sh_fullscreen.close();
        });
        // next And prev button
        $("#popupslide .galleria-image-nav-left").bind("click", function () {
            sh_fullscreen.showPrev();
        });
        $("#popupslide .galleria-image-nav-right").bind("click", function () {
            sh_fullscreen.showNext();
        });
        ///////////////////// show thumb /////////////
        $("#slide_show_gal").find(".showthumb").bind("click",function(){
            sh_fullscreen.showThumb();
        })
        // play + pause  slide
        /* 
        $("#popupslide .galleria-play").bind("click", function () {
            //sh_fullscreen.hideThumb();
            if ($(this).text() == 'Play') {
                sh_fullscreen.play();
            } else {
                sh_fullscreen.pause();
            }
        }); */
        $(window).resize(function() {
            sh_fullscreen.resizeBox();
        })
        $(".viewall a").click(function () {
            sh_fullscreen.showThumb();
        });
        // fullscreen button
        $("#popupslide .sh_slide_left").mouseenter(function () {
            $("#popupslide .galleria-bar").show();
        });
        // share facebook
        $("#popupslide").find(".galleria-share-facebook").bind("click",function(){
             sh_fullscreen.sharefacebook();
        });
        $(document).ready(function(){
            sh_fullscreen.resizeBox();
            if (sh_fullscreen.params.started > 0) {
                sh_fullscreen.showImage(sh_fullscreen.params.started - 1);
            }
            //sh_fullscreen.showImage(0);
        });
        /* $("#galleria").mouseenter(function () {
            if ($('#galleria-info').css('display') == 'none' && $('#slide_show_gal').attr('class') != 'fullscreen') {
                $('#galleria-info').fadeIn();
            }
        }); */
        
    },
    sharefacebook:function () {
        width = 500; height = 400;
        var objectid = parseInt(this.imgarr[this.current].attr("data-reference-id"));
        var leftPosition, topPosition;
        //Allow for borders.
        leftPosition = (this.windowsize.width / 2) - ((width / 2) + 10);
        //Allow for title and status bars.
        topPosition = (this.windowsize.height / 2) - ((height / 2) + 50);
        var windowFeatures = "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";
        u= location.href 
        if (this.params.sharelink) {
            u = this.params.sharelink + '/' + this.articleid + '/refer/' + objectid;
        }
        else {
            u = 'http://vnexpress.net/detail/photoshare/id/' + this.articleid + '/refer/' + objectid;
        }
        
        t=document.title;
        window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer', windowFeatures);
        return false;
    },
    // get and save and set event image to array
    getImage:function () {
        var mypic = new Array();
        var mysize = new Array();
        var i = 0;
        var caption = '';
        
        ////////////// get META //////////////
        var name = 'tt_article_id';
        var b = document.getElementsByTagName("meta");
        if (b.length > 0) for (var c = 0; c < b.length; c++) if (b[c].getAttribute("name") != null && b[c].getAttribute("name").toLowerCase() == name.toLowerCase()) var articleid =  b[c].getAttribute("content");
        this.articleid = articleid;
        // parser caption to image
        /********** KHONG DUNG TRONG BAI PHOTO ***********
        $("#article_content .tplCaption").each(function (index) {
            var caption = $.trim($(this).find(".Image").text());
            $(this).find("img").attr({"data-component-caption": caption});
        });
        *********************************/
        var $thumhtml = '<div class="galleria-thumb-item"><div id="galleria-thumb-jcarousellite">';
        var css_path = this.cssurl;
        var length_img = $("#article_content img").length;
        var thumbreset = 0;
        $thumhtml += '<ul class="slides">';
        $("#article_content img").each(function (index) {
            $thumhtml += '<li class="galleria-image galleria-image-' + index + '" onClick="sh_fullscreen.activeImg(this)"><img rel="' + index + '" src="' + $(this).attr("src") + '"></li>';
            // gan id vao cho img
            $(this).attr("id", "vne_slide_image_" + index);
            mypic[index] = $(this).clone();
            $(this).css("cursor", 'url(' + css_path + '/slideshow/images/icons/zoom_cursor.png),auto');
            $(this).on("click", function () {
                sh_fullscreen.open(index);
                ///////////// TRACKING /////////
                
                //_gaq.push(['_trackEvent', 'Detail_Album', articleid, 'Open Slide']);
                ////////////////////////////////
                /* if (index == 0) {
                    $('.galleria-image-nav-left').hide();
                    $('.galleria-image-nav-right').show();
                } else if (index + 1 == length_img) {
                    $('.galleria-image-nav-right').hide();
                    $('.galleria-image-nav-left').show();
                    // hien thi thumbnail nhu google o.O
                    sh_fullscreen.showThumb();
                }*/
            });
            /// apply voi icon phia tren anh
            $(this).parent().find(".btn_icon_show_slide_show").on("click",function(){
                sh_fullscreen.open(index);
            })
    
            i++;
        });
        $thumhtml += '</ul>';
        $thumhtml += '</div>';
        $thumhtml += '<div class="galleria-thumb-nav-left"></div><div class="galleria-thumb-nav-right"></div></div>';
        $("#popupslide .galleria-thumbnails-list").append($thumhtml);
        this.imgleng = i - 1;
        this.imgarr = mypic;
    },
    // close slideshow
    close:function () {
        $("html").css({'overflow-x': 'auto','overflow-y' : 'auto'});
		if (this.params.masterial){
            $("#slide_show_gal").find('.footer_slideshow').remove();
        }
        $("#popupslide").hide();
        //// an thumb
        this.hideThumb();
        $(document).off('keydown');
        /// scroll den anh dang xem
        /* $('html, body').animate({
            scrollTop: $("#vne_slide_image_"+ sh_fullscreen.current).offset().top
        }, 2000); */
        // 
    },
    /** Active anh thumb khi show **/
    activeImg:function (obj) {
        $('.galleria-image').removeClass('active');
        $(obj).addClass('active');
    },
    // ham nhan load lan dau khi moi popup
    open: function(index, type){
        $(document).keydown(function(event){
            if (event.keyCode == 39){
                sh_fullscreen.showNext();
            }
            if (event.keyCode == 37){
                sh_fullscreen.showPrev();
            }
            if (event.keyCode == 27){
                sh_fullscreen.close();
            }
        });
        this.showImage(index, type);
    },
    showImage:function (index, type) {
        var self = this;
        $("html").css({'overflow-x': 'hidden','overflow-y' : 'hidden'});
        // nut loading
        $("#popupslide").prepend('<div class="galleria_loading"><img src="'+this.cssurl+'/slideshow/images/icons/classic-loader.gif" alt="Loading"></div>');
        this.current = parseInt(index);
        // check size anh doc hay ngang
        var image = this.imgarr[index];
        var $src = image.attr("src");
        /*** RESIZE ANH **/
        $src = $src.replace("_660x0.", "_1200x0.");
        // them anh can load vao 
        $("#galleria").html('<img style="max-width: 100%; max-height: 100%; display: none;" src="' + $src + '" />');
        // caption anh
        $("#galleria-info").remove();
        var textinfo = image.attr("data-component-caption");
        textinfo = this.htmldecode(textinfo);
        var TextInfoLength = textinfo.length;
        
        if (TextInfoLength >= 3){
            $("#galleria").append('<div id="galleria-info" data-show="1" class="galleria-info"><div class="galleria-info-text"><div class="galleria-info-description"><div class="text-too-lenght show_description_all"><span class="galleria-count-image-thumb">' + (this.current + 1) +'/' + (this.imgleng + 1) + '  |  </span>' + textinfo + '</div></div></div></div>');
            ////////// kiem tra do cao ///////
            var fori = 1;
            var appint = setInterval(function(){
                var devheight = $(".text-too-lenght").height();
                //console.log(devheight + '- status: ' + self.expandstatus);
                if (devheight > 0 || fori > 5) {
                    if (devheight > 60){
                        $(".galleria-info-description").append('<div class="too-length-dot"> ...</div>');
                        if (self.expandstatus == 0) {
                            $("#galleria-info").prepend('<div class="view-more-too-lenght" onclick="sh_fullscreen.showviewmore();">&nbsp;</div>');
                            self.showviewmore();
                        }
                        else {
                            $("#galleria-info").prepend('<div class="view-more-too-lenght show_icon_all" onclick="sh_fullscreen.showviewmore();">&nbsp;</div>');
                            $(".too-length-dot").hide();
                        }
                    }
                    clearInterval(appint)
                }
                fori ++;
            }, 100);
        }
        // hien thi fullscreen
		/////////// khi co masterial /////////
        if (this.params.masterial && $('#slide_show_gal .footer_slideshow').size() < 1){
            $("#slide_show_gal").append('<div class="footer_slideshow"><div class="footer_gal">' +	this.params.masterial + 
			'</div></div></div>');
            $("#slide_show_gal").find(".content_gal").css("bottom",70);
        }
        $("#popupslide").show();
        
        // Not loaded yet, register the handler
        $("#galleria").find("img").load(function(){
            var hBoxLeft = sh_fullscreen.framesize.height;
            hheight = $(this).height();
            
            marTop = hBoxLeft/2 - hheight/2;
            //console.log(hwidth + '-' + hheight + '-' +hBoxLeft);
            if (marTop < 0) {marTop = 0}
            
            // xoa nut loading + hien thi anh
            $(".galleria_loading").remove();
            $(this).css("margin-top", marTop+'px').fadeIn(500);
        });
        
        //}
        // neu la tablet
        if (this.detectdevide == 0){
            this.isTouchEnabled();
        }
        /// check nut next + prev
        if (this.current == 0) {
            $('.galleria-image-nav-left').hide(); 
        }
        else {
            $('.galleria-image-nav-left').show(); 
        }
        if (this.current == this.imgleng){
            $('.galleria-image-nav-right').hide();
        }
        else {
            $('.galleria-image-nav-right').show();
        }
    },
    isTouchEnabled: function() { 
        /// kiem tra thiet bi co touch ko ?
        var result = !!document.createTouch; 
        //console.log(result);
        if (result) {
            $(function() {
              //Enable swiping...
              $("#galleria").swipe( {
                //Generic swipe handler for all directions
                swipe:function(event, direction, distance, duration, fingerCount) {
                  if (direction == 'left') {
                    sh_fullscreen.showNext(); 
                  }
                  if (direction == 'right') {
                    sh_fullscreen.showPrev();
                  } 
                },
                tap:function(event, target) {
                //testvar;
                    jQuery(target).trigger('click');
                }
              });
            });
        }
        this.detectdevide = 1;
    },
    // show text viewmore
    showviewmore: function(){
        var status = parseInt($("#galleria-info").attr("data-show"));
        //console.log(status);
        /// chua duoi ra
        if (status == 0) {
            $("#galleria-info").find(".text-too-lenght").addClass('show_description_all');
            $("#galleria-info").find(".view-more-too-lenght").addClass('show_icon_all');
            $("#galleria-info").find(".too-length-dot").hide();
            $("#galleria-info").find(".galleria-count-image-thumb").hide();
            $("#galleria-info").attr("data-show",1);
            this.expandstatus = 1;
        }
        else {
            $("#galleria-info").find(".text-too-lenght").removeClass('show_description_all');
            $("#galleria-info").find(".view-more-too-lenght").removeClass('show_icon_all');
            $("#galleria-info").attr("data-show",0);
            $("#galleria-info").find(".too-length-dot").show();
            $("#galleria-info").find(".galleria-count-image-thumb").show();
            this.expandstatus = 0;
        }
        
    },
    // next or prev button
    showNext:function () {
        var length_img = $("#article_content img").length;
        //console.log(this.current);
        if (this.current < this.imgleng) {
            this.showImage(parseInt(this.current) + 1);
        }
        // hien thi nut next
        //$('.galleria-image-nav-left').show();
        return true;
    },
    showPrev:function () {
        var length_img = $("#article_content img").length;
        if (this.current == 0) {
            //this.showImage(this.imgleng); // quay tron
        }
        else {
            this.showImage(parseInt(this.current) - 1);
        }
        //$('.galleria-image-nav-right').show();
    },
    // hide thumb when click thumb pic
    hideThumb:function () {
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
    },
    showThumb:function () {
        
        /*
        if ($('.footer_gal li.viewall a').hasClass('hideslideshow')) {
            $('.footer_gal li.viewall a').text('Thu gọn');
            $('#thumb-more').css('background-position', 'right top -4px');
            $('#thumb-more').css('margin-top', '6px');

            this.hideThumb();
            return false;
        }
        $('.footer_gal li.viewall a').addClass('hideslideshow');
        $('.footer_gal li.viewall a').text('Thu gọn'); */
        $('#thumb-more').css('background-position', 'right top -4px');
        $('#thumb-more').css('margin-top', '6px');
        
        if (this.loadedslide == 0) {
            $('#galleria-thumb-jcarousellite').flexslider({
                animation: "slide",
                itemWidth: 150,
                itemMargin: 10,
                animationLoop: false,
                directionNav: false,
                pauseOnAction: false,
                slideshow: false,
                controlNav: false,
                //slideshowSpeed: 4000,
                move: 1,
                start: function(slider) {
                    $('.galleria-thumb-nav-right').click(function(event){
                        event.preventDefault();
                        slider.flexAnimate(slider.getTarget("next"));
                    });
                    $('.galleria-thumb-nav-left').click(function(event){
                        event.preventDefault();
                        slider.flexAnimate(slider.getTarget("prev"));
                    });
                    
                    var start = 0;
                    $(".showthumb").bind("click",function(){
                        var visible = slider.visible;
                        var endimg = slider.last;
                        if (sh_fullscreen.current > endimg) {
                            start = endimg
                        }else {
                            start = sh_fullscreen.current; 
                        }
                        console.log(endimg);
                        slider.flexAnimate(start,true);
                    });
                    slider.flexAnimate(start,true);
                }
                
            });
            $("#popupslide .galleria-total").text(this.imgleng + 1);
            $("#close_thumbs").bind("click", function () {
                sh_fullscreen.hideThumb();
            });
            $("#popupslide .galleria-image").find("img").bind("click", function () {
                //console.log($(this).attr("rel"));
                sh_fullscreen.showImage($(this).attr("rel"));
                sh_fullscreen.hideThumb();
                $('.bg_opacity_slide').hide();
            });
            this.loadedslide = 1;
        }
        ////////////////////////////////////
        $('#slide_thumbs').css('bottom', '-250px');
        $("#slide_thumbs").show();
        var bottomspace = (this.params.masterial) ? 70 : 20;
        $('#slide_thumbs').animate({
            bottom: bottomspace
        }, 500);
        $('.bg_opacity_slide').show();
        $("#galleria-thumb-jcarousellite").find(".galleria-image").removeClass("active");
        $("#galleria-thumb-jcarousellite").find(".galleria-image-" + this.current).addClass("active");
    },
    resizeBox : function(){
        var documentWidth = $(window).width(); //retrieve current window width
        var documentHeight = $(window).height(); //retrieve current window height
        var framewidth = documentWidth - 80;
        var frameheight = (this.params.masterial) ? (documentHeight - 100) : (documentHeight - 60);
        this.windowsize = {"width" : documentWidth, "height" : documentHeight};
        this.framesize = {"width" : framewidth, "height" : frameheight};
        /////////// run resize khoang cach ///////////
        this.calculator();
        
    },
    calculator: function(){
        $("#popupslide").find(".galleria-image-nav").css("top", (this.framesize.height - 50) / 2);  
        $("#galleria").height(this.framesize.height); 
    },
    htmldecode: function(str) {
        return $('<div/>').html(str).text();
    },
    replacetext: function(str){
         return (str + '')
			.replace(/\\(.?)/g, function(s, n1) {
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
}