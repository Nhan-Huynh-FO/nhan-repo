<?php

class Zend_View_Helper_ShareurlSeagame extends Zend_View_Helper_Abstract
{

    protected $_search;
    protected $_replace;

    public function __construct()
    {
        switch (APPLICATION_ENV)
        {
            case 'development':
                $search = array('http://phuongtn1.vnexpressdev.net', 'http://hungnt1.v3.thethao.vnexpressdev.net', 'http://phuongtn1.thethao.vnexpressdev.net', 'http://video.vnexpressdev.net/');
                $replace = array('http://hungnt1.seagames.vnexpressdev.net', 'http://hungnt1.seagames.vnexpressdev.net', 'http://hungnt1.seagames.vnexpressdev.net', 'http://hungnt1.seagames.vnexpressdev.net/video/');
                break;
            case 'qc':
                $search = array('http://qc.gamethu.vnexpress.net', 'http://qc.video.vnexpress.net', 'http://qc.v2.vnexpress.net', 'http://qc.v2.kinhdoanh.vnexpress.net', 'http://qc.v3.giaitri.vnexpress.net', 'http://qc.v3.thethao.vnexpress.net', 'http://qc.v2.doisong.vnexpress.net', 'http://qc.v3.sohoa.vnexpress.net', 'http://qc.v2.dulich.vnexpress.net');
                $replace = array('http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net', 'http://qc.seagames.vnexpress.net');
                break;
            case 'production':
            default:
                $search = array('http://gamethu.vnexpress.net', 'http://video.vnexpress.net', 'http://vnexpress.net', 'http://kinhdoanh.vnexpress.net', 'http://giaitri.vnexpress.net', 'http://thethao.vnexpress.net', 'http://doisong.vnexpress.net', 'http://sohoa.vnexpress.net', 'http://dulich.vnexpress.net');
                $replace = array('http://seagames.vnexpress.net', 'http://seagames.vnexpress.net/video', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net', 'http://seagames.vnexpress.net');
                break;
        }//end switch
        //set private
        $this->_search = $search;
        $this->_replace = $replace;
    }

    public function ShareurlSeagame($strUrl)
    {
        //return
        return str_replace($this->_search, $this->_replace, $strUrl);
    }

}
