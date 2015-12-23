<?php

/**
 * @author      :   HungNT1
 * @name        :   RssController
 * @copyright   :   Fpt Online
 * @todo        :   Rss Controller
 */
class RssController extends Zend_Controller_Action
{
    /**
     * @todo VnE Thethao RSS Index Page
     */
    public function indexAction()
    {
        $this->_helper->layout->setLayout('error');

        //Get cate_id
        $cate = $this->_request->getParam('cate');

        //check param cate
        if ($cate != '')
        {
            //forward to detail action
            return $this->_forward('detail');
        }

        $this->_redirect($this->view->configView['url_vne'] . '/rss/', array('code' => 301));
        return;
    }

    /**
     * @todo - VnE Thethao Rss Detail Page
     * @author HungNT1
     */
    public function detailAction()
    {
        //Set no view & no layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        header("Content-Type: application/xml; charset=utf-8");
        //Auto refesh
        $this->view->headMeta()->appendHttpEquiv('refesh', '300');
        $strCodeTinMoi = 'tin-moi-nhat';
        $strCodeTinHot = 'tin-hot';

        //Get cate_id
        $cate = $this->_request->getParam('cate');
        if ($strCodeTinMoi !== $cate && $strCodeTinHot != $cate)
        {
            if ($cate == null)
            {
                $this->_redirect($this->view->configView['url']);
            }
            $category = Thethao_Model_Category::getInstance();
            $result = $category->getCategoryByCode($cate);

            $cate_id = $result['category_id'];
            if ($cate_id < 0 || $cate_id == null)
            {
                $this->_redirect($this->view->configView['url']);
            }
        }

        //Get instance
        $modelNews = Thethao_Model_News::getInstance();


        //Create parent feed
        $feed = new Zend_Feed_Writer_Feed;
        $feed->setGenerator('Thehao.VnExpress.net:' . $this->view->configView['url'] . '/rss');
        $feed->setDateModified(time());
        $feed->setDescription('VNExpress Thể thao RSS');

        if ($cate == $strCodeTinMoi)
        {
            //redirect to vne
            $this->_redirect($this->view->configView['url_vne'].'/rss/the-thao.rss', array('code' => 301));

        }
        elseif ($cate == $strCodeTinHot)
        {
            // Get lastest article in 1 days
            $arrParams = array(
                'area' => array(
                    0 => array('category_id' => SITE_ID, 'showed_area' => 'trangchu'),
                ),
                'offset' => 0,
                'limit' => 100
            );
            $arrLastestArticleID = $this->view->objArticle->getTopHotArticleByArr($arrParams);

            if (!empty($arrLastestArticleID[0]))
            {
                foreach ($arrLastestArticleID[0] as $val)
                {
                    $val['article_id'] = intval($val['article_id']);
                    $entry = $feed->createEntry();
                    $link = '';

                    $link .= $val['share_url'];
                    if ($val['title'] != null)
                        $entry->setTitle($val['title']);
                    if ($val['share_url'] != null)
                        $entry->setLink($link);
                    if ($val['update_time'] > 0)
                    {
                        $entry->setDateModified($val['update_time']);
                    }
                    if ($val['publish_time'] > 0)
                        $entry->setDateCreated($val['publish_time']);

                    $val['lead'] != null ? $entry->setDescription('<a href="' . $link . '"><img width=130 height=100 src="' . $this->view->ImageurlArticle($val, 'size1') . '" ></a></br>' . $val['lead']) : $entry->setDescription('No Description');
                    $feed->addEntry($entry);
                }
            }
            $result['catename'] = 'Tin hot';
        }
        else
        {
            // Get list article with rule 3
            $arrPamram = array( 'category_id'   => $cate_id,
                                'site_id'       => SITE_ID,
                                'offset'        => 0,
                                'limit'         => 20
                                );

            $arrTopArticleID = $modelNews->getListArticleIdsByRule3($arrPamram);

            //Add entry
            if (!empty($arrTopArticleID['data']))
            {
                $this->view->objArticle->setIdBasic($arrTopArticleID['data']);
                foreach ($arrTopArticleID['data'] as $id)
                {
                    $val = $this->view->objArticle->getArticleBasic($id);

                    if (empty($val))
                    {
                        continue;
                    }
                    $link = $val['share_url'];
                    $entry = $feed->createEntry();
                    if ($val['title'] != null)
                        $entry->setTitle($val['title']);
                    if (!empty($link) && null != $link)
                        $entry->setLink($link);
                    if ($val['update_time'] != null)
                        $entry->setDateModified($val['update_time']);
                    if ($val['creation_time'] != null)
                        $entry->setDateCreated($val['creation_time']);
                    $val['lead'] != null ? $entry->setDescription('<a href="' . $link . '"><img width=130 height=100 src="' . $this->view->ImageurlArticle($val, 'size5') . '" ></a></br>' . $val['lead']) : $entry->setDescription('No Description');

                    $feed->addEntry($entry);
                }
            }
        }

        $feed->setTitle($result['catename'] . ' - VNExpress Thể thao RSS');
        $feed->setLink('http://thethao.vnexpress.net/rss/' . $cate . '.rss');
        //Create xml
        $out = $feed->export('rss');
        echo $out;
    }

    /**
     * Page Rss Yahoo
     */
    public function yahooAction() {
        $this->_helper->layout->disableLayout();

        $modelNews = Thethao_Model_News::getInstance();

        // Get list article with rule 3
        $arrPamram = array( 'category_id'   => SITE_ID,
                            'site_id'       => SITE_ID,
                            'offset'        => 0,
                            'limit'         => 25
                            );

        $arrTopArticleID = $modelNews->getListArticleIdsByRule3($arrPamram);

		$this->view->assign(array(
			'arrArticle'             => $arrTopArticleID['data']
		));
    }
}