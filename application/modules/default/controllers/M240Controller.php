<?php

class M240Controller extends Zend_Controller_Action
{

    /**
     * Index Action
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        // Get block cate
        $intCateId = SITE_ID;

        // get current page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);

        //get Instance News
        $objNews = $this->view->objArticle;

        // get news model
        $newsModel = Thethao_Model_News::getInstance();

        // Params get news by rule 2
        $arrParams = array(
            'category_id' => $intCateId,
            'limit' => LIMIT_LIST_NEWS,
            'offset' => ($intPage - 1) * LIMIT_LIST_NEWS,
            'article_type' => TYPE_ALL
        );

        //********************* LIST PAGING *************************//
        $arrListPagingNews = $newsModel->getListArticleIdsByRule2($arrParams);

        //set title paging
        $titlePaging = '';

        //Check total page
        if ($intPage > 1 && $arrListPagingNews['total'] > 0)
        {
            $maxPage = ceil($arrListPagingNews['total'] / LIMIT_LIST_NEWS);
            //check
            if ($intPage > $maxPage)
            {
                $this->_redirect($this->view->configView['url'] . '/page/' . $maxPage . '.html');
            }//end if

            $titlePaging = ' - Trang ' . $intPage;
        }//end if
        // check to set id basic + set first news
        $firstNews = 0;
        if (!empty($arrListPagingNews['data']))
        {
            // set id from $arrListPagingNews to get article
            $objNews->setIdBasic($arrListPagingNews['data']);

            //set first news
            $firstNews = array_shift($arrListPagingNews['data']);
        }//end if
        //set param paging
        $arrParamPaging = array(
            'total' => $arrListPagingNews['total'],
            'current' => $intPage,
            'perpage' => LIMIT_LIST_NEWS,
            'url' => $this->view->configView['url'] . '/page',
            'extEnd' => '.html',
        );

        //********************* Assign to view *************************//
        $this->view->assign(array(
            'intCategoryId' => $intCateId,
            'firstNews' => $firstNews,
            'arrListPagingNews' => $arrListPagingNews['data'],
            'arrParamPaging' => $arrParamPaging
        ));

        // Prepend title
        $this->view->headTitle()->prepend('Tin tức giải trí 24h, showbiz, sao Việt & Thế Giới' . $titlePaging . ' - VnExpress Giải Trí');
        $this->view->headMeta()->setName('description', 'Chuyên trang thông tin giải trí, tin tức âm nhạc, hậu trường sân khấu, kịch, điện ảnh, lịch chiếu phim, trailer phim chiếu rạp mới nhất')
            ->setName('keywords', 'Tin tức giải trí 24h, showbiz, sao Việt & Thế Giới' . $titlePaging . ' - VnExpress Giải Trí');
    }

    /**
     * Category Action
     */
    public function categoryAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //get link info
        $strLink = $this->_request->getPathInfo();
        $strLink = rtrim($strLink, '/');

        //get cate code
        $cateCode = strtolower($this->_request->getParam('cate_code', ''));

        //check subfoder goc-nhin to redirect sau-man-anh
        if ($cateCode == 'goc-nhin')
        {
            $this->_redirect($this->view->configView['url'] . '/tin-tuc/phim/sau-man-anh', array('code' => 301));
        }//end if
        //check subfoder goc-nhin-am-nhac to redirect lang-nhac
        if ($cateCode == 'goc-nhin-am-nhac')
        {
            $this->_redirect($this->view->configView['url'] . '/tin-tuc/nhac/lang-nhac', array('code' => 301));
        }//end if
        //get instance Cate
        $modelCate = Thethao_Model_Category::getInstance();

        //get cate id by cate code
        $intCateId = $modelCate->getIdByCode($cateCode);

        //check cate id is not exists
        if ($intCateId <= 0)
        {
            //redirect home page
            $this->_redirect("/");
        }//end if
        // Get Category detail
        $cateDetail = $modelCate->getDetailByCateId($intCateId);

        //redirect link to redirect
        if (strpos($strLink, $cateDetail['link']) === FALSE)
        {
            $this->_redirect($cateDetail['link'], array('code' => 301));
        }//end if
        //set cate to param
        $this->_request->setParam('cateid', $intCateId);

        //Get page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);

        //Init Object News
        $modelNews = Thethao_Model_News::getInstance();

        //Init params
        $arrParamsRule2 = array(
            'category_id' => $intCateId,
            'limit' => LIMIT_LIST_NEWS,
            'offset' => ($intPage - 1) * LIMIT_LIST_NEWS,
            'article_type' => TYPE_ALL
        );

        //********************* LIST PAGING *************************//
        //get news by rule 2
        $arrListPagingNews = $modelNews->getListArticleIdsByRule2($arrParamsRule2);

        //Set title paging
        $titlePaging = '';

        //Check total page
        if ($intPage > 1 && $arrListPagingNews['total'] > 0)
        {
            $maxPage = ceil($arrListPagingNews['total'] / LIMIT_LIST_NEWS);
            //check if page > max page then return to max page
            if ($intPage > $maxPage)
            {
                //redirect
                $this->_redirect($cateDetail['link'] . '/page/' . $maxPage . '.html');
            }//end if

            $titlePaging = ' - Trang ' . $intPage;
        }//end if
        //set id basic list paging + set first news
        $firstNews = 0;
        if (!empty($arrListPagingNews['data']))
        {
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrListPagingNews['data']);

            //set first news
            $firstNews = array_shift($arrListPagingNews['data']);
        }//end if
        //Switch CateId: declare title meta + description meta
        switch ($intCateId)
        {
            case BLOCK_CATE_GIOISAO:
                $titleMeta = $keywordMeta = 'Tin tức sao, giới sao, hậu trường showbiz' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tin tức sao quốc tế, sao Việt, tin hot, tin hậu trường, đời sống của sao được cập nhật liên tục tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TRONGNUOC:
                $titleMeta = $keywordMeta = 'Sao Việt, mới nhất tuần qua, clip, ảnh' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Scandal sao, Việt Nam, sao Việt, clip hot, với các thông tin được cập nhật hàng ngày và liên tục tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_QUOCTE:
                $titleMeta = $keywordMeta = 'Sao Thế Giới, Hollywood, Mỹ, Hàn Quốc, Trung Quốc' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Scandal sao thế giới, clip hot, với các thông tin được cập nhật hàng ngày và liên tục tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_FASHION:
                $titleMeta = $keywordMeta = 'Thời trang, mốt thời trang, Xu hướng, phong cách thời trang' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Cập nhật các xu hướng thời trang mới nhất, với nhiều phụ kiện, phong cách đặc biệt, cá tính của làng thời trang Việt và thế giới';
                break;
            case BLOCK_CATE_LANGMOT:
                $titleMeta = $keywordMeta = 'Mốt thời trang, mốt model mới, xuân, hạ, thu, đông' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các mốt thời trang dạ hội, thời trang áo tắm, thời trang thu đông, thời trang mới nhất được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_COLLECTION:
                $titleMeta = $keywordMeta = 'Bộ sưu tập thời trang, nhà thiết kế, bộ sưu tập đẹp' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các bộ sưu tập thời trang, các phong cách thời trang. Các kiểu đồ đẹp, mix đồ thời trang được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_SAODEPSAOXAU:
                $titleMeta = $keywordMeta = 'Sao đẹp, sao xấu, top các sao nổi tiếng Việt Nam, quốc tế' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Phong cách thời trang, bình luận về các sao đẹp, sao xấu ở Việt Nam, quốc tế, thông tin được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_LAMDEP:
                $titleMeta = $keywordMeta = 'Làm đẹp, bí quyết trang điểm, cách làm đẹp da, mặt, tóc' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Chuyên trang tư vấn, hướng dẫn làm đẹp, trang điểm, mặt, làm tóc đẹp, và các bí quyết làm đẹp dành cho phụ nữ';
                break;
            case BLOCK_CATE_MYPHAM:
                $titleMeta = $keywordMeta = 'Mỹ phẩm, tư vấn mỹ phẩm làm đẹp da, tóc' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Chuyên trang thông tin, bí quyết sử dụng mỹ phẩm của các nước Mỹ, Hàn Quốc cùng các tính năng, công dụng của mỹ phẩm';
                break;
            case BLOCK_CATE_TRILIEU:
                $titleMeta = $keywordMeta = 'Trị liệu làm đẹp da, tóc, trị liệu thư giãn' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Chuyên trang thông tin trị liệu, làm đẹp da, trị liệu thư giãn, các cách và bí quyết giúp thư giãn tinh thần';
                break;
            case BLOCK_CATE_KINHNGHIEM:
                $titleMeta = $keywordMeta = 'Kinh nghiệp làm đẹp, tư vấn thông tin làm đẹp mỗi ngày' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Chuyên trang chia sẻ thông tin, cẩm nang làm đẹp kinh nghiệp làm đẹp hay, tư vấn làm đẹp cho phụ nữ hàng ngày';
                break;
            case BLOCK_CATE_PHIM:
                $titleMeta = $keywordMeta = 'Phim ảnh, tin tức phim ảnh, phim chiếu rạp' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tin tức phim ảnh, phim điện ảnh xưa và nay, thông tin hậu trường, cảnh nóng phim, các tin điện ảnh mới nhất được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_DIEMPHIM:
                $titleMeta = $keywordMeta = 'Điểm phim mới, điểm phim hay, HBO, Hàn Quốc, Mỹ, Việt Nam' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Điểm phim, cập nhật các phim mới và nổi tiếng, các phim chiếu trên truyền hình thông tin được cập nhật nhanh nhất tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_SAUMANANH:
                $titleMeta = $keywordMeta = 'Hậu trường phim, phía sau màn ảnh, bí mật phim trường' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Góc nhìn điện ảnh, hậu trường điện ảnh, phía sau màn ảnh được cập nhật nhanh nhất tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TRUONGQUAY:
                $titleMeta = $keywordMeta = 'Tin tức trường quay, thông tin hậu trường, thú vị hậu trường' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tin tức và thông tin trường quay, sau màn ảnh, bí mật làm phim được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_CINE:
                $titleMeta = $keywordMeta = 'Biểu tượng màn bạc, huyền thoại màn bạc' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Biểu tượng điện ảnh xưa và nay, các huyền thoại màn bạc được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_GOCNHIN:
                $titleMeta = $keywordMeta = 'Góc nhìn chuyên gia, tư vấn làm phim, hậu kỳ' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các bình luận về điện ảnh, các góc nhìn đánh giá điện ảnh, phim ảnh Việt, quốc tế được cập nhật tại chuyên trang Giải Trí VnExpress';
                break;
            case BLOCK_CATE_TRUYENHINH:
                $titleMeta = $keywordMeta = 'Lịch truyền hình online, Truyền hình thực tế' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Lịch truyền hình hôm nay, lịch mới nhất, các chương trình truyền hình thực tế được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_SACH:
                $titleMeta = $keywordMeta = 'Sách hay, truyện hay, tác phẩm hay, văn học đa thể loại' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Sách hay, truyện hay, các thể loại văn học, tác phẩm để đời được giới thiệu và cập nhật tại chuyên trang Giải Trí VnExpess';
                break;
            case BLOCK_CATE_LANGVAN:
                $titleMeta = $keywordMeta = 'Đời sống văn học nghệ thuật, chuyện làng văn' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Đời sống văn học nghệ thuật, phê bình, tin tức văn học được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_DIEMSACH:
                $titleMeta = $keywordMeta = 'Tin tức sách, sách hay, sách mới, sách điện tử' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các tác phẩm, tác giả, sách hay, sách mới được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TACPHAM:
                $titleMeta = $keywordMeta = 'Tác phẩm, tác giả, sách hay, sách nổi tiếng' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tác phẩm, tác giả, sách hay, nổi tiếng, sáng tác mới được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_THO:
                $titleMeta = $keywordMeta = 'Tác phẩm thơ hay, sáng tác mới' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các tác phẩm thơ hay, sáng tác mới được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TRUYENNGAN:
                $titleMeta = $keywordMeta = 'Tác phẩm truyện ngắn hay, tác giả truyện ngắn' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tác phẩm truyện ngắn hay, tác giả truyện ngắn được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TANVAN:
                $titleMeta = $keywordMeta = 'Tản văn, câu chuyện đời thường, đời sống' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tản văn, câu chuyện đời thường, đời sống được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TIEUTHUYET:
                $titleMeta = $keywordMeta = 'Tiểu tuyết dài kỳ, đăng nhiều kỳ' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tiểu thuyết văn học, tiểu thuyết dài kỳ được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_KY:
                $titleMeta = $keywordMeta = 'Ký, hồi ký, truyện ký, ký sự' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Ký, hồi ký, truyện ký, ký sự được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_NHAC:
            case BLOCK_CATE_CAMXUCAMNHAC:
                $titleMeta = $keywordMeta = 'Nhạc, bài hát hay, cảm xúc âm nhạc' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Nhạc hay, bài hát, bình luận ca nhạc, hậu trường được cập nhật nhanh nhất tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_LANGNHAC:
                $titleMeta = $keywordMeta = 'Làng nhạc Việt Nam, quốc tế, album, bài hát, music video mới' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Làng nhạc Việt, ca sĩ, album, bài hát, music video mới được cập nhật nhanh nhất tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_GOCNHINAMNHAC:
                $titleMeta = $keywordMeta = 'Góc nhìn, bình luận âm nhạc, chuyên gia đánh giá' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Bình luận âm nhạc, đánh giá từ chuyên gia  được cập nhật nhanh nhất tại trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_SANKHAUMYTHUAT:
            case BLOCK_CATE_SANKHAU:
            case BLOCK_CATE_MYTHUAT:
                $titleMeta = $keywordMeta = 'Sân khấu, kịch nói, cải lương, chèo, xiếc, hội họa, nhiếp ảnh ' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Tin tức sân khấu, kịch nghệ, cải lương, chèo, xiếc, hội họa, nhiếp ảnh, điêu khắc được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_CONGDONG:
                $titleMeta = $keywordMeta = 'Cộng đồng giải trí, độc giả, âm nhạc, phim ảnh' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Cộng đồng, giải trí, bình luận, đánh giá về đời sống văn hóa, nghệ thuật được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_BINHLUAN:
                $titleMeta = $keywordMeta = 'Đánh giá, bình luận về âm nhạc, điện ảnh, đời sống giải trí' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Bình luận âm nhạc, bình luận phim ảnh, cảm xúc âm nhạc được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_TOIDEP:
                $titleMeta = $keywordMeta = 'Phong cách thời trang cá tính, độc đáo, lạ mắt' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Phong cách thời trang với nhiều màu sắc, mix đồ, phối đồ, các đồ thời trang lạ mắt, xu hướng thời trang được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_THUTAI:
                $titleMeta = $keywordMeta = 'Thử tài âm nhạc, điện ảnh, thời trang, góc quà tặng' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Các sự kiện offline, thử tài, đố vui, góc quà tặng được cập nhật tại chuyên trang VnExpress Giải Trí';
                break;
            case BLOCK_CATE_PHONGVANTRUCTUYEN:
                $titleMeta = $keywordMeta = 'Phỏng vấn trực tuyến người nổi tiếng' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Trò chuyện cùng người nổi tiếng , phỏng vấn sao online. Lắng nghe tâm sự của sao trong làng giải trí Việt Nam và Thế Giới';
                break;
            default:
                $titleMeta = $keywordMeta = 'Tin tức giải trí 24h, showbiz, sao Việt & Thế Giới' . $titlePaging . ' - VnExpress Giải Trí';
                $desMeta = 'Chuyên trang thông tin giải trí, tin tức âm nhạc, hậu trường sân khấu, kịch, điện ảnh, lịch chiếu phim, trailer phim chiếu rạp mới nhất';
                break;
        }//end switch case
        //set param paging
        $arrParamPaging = array(
            'total' => $arrListPagingNews['total'],
            'current' => $intPage,
            'perpage' => LIMIT_LIST_NEWS,
            'url' => $cateDetail['link'] . '/page',
            'extEnd' => '.html',
        );

        //********************* Assign to view *************************//
        $this->view->assign(array(
            'intCategoryId' => $intCateId,
            'firstNews' => $firstNews,
            'arrListPagingNews' => $arrListPagingNews['data'],
            'arrParamPaging' => $arrParamPaging
        ));

        //Set no render
        $this->_helper->viewRenderer->setNoRender();

        //Echo html
        echo $this->view->render('/m240/index.phtml');

        // Prepend meta data
        $this->view->headTitle()->prepend($titleMeta);
        $this->view->headMeta()->setName('description', $desMeta)->setName('keywords', $keywordMeta);
    }

    /**
     * Category photo Action
     */
    public function catephotoAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //set cate to param
        $this->_request->setParam('cateid', CATE_ID_PHOTO);

        //Get page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);

        //Init Object News
        $modelNews = Thethao_Model_News::getInstance();

        //Init params
        $arrParamsRule2 = array(
            'category_id' => CATE_ID_PHOTO,
            'limit' => LIMIT_LIST_NEWS,
            'offset' => ($intPage - 1) * LIMIT_LIST_NEWS,
            'article_type' => TYPE_ALL
        );

        //********************* LIST PAGING *************************//
        //get news by rule 2
        $arrListPagingNews = $modelNews->getListArticleIdsByRule2($arrParamsRule2);
        
        //Check total page
        if ($intPage > 1 && $arrListPagingNews['total'] > 0)
        {
            $maxPage = ceil($arrListPagingNews['total'] / LIMIT_LIST_NEWS);
            //check if page > max page then return to max page
            if ($intPage > $maxPage)
            {
                //redirect
                $this->_redirect($this->view->configView['url'] . '/photo/page/' . $maxPage . '.html');
            }//end if
        }//end if
        //set id basic list paging + set first news
        $firstNews = 0;
        if (!empty($arrListPagingNews['data']))
        {
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrListPagingNews['data']);

            //set first news
            $firstNews = array_shift($arrListPagingNews['data']);
        }//end if
        //set param paging
        $arrParamPaging = array(
            'total' => $arrListPagingNews['total'],
            'current' => $intPage,
            'perpage' => LIMIT_LIST_NEWS,
            'url' => $this->view->configView['url'] . '/photo/page',
            'extEnd' => '.html',
        );

        //********************* Assign to view *************************//
        $this->view->assign(array(
            'intCategoryId' => CATE_ID_PHOTO,
            'firstNews' => $firstNews,
            'arrListPagingNews' => $arrListPagingNews['data'],
            'arrParamPaging' => $arrParamPaging
        ));

        //Set no render
        $this->_helper->viewRenderer->setNoRender();

        //Echo html
        echo $this->view->render('/m240/index.phtml');

        // Prepend meta data
        $this->view->headTitle('Lưu trữ hình ảnh cầu thủ, trận đấu các giải bóng đá trong nước và thế giới - VnExpress Thể thao');
        $this->view->headMeta()->setName('description', 'Lưu trữ hình ảnh của các cầu thủ, pha ghi bàn, bàn thắng đẹp, sự kiện các trận đấu của các giải bóng đá trong nước và quốc tế.');
        $this->view->headMeta()->setName('keywords', 'Hinh anh,bong da,the thao');
    }

    /**
     * Detail Action
     */
    public function detailAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //Get article id
        $id = $this->_request->getParam('id', 0);
        if (!is_numeric($id) || $id <= 0)
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
        }//end if
        //Get full detail article
        $arrData = $this->view->objArticle->getArticleFull($id);

        //********************* Check Detail Article *************************//
        if (empty($arrData))
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
        }//end if
        //********************* Check Bài Hẹn Giờ *************************//
        if (intval($arrData['publish_time']) > time())
        {
            //secr not match
            if (md5($id . $arrData['creation_time']) != $this->_request->getParam('sig'))
            {
                //redirect về Trang Error
                $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
            }//end if
        }//end if
        //Get string link
        $strLink = $this->view->configView['url'] . $this->_request->getPathInfo();

        //check page
        $pageTypeExt = 0;
        $page = 0;

        //Get page
        $intPage = max(1, $this->_request->getParam('page', 1));
        if ($intPage > 1)
        {
            //get data page extend
            $arrDataExtend = $this->view->objArticle->getDetailPageExtend($id, $intPage);
            if (!empty($arrDataExtend))
            {
                //Page type
                $pageTypeExt = $arrDataExtend['page_type'];

                //unset
                unset($arrDataExtend['creation_time']);
                unset($arrDataExtend['publish_time_format']);
                unset($arrDataExtend['publish_time']);

                //merge page extend to data detail
                $arrData = array_merge($arrData, (array) $arrDataExtend);
                //get link remove -p2
                $strLink = preg_replace('#(-p[\d]+\.html)#', '.html', $strLink);
                $page = $intPage;
            }
            else
            {
                $strLink = '';
            }//end if
        }//end if
        //Check page_type popup
        if ($pageTypeExt == 2)
        {
            $this->view->assign('arrData', $arrData);
            echo $this->view->render('/m240/popup.phtml');
            exit;
        }//end if
        //replace share_url
        $arrData['share_url'] = $this->view->ShareurlM240($arrData['share_url']);

        //check link to redirect
        if ($strLink != $arrData['share_url'])
        {
            //($page>1)?$this->_redirect(str_replace('.html', '-p'.$page.'.html', $arrData['share_url']), array('code'=>301)):$this->_redirect($arrData['share_url'], array('code'=>301));
        }//end if
        //Set category id for block_cate user in block
        $this->_request->setParam('cateid', $arrData['category_id']);

        //News object
        $modelNews = Thethao_Model_News::getInstance();

        //Category object
        $modelCate = Thethao_Model_Category::getInstance();

        //********************* Process Tin khác *************************//
        //Init array params other news
        $arrOtherNewsParams = array(
            'category_id' => $arrData['category_id'], //Cate chinh
            'limit' => LIMIT_OTHER_NEWS + 1, //loai tru tin dang xem
            'offset' => 0,
            'article_type' => TYPE_ALL
        );

        //Get news by rule 2: folder và liston lên folder của tin đang xem)
        $arrOtherNews = $modelNews->getListArticleIdsByRule2($arrOtherNewsParams);

        //check data empty
        if (is_array($arrOtherNews) && !empty($arrOtherNews['data']))
        {
            $intKeyExists = array_search($id, $arrOtherNews['data']);
            if ($intKeyExists !== FALSE)
            {
                unset($arrOtherNews['data'][$intKeyExists]);
            }
            elseif (isset($arrOtherNews['data'][LIMIT_OTHER_NEWS]))
            {
                unset($arrOtherNews['data'][LIMIT_OTHER_NEWS]);
            }//end if
        }//end if
        //********************* Process Topic - Tin liên quan *************************//
        /*
         *  - Nếu bài đang xem thuộc duy nhất 1 topic thì lấy 2 tin mới nhất của topic đó
         *  - Nếu bài đang xem thuộc từ 2 topic trở lên thì lấy 2 tin mới nhất của topic có tin mới nhất
         */
        // GET TOPIC NEWS
        $arrTopic = $arrTopicDetail = array();

        // check empty list topic
        if (!empty($arrData['list_topic_id']))
        {
            // get topic object
            $modeTopic = new Fpt_Data_News_Topic();

            // init topic params
            $arrTopicParams = array(
                'site_id' => SITE_ID_VNE,
                'topic_id' => $arrData['list_topic_id'],
                'limit' => LIMIT_TOPIC_DETAIL + 1,
                'offset' => 0,
                'withscore' => true,
                'article_type' => TYPE_ALL
            );

            // get list article by topic
            $arrTopic = $modeTopic->getTopicNewsByScore($arrTopicParams);

            // check array topic data
            if (!empty($arrTopic))
            {
                $score = 0;
                $temp_topic_id = 0;

                //Loop
                foreach ($arrData['list_topic_id'] as $index => $topic_id)
                {
                    if (isset($arrTopic[$topic_id]['data'][$id]))
                    {
                        unset($arrTopic[$topic_id]['data'][$id]);
                        $arrTopic[$topic_id]['total'] = $arrTopic[$topic_id]['total'] - 1;
                    }//end if
                    if (empty($arrTopic[$topic_id]['data']))
                    {
                        unset($arrData['list_topic_id'][$index]);
                    }//end if
                    // get first article to compare
                    $firstCore = current($arrTopic[$topic_id]['data']);
                    if ($firstCore > $score)
                    {
                        $temp_topic_id = $topic_id;
                        $score = $firstCore;
                    }//end if
                }//end foreach
                if (!empty($arrData['list_topic_id']))
                {
                    // get detail of topic id list
                    $arrTopicDetail = $modeTopic->getDetailTopicByArrId(array($temp_topic_id));
                    $arrTopic = array_slice(array_keys($arrTopic[$temp_topic_id]['data']), 0, LIMIT_TOPIC_DETAIL);
                    $arrTopicDetail = $arrTopicDetail[$temp_topic_id];

                    // set to article instance
                    $this->view->objArticle->setIdBasic($arrTopic);
                }//end if
            }//end if
        }//end if
        //Check empty
        if (!empty($arrOtherNews['data']))
        {
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrOtherNews['data']);
        }//end if
        //replace image size for mobile
        $content = $arrData['content'];
        $content = preg_replace('/src="(.[^>]*)_500x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
        $content = preg_replace('/src="(.[^>]*).(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
        $content = preg_replace('/src="(.[^>]*)_m_460x0_m_460x0.(jpg|gif|png|jpeg)"/', 'src="$1_m_460x0.$2"', $content);
		//remove all iframe and div has class embed-container
		$content = preg_replace('/<iframe.[^>]*>.*<\/iframe>/siU', '<div class="clear">&nbsp;</div>', $content);
        $content = preg_replace('/<div(.[^>]*)class="embed-container(.*[^\"])?"(.[^>])*>(.*)<\/div><\/div>/siU', '<div class="clear">&nbsp;</div>', $content);
        $content = $this->view->ShareurlM240($content);
        $arrData['content'] = $content;

        //Trim data
        $arrData['title'] = trim($arrData['title']);
        $arrData['title_format'] = trim($arrData['title_format']);
        $arrData['lead'] = trim($this->view->ShareurlM240($arrData['lead']));

        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = strip_tags(html_entity_decode($arrData['title'], ENT_COMPAT, 'UTF-8')) . ' - VnExpress Giải Trí';
        $strMetaDescript = strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8')) . ' - VnExpress Giải Trí';

        //********************* Assign to views *************************//
        $this->view->assign(array(
            'arrArticleDetail' => $arrData,
            'arrOtherNews' => $arrOtherNews,
            'arrTopic' => $arrTopic,
            'arrTopicDetail' => $arrTopicDetail,
            'intCategoryId' => $arrData['category_id'],
			'intArticleId' => $arrData['article_id']
        ));

        // Prepend title
        $this->view->headTitle()->prepend($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDescript)
            ->setName('keywords', $strMetaTitle);
    }

    /**
     * Photo Action
     */
    public function photoAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //Get article id
        $id = (int) $this->_request->getParam('id', 0);
        if (!is_numeric($id) || $id <= 0)
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
        }//end if
        //Get full detail article
        $arrData = $this->view->objArticle->getArticleFull($id);

        //valid data
        if (empty($arrData))
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
        }//end if
        //check publish
        if (intval($arrData['publish_time']) > time())//is schedule article
        {
            $sig = md5($id . $arrData['creation_time']); //gen sig
            if ($sig != $this->_request->getParam('sig'))//secr not match
            {
                $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);
            }//end if
        }//end if
        //str link
        $strLink = $this->view->configView['url'] . $this->_request->getPathInfo();

        //replace share_url
        $arrData['share_url'] = $this->view->ShareurlM240($arrData['share_url']);

        //check link
        if ($strLink != $arrData['share_url'])
        {
            $this->_redirect($arrData['share_url']);
        }//end if
        //Set category id for block_cate user in block
        $this->_request->setParam('cateid', $arrData['category_id']);

        //Set params
        $arrParams = array(
            'article_id' => $arrData['article_id'],
            'limit' => LIMIT_SLIDESHOW_PHOTO,
            'offset' => 0
        );
        //Get list object reference
        $arrReference = $this->view->objArticle->getObjectReferenceByArticleId($arrParams);

        //********************* Initialize Model Object *************************//
        //News object
        $modelNews = Thethao_Model_News::getInstance();

        //********************* Process Tin khác *************************//
        //Init array params other news
        $arrOtherNewsParams = array(
            'category_id' => $arrData['category_id'], //Cate chinh
            'limit' => LIMIT_OTHER_NEWS + 1, //loai tru tin dang xem
            'offset' => 0,
            'article_type' => TYPE_ALL
        );

        //Get news by rule 2: folder và liston lên folder của tin đang xem)
        $arrOtherNews = $modelNews->getListArticleIdsByRule2($arrOtherNewsParams);

        //check data empty
        if (is_array($arrOtherNews) && !empty($arrOtherNews['data']))
        {
            $intKeyExists = array_search($id, $arrOtherNews['data']);
            if ($intKeyExists !== FALSE)
            {
                unset($arrOtherNews['data'][$intKeyExists]);
            }
            elseif (isset($arrOtherNews['data'][LIMIT_OTHER_NEWS]))
            {
                unset($arrOtherNews['data'][LIMIT_OTHER_NEWS]);
            }//end if
        }//end if
        //Check empty
        if (!empty($arrOtherNews['data']))
        {
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrOtherNews['data']);
        }//end if
        //Trim data
        $arrData['title'] = trim($arrData['title']);
        $arrData['title_format'] = trim($arrData['title_format']);
        $arrData['lead'] = trim($this->view->ShareurlM240($arrData['lead']));
		$arrData['content'] = $this->view->ShareurlM240($arrData['content']);

        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = strip_tags(html_entity_decode($arrData['title'], ENT_COMPAT, 'UTF-8')) . ' - VnExpress Giải Trí';
        $strMetaDescript = strip_tags(html_entity_decode($arrData['lead'], ENT_COMPAT, 'UTF-8')) . ' - VnExpress Giải Trí';

        //Assign to views
        $this->view->assign(array(
            'arrArticleDetail' => $arrData,
            'arrPhoto' => $arrReference['data'],
            'intCategoryId' => $arrData['category_id'],
			'intArticleId' => $arrData['article_id'],
            'arrOtherNews' => $arrOtherNews
        ));

        // Prepend title
        $this->view->headTitle()->prepend($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDescript)
            ->setName('keywords', $strMetaTitle);
    }

    /**
     * Infographic Action
     */
    public function infographicAction()
    {
        //get link info
        $strLink = $this->_request->getPathInfo();
        $strLink = preg_replace('/\/$/', '', $strLink);

        //redirect to VNE
        $this->_redirect($this->view->configView['vnexpress']['url'] . $strLink, array('code' => 301));
    }

}