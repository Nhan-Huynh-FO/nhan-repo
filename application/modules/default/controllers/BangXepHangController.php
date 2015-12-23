<?php

/**
 * @copyright       : FOSP
 * @version         : 20120322
 * @todo            : Table ranking controller
 * @name            : RankingController
 * @author          : QuangTM
 */
class BangXepHangController extends Zend_Controller_Action
{

    public function tennisAction()
    {
        //get parameter
        $gender = $this->_request->getParam('gender', 1);
        $year = $this->_request->getParam('year', date('Y'));
        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', SITE_ID);
        //get instance
        $tennisModel = Thethao_Model_Tennis::getInstance();
        //get list tennis ranking
        $arrTennisRanking = $tennisModel->getTableTennis($gender, $year);

        $objTennis = $this->view->objObject->getTennis();

        if (!empty($arrTennisRanking))
        {
            //get player tennis id
            $arrPlayerTennis = array_keys($arrTennisRanking);
            $objTennis->setId($arrPlayerTennis);
        }

        //set breakcumb
        $breakCumbOther = array(
            'link'  => '/bang-xep-hang/tennis/gender/1/year',
            'name'  => 'Bảng xếp hạng',
        );

        // Assign to view
        $this->view->assign(array(
            'arrTennisRanking' => $arrTennisRanking,
            'gender' => $gender,
            'year' => $year,
            'objTennis' => $objTennis,
            'breakCumbOther' => $breakCumbOther,
            'intCategoryId' => CATE_ID_TENNIS,
            'ogType'            => 'website',
            'ogTitle'           => 'Bảng xếp hạng Tennis - VnExpress',
            'ogUrl'             => $this->view->configView['url'] . '//bang-xep-hang/tennis/gender/1/year',
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Bảng xếp hạng Tennis - VnExpress'
        ));

        //menu & set cateid to show active in menu
        $this->_request->setParam('cateid', CATE_ID_TENNIS);

        //Head title
        $this->view->headTitle('Bảng xếp hạng Tennis - VnExpress');
    }

    public function ajaxtennisAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        //get params
        $gender = $this->_request->getParam('gender', 0);
        $year = $this->_request->getParam('year', date('Y'));
        //get instance
        $tennisModel = Thethao_Model_Tennis::getInstance();
        //get list tennis ranking
        $arrTennisRanking = $tennisModel->getTableTennis($gender, $year);

        $objTennis = $this->view->objObject->getTennis();

        if (!empty($arrTennisRanking))
        {
            //get player tennis id
            $arrPlayerTennis = array_keys($arrTennisRanking);
            $objTennis->setId($arrPlayerTennis);
        }

        // Assign to view
        $this->view->assign(array(
            'arrTennisRanking' => $arrTennisRanking,
            'gender' => $gender,
            'year' => $year,
            'objTennis' => $objTennis
        ));

        $result = array(
            'error' => 0,
            'msg' => 'Success',
            'body' => $this->view->render('bang-xep-hang/ajaxtennis.phtml'),
        );

        echo Zend_Json::encode($result);
        exit;
    }
}