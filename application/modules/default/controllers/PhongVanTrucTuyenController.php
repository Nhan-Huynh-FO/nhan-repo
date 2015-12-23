<?php

/**
 * @author      : AnPCD
 * @name        : InterviewController
 * @copyright   : FPT Online
 * @todo        : Phong van truc tuyen
 */
class PhongVanTrucTuyenController extends Zend_Controller_Action
{

    /**
     * @author      : AnPCD
     * @name        : indexAction
     * @copyright   : FPT Online
     * @todo        : index Action
     */
    public function indexAction()
    {
        // redirect to VNE
        $this->_redirect($this->view->configView['url_vne'] . $this->_request->getPathInfo(), array('code' => 301));
    }

}

?>