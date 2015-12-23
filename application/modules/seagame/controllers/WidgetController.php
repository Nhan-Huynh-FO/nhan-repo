<?php

/**
 * @author  : HungNT1
 * @name    : WidgetController
 * @copyright   : FPT Online
 * @todo    : WidgetController
 */
class WidgetController extends Zend_Controller_Action
{

    /**
     * Widget hot news
     */
    public function indexAction()
    {
        //disable layout
        $this->_helper->layout->disableLayout();
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Get article id
        $arrParam = $this->_request->getParams();

        switch ($arrParam['type'])
        {
            case 3:
                $keyId = 'schedule_seagames28_vn';
                break;
            case 2:
                $keyId = 'bxh_seagames_28_vn';
                break;
            case 1:
            default :
                $keyId = 'bxh_seagames_28';
                break;
        }
        
        //get Instance News
        $modelBlock = new Thethao_Model_Block();
        //get data with rule 1
        $arrData = $modelBlock->getKeyBox(array('key_id' => $keyId));
        
        //check type
        $intType = 1;
        if (isset($arrParam['type']))
        {
            $intType = $arrParam['type'] > 3 ? 1 : $arrParam['type'];
        }
        
        $this->view->assign(array(
            'arrData' => $arrData,
            'type' => $intType,
        ));
    }

}
