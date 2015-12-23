<?php

/**
 * @author      :   PhonTX - 09/08/2012
 * @name        :   CaptchaController
 * @version     :   09082012
 * @copyright   :   FPT Online
 * @todo        :   Capcha controller
 */
class CaptchaController extends Zend_Controller_Action
{

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : showAction
     * @copyright   : FPT Online
     * @todo    : Show Action
     */
    public function showAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);
        $wordlen = $this->_request->getParam('wordlen', '4');
        $width = $this->_request->getParam('width', '60');
        $height = $this->_request->getParam('height', '40');
        //captcha new object from framework - extend of Zend_Captcha_Image
        $captcha    = new Fpt_Captcha_ImageMem();

        //Set lenght string captcha
        $captcha->setWordLen($wordlen);
        //Set time out
        $captcha->setTimeout('300');
        //Set height image captcha
        $captcha->setHeight($height);
        //Set width image captcha
        $captcha->setWidth($width);
        //Set font captcha
        $captcha->setFontSize(14);
        $captcha->setDotNoiseLevel(0);
        $captcha->setLineNoiseLevel(0);

        //Set file font use captcha
        $captcha->setFont(APPLICATION_PATH . '/data/fonts/tahoma.ttf');
        //set image captcha url
        $captcha->setImgUrl($this->view->configView['url'] . '/captcha/viewimg/id');
        //set image captcha alt
        $captcha->setImgAlt('Mã xác nhận gửi bài viết đến tòa soạn');
        //Generate captcha
        $captcha->generate();
        //Render image captcha
        $image = $captcha->render();
        // difine out captcha id
        $input = '<input type="hidden" name="captchaID" id="captchaID" value ="' . $captcha->getId() . '" />';
        //Get word captcha
        $word = $captcha->getWord();
        //set session word captcha
        $_SESSION['word'] = $word;
        //Define array response
        $arrResponse = array('html' => $image . $input);
        //Return data json
        echo Zend_Json::encode($arrResponse);
        exit;
    }

    /**
     * View image action
     */
    function viewimgAction()
    {
        $id = $this->_request->getParam('id', '');
        if ( $id )
        {
            $captcha    = new Fpt_Captcha_ImageMem();
            $image      = $captcha->_getCache($id);
            header('Content-Type: image/png');
            echo $image;
        }//end if
        exit;
    }

}