define(['jquery', 'parser'], function(jQuery, Parsers) {
    var messages = {
        feedback_completed: 'Bạn đã gửi phản hồi thành công.',
        feedback_error: 'Có lỗi, hãy thử lại.',
        sendmail_completed: 'Bạn đã gửi email thành công.',
        sendmail_error: 'Có lỗi, hãy thử lại.',
        post_completed: 'Bạn đã gửi bài viết thành công.',
        post_error: 'Có lỗi, hãy thử lại.',
        share_completed: 'Bạn đã gửi chia sẻ thành công.',
        share_error: 'Có lỗi, hãy thử lại.',
        email_required: 'Email không được để trống.',
        email_invalid: 'Địa chỉ email không hợp lệ.',
        fullname_required: 'Họ tên không được để trống.',
        title_required: 'Tiêu đề không được để trống.',
        title_maxlength_error: 'Tiêu đề không được dài hơn 100 ký tự',
        description_required: 'Mô tả ngắn không được để trống.',
        content_required: 'Nội dung không được để trống.',
        content_minlength_error: 'Nội dung không được ít hơn 5 từ',
        post_content_required: 'Nội dung câu hỏi không được để trống.',
        captcha_required: 'Mã xác nhận không được để trống.',
        captcha_invalid: 'Mã xác nhận không đúng.'
    };
    var configs = $.extend({}, defaults, messages);

    //init parser to parser video, vote, rating, like
    var parser = new Parsers({
        siteId: configs.siteId,
        autoPlay: configs.autoPlay,
        vneUrl: configs.vneUrl,
        baseUrl: configs.baseUrl
    })
    //parser vote if have
    var containerVote = 'div[data-component-type="vote"]';
    var containerLike = 'div[data-component-type="like"]';
    var containerRating = 'div[data-component-type="rating"]';
    ($(containerVote).length) > 0 && parser.voteParser();
    ($(containerLike).length > 0) && parser.likeParser();
    ($(containerRating).length > 0) && parser.ratingParser();
});

define('cate',function () {
    var Category = function () {

        function stripTags(input, allowed) {
            allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
                commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
            return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        };
        function scrollToTop() {
            window.scrollTo(0, 0);
            return false;
        };
        function setClassEnd(container) {
            if (!container) {
                container = '.make_class_end';
            }
            $.each($(container), function (i, o) {
                $(o).children(':last').addClass('end');
            });
        };
        function noticeAlert(message) {
            var html_message = '<div id="login-vne4" class="login-form">'
                + '<div class="ttOline">Thông báo</div>'
                + '<div class="content_ligbox">'
                + '<div class="complete-form">'
                + '<div class="content_form">' + message + '</div>'
                + '</div>'
                + '</div>'
                + '<div class="close-lb"></div>'
                + '<div class="clear">&nbsp;</div>'
                + '</div>';
            Sexy.notice(html_message);
        };
        function initCommon() {

        };

        return {
            messages: messages,
            clock: clock,
            stripTags: stripTags,
            scrollToTop: scrollToTop,
            setClassEnd: setClassEnd,
            noticeAlert: noticeAlert,
            initCommon: initCommon,
            vneRecommendation: vneRecommendation,
            switchToDesktop: switchToDesktop,
            initLoadImage: initLoadImage
        };
    };
    return Category;
});