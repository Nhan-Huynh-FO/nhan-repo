var Lightbox = {
    domain_image:'',
    selector: null,
    isInit: false,
    delay : null,
    type : "html", // default type
    init : function() {
        if ($('#box').length > 0) {
            $('#box').appendTo('body');
        } else {
            Lightbox.generate();
        }

        if ($("#box").css("position") == "absolute") {
            $(window).scroll(function () {
                $("#box").css("top", $(document).scrollTop() + "px");
            });
        }
        this.isInit = true;
    },
    setConfig: function(data) {
        if (typeof(data.selector) != 'undefined') {
            this.selector = data.selector;
        }

        if (data.type) {
            this.type = data.type;
        }
    },
    generate: function() {
        $('body').append('<div class="bigbox" id="box">'
            + '<iframe frameborder="0" class="windowmask"></iframe><div class="windowmask"></div>'
            + '<div class="contentbox lightbox contLightbox01" id="lightbox">'
            + '<div class="lbcont" id="lbcontent">'
			+ '<div class="lightbox-border">'
			+ '<table width="100%" border="0" cellspacing="0" cellpadding="0">'
			+ '<tr><td class="tl"></td><td class="b"></td><td class="tr"></td></tr>'
			+ '<tr><td class="b"></td><td class="bbody"><div class="lightbox-boxhead"><div class="tlc"><div class="trc"><div class="boxhead-content"><div class="right"><a href="javascript:;" onclick="Lightbox.hide()" ><img src="'+(Lightbox.domain_image || 'http://st.f1.thethao.vnexpress.net/i/v7')+'/graphics/blank_img.gif" alt="" height="1" width="1" class="close-btn" /></a></div><div class="boxhead-content-main"></div></div></div></div></div><div class="lightbox-boxbody"></div></td><td class="b"></td></tr>'
            + '<tr><td class="bl"></td><td class="b"></td><td class="br"></td></tr>'
            + '</table>'
			+ '</div>'
			+ '</div>'
			+ '</div>'
            );
    },
    setWidth: function(lbgWidth) {
        $('#lightbox').css('width', lbgWidth ? lbgWidth : 'auto');
    },
    setHeight: function(lbgHeight) {
        $('.lightbox-boxbody').css('height', lbgHeight ? lbgHeight : 'auto');
    },
    show : function(boxId,title, msgWidth, msgHeight) {

        $('.boxhead-content-main').html(title);

        if (this.type == 'iframe') {
            var url = boxId;
            if (!url){
                return false;
            } else {
                var width = (msgWidth) ? parseInt(msgWidth) - 20 : 430;
                var height = (msgHeight) ? parseInt(msgHeight) : 300;
                var html = "<iframe width='" + width + "' height='" + height + "' src='" + url + "' style='border: none' frameborder='0'></iframe>";
                $('.lightbox-boxbody').html(html);
            }
        } else {

            if (msgHeight) {
                this.setHeight(parseInt(msgHeight));
            }
            
            if(this.type == 'html') {
                $('.lightbox-boxbody').html($("#" + boxId).html());
            } else if(this.type == 'text') {
                $('.lightbox-boxbody').text($("#" + boxId).html());
            }// define more type here
        }
        
        
        Lightbox.setWidth(parseInt(msgWidth)+18 || 300);
        var elem = $('#lightbox');

        elem.parent(this).fadeIn(200);
        $(window).bind("resize", function(){
            Lightbox.setPos(elem);
        });
        
        Lightbox.setPos(elem);        

        if ($('.btngreen:first', elem).focus()[0] == undefined) {
            if ($('span.btngray:first > input', elem).focus()[0] == undefined) {
                $('input.cancel:first', elem).focus();
            }
        }
    },
    hide:function() {
        $('#lightbox').parent(this).fadeOut(200);
    },
    setPos: function(elem){
        
        var top = (document.documentElement.clientHeight - elem.height()) / 2;
        var left = (document.documentElement.clientWidth - elem.width()) / 2;
        if (top < 0) top = 0;
        if (left < 0) left = 0;
        elem.css({
            'left' : left + 'px',
            'top' : top + 'px'
        });
    },
    showemsg: function(title, content, namebtn){
        var errbox =
        '<div class="reg">'
        +   '<p>'+content+'</p>'
        +   '<table width="28%" border="0" cellspacing="0" cellpadding="0" style="margin:0px auto;" class="list-button">'
        +    '<tr>'
        +     '<td><div class="sidebutton"><div class="btn-left-02"><input name="huy" type="button" value="'+namebtn+'" class="nbutton" onclick="Lightbox.hide()"  /></div></div></td>'
        +    '</tr>'
        +   '</table>'
        +  '</div>';

        $('.boxhead-content-main').html(title);
        $('.lightbox-boxbody').html(errbox);

        Lightbox.setWidth(300+18);
        var elem = $('#lightbox');
        elem.parent(this).fadeIn(200);
        Lightbox.setPos(elem);
    }
};

$(document).ready(function(){
    if (!Lightbox.isInit) {
        Lightbox.init();
    }

    $("[lightbox]").click(function(){
        if (!$(this).attr("onclick")) {
            Lightbox.setConfig({
                selector: $(this),
                type: $(this).attr("rel") || "html"
            });
            var params = $.map($(this).attr("lightbox").split(","), $.trim);                                    
            Lightbox.show(params[0], params[1], params[2]);
        }
    });
});