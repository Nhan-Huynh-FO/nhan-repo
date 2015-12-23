<?php

/**
 * @author   : PhongTX - 21/08/2012
 * @name : InternalController
 * @copyright   : FPT Online
 * @todo    : Internal Controller
 */
class InternalController extends Zend_Controller_Action
{

    /**
     * @author   : PhongTX - 21/08/2012
     * @name : indexAction
     * @copyright   : FPT Online
     * @todo    : index Action
     */
    public function indexAction()
    {
        $this->_redirect('/error.html', array('code' => 301));
        exit;
    }

    /**
     * revert from web routes url to mobile url and redirect
     * @author: NhuanTP
     */
    public function mobilerevertAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        //Get params
        $params = $this->_request->getParams();

        //Set COOKIE for PDA !important
        setcookie("VNEPDA", 1, time() + 3600, '/', '.vnexpress.net');

        //Get model article
        $objArticle = $this->view->objArticle;
        //Set href redirect
        $href = $this->view->configView['url'];

        if ($params['a'] > 0 && $params['c'] == 0)
        {

            $modelObject = Fpt_Data_Materials_Object::getInstance();
            $modelMatch = $modelObject->getMatch();
            $modelTeam = $modelObject->getTeam();
            // Set list matches' id
            $modelMatch->setId($params['a']);
            $matchDetail = $modelMatch->getDetailObjectBasic($params['a']);
            if (!empty($matchDetail))
            {
                $team1 = $team2 = array();
                $modelTeam->setId(array($matchDetail['team1'], $matchDetail['team2']));
                $team1 = $modelTeam->getDetailObjectBasic($matchDetail['team1']);
                $team2 = $modelTeam->getDetailObjectBasic($matchDetail['team2']);
                $title = Fpt_Filter::setSeoLink($team1['name'] . ' vs ' . $team2['name']);
                $href .= "/tuong-thuat/tran-$title-$params[a]";
            }
        }
        elseif ($params['a'] > 0)
        {
            $objArticle->setIdBasic($params['a']);
            $arrArticleDetail = $objArticle->getArticleBasic($params['a']);
            $arrArticleDetail['product_id'] = 130;

            //Check detail article
            if (is_array($arrArticleDetail) && isset($arrArticleDetail['product_id']))
            {
                //kiem tra mot lan nua xem co nhieu hon mot bai viet khong
                //Get model news
                $objNews = Thethao_Model_News::getInstance();
                $arrArticleId = $objNews->getArticleIdByProductId($arrArticleDetail['product_id']);

                if ($arrArticleId != -1)
                {
                    foreach ($arrArticleId as $index => $id)
                    {
                        if ($id == $params['a'])
                        {
                            unset($arrArticleId[$index]);
                        }
                    }

                    if (count($arrArticleId) > 0)
                    {
                        //Get detail article
                        $this->view->objArticle->setIdBasic($arrArticleId[0]);
                        $arrArticleDetail = $this->view->objArticle->getArticleBasic($arrArticleId[0]);
                    }
                }
            }

            if (!empty($arrArticleDetail))
            {
                //Redirect
                $this->_redirect($arrArticleDetail['share_url'], array('code' => 301));
                exit;
            }
        }
        if ($params['c'] > 0)
        {
            if ($params['c'] != SITE_ID)
            {
                //get instance Thethao_Model_Category
                $objCategory = Thethao_Model_Category::getInstance();
                //get detail cate current
                $cateDetail = $objCategory->getDetailByCateId($params['c']);
                //Check menu detail
                if (is_array($cateDetail))
                {
                    $href .= $cateDetail['link'];
                }//end if
            }
        }
        //Redirec
        $this->_redirect($href, array('code' => 301));
        exit;
    }

}

?>
