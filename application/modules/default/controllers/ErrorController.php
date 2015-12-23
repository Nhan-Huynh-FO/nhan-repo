<?php

/**
 * @author      :   HungNT1
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class ErrorController extends Zend_Controller_Action
{

    /**
     * Page Error
     */
    public function indexAction()
    {
        // Redirect VNE
        $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV]);

        $this->_helper->layout->setLayout('error');
        // Prepend title

        $this->view->breakName  = 'Error';

        $this->view->headTitle('Thể thao - Tin thể thao 24h, lịch thi đấu, kết quả, video clip - VnExpress');
        $this->view->headMeta()->setName('description', 'Tin nhanh video clip hình ảnh các môn thể thao: bóng đá quyền anh đua xe bóng chuyền cầu lông… đang diễn ra ở VN & thế giới.');
        $this->view->headMeta()->setName('keywords', 'Thể thao,tin tức,bóng đá');
    }

}