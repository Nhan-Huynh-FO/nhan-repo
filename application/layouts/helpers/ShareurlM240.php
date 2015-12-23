<?php

class Zend_View_Helper_ShareurlM240 extends Zend_View_Helper_Abstract
{
    protected $_search;
    protected $_replace;

    public function __construct()
    {
        switch ( APPLICATION_ENV )
        {
            case 'development':
                $search     = array('http://phuongtn1.vnexpressdev.net', 'http://hungnt1.v3.thethao.vnexpressdev.net');
                $replace    = array('http://phuongtn1.m.vnexpressdev.net', 'http://hungnt1.m.v3.thethao.vnexpressdev.net');
                break;
            case 'qc':
                $search     = array('http://qc.v2.vnexpress.net', 'http://qc.v2.kinhdoanh.vnexpress.net', 'http://qc.v3.giaitri.vnexpress.net', 'http://qc.v3.thethao.vnexpress.net', 'http://qc.v2.doisong.vnexpress.net', 'http://qc.v3.sohoa.vnexpress.net', 'http://qc.v2.dulich.vnexpress.net');
                $replace    = array('http://qc.m.v2.vnexpress.net', 'http://qc.m.v2.kinhdoanh.vnexpress.net', 'http://qc.m.v3.giaitri.vnexpress.net', 'http://qc.m.v3.thethao.vnexpress.net', 'http://qc.m.v2.doisong.vnexpress.net', 'http://qc.m.v3.sohoa.vnexpress.net', 'http://qc.m.v2.dulich.vnexpress.net');
                break;
            case 'production':
            default:
                $search     = array('http://vnexpress.net', 'http://kinhdoanh.vnexpress.net', 'http://giaitri.vnexpress.net', 'http://thethao.vnexpress.net', 'http://doisong.vnexpress.net', 'http://sohoa.vnexpress.net', 'http://dulich.vnexpress.net');
                $replace    = array('http://m.vnexpress.net', 'http://m.kinhdoanh.vnexpress.net', 'http://m.giaitri.vnexpress.net', 'http://m.thethao.vnexpress.net', 'http://m.doisong.vnexpress.net', 'http://m.sohoa.vnexpress.net', 'http://m.dulich.vnexpress.net');
                break;
        }//end switch

        //set private
        $this->_search      = $search;
        $this->_replace     = $replace;
    }

    public function ShareurlM240($strUrl)
    {
        //return
        return str_replace($this->_search, $this->_replace, $strUrl);
    }
}