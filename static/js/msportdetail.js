//view news with select date time
var thethao = {
    init : function() {
        if (typeof(ArticleShare) != 'undefined') {
            ArticleShare.renderSocialPlugins();
            ArticleShare.SocialButtonLike();
        }
        common.initCommon();
        common.switchToDesktop();
    }
};
$(function(){
    thethao.init();
});