$(document).ready(function() {
    load();
    function load()
    {
        var href = window.location.href;
        var url = href.split('#');
        if (url[1] != undefined)
        {
            var obj = $('#' + url[1]);
            if (obj.length > 0)
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $("html, body").animate({
                    scrollTop: (obj.offset().top - 108)
                }, 900);
                //window.scrollTo(obj.position().left, obj.position().top);
                $('#td_' + url[1]).addClass('active');
            }
            else
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $('#td_' + url[1]).addClass('active');
            }
        }
        else
        {
            window.location.href = base_url + '/lich-thi-dau#' + dateNow;
            var obj = $('#' + dateNow);
            if (obj.length > 0)
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $("html, body").animate({
                    scrollTop: (obj.offset().top - 108)
                }, 900);
                //window.scrollTo(obj.position().left, obj.position().top - 68);
                $('#td_' + dateNow).addClass('active');
            }
            else
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $('#td_' + dateNow).addClass('active');
            }
        }
    }
    $('.tbl_corn_calendar td a').click(function(event) {
        event.preventDefault();
        var data_date = $(this).attr('data-date');
        if (data_date != undefined)
        {
            window.location.href = base_url + '/lich-thi-dau#' + data_date;
            var obj = $('#' + data_date);
            if (obj.length > 0)
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $("html, body").animate({
                    scrollTop: (obj.offset().top - 68)
                }, 900);
                //window.scrollTo(obj.position().left, obj.position().top - 68);
                $('#td_' + data_date).addClass('active');
            }
            else
            {
                $('.tbl_corn_calendar td').removeClass('active');
                $('#td_' + data_date).addClass('active');
            }
        }
    })
});