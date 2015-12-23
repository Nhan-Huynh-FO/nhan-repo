<?php

/**
 * @author   : TienDQ
 * @name : MbUcfirst
 * @copyright   : FPT Online
 * @todo    : Helper MbUcfirst
 */
class Zend_View_Helper_MbUcfirst extends Zend_View_Helper_Abstract
{

    function MbUcfirst($str, $encoding = 'UTF-8', $lower_str_end = false)
    {
        $str = trim($str);
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = '';
        if ($lower_str_end)
        {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }
        else
        {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }

}