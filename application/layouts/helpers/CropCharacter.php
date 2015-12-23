<?php

/**
 * @author   : TienDQ
 * @name : CropCharacter
 * @copyright   : FPT Online
 * @todo    : Helper CropWord
 */
class Zend_View_Helper_CropCharacter extends Zend_View_Helper_Abstract
{

    protected $iconmore;

    public function __construct()
    {
        $config = Thethao_Global::getApplicationIni();
        $this->iconmore = ' <img src="' . $config['view']['images'] . '/graphics/icon_lead_more.gif" />';
    }
    
    public function CropCharacter($str = '', $len = 0)
    {
        //set default return
        $return = $str;

        if ($len > 0)
        {
            $str = trim(strip_tags(html_entity_decode($str, ENT_COMPAT, 'utf-8')));
            $maxlen = mb_strlen($str, 'utf-8');
            if ($maxlen > $len)
            {
                $isMore = true;
                $tmp = mb_substr($str, 0, $len + 1, 'utf-8');
                $rPos = mb_strrpos($tmp, ' ', 0, 'utf-8');
                if ($rPos === false)
                {
                    $lPos = mb_strpos($str, ' ', 0, 'utf-8');
                    if ($lPos !== false)
                    {
                        $tmp = mb_substr($str, 0, $lPos, 'utf-8');
                    }
                    else
                    {
                        $tmp = $str;
                        $isMore = false;
                    }//end if
                }
                elseif ($rPos < $len)
                {
                    $tmp = mb_substr($tmp, 0, $rPos, 'utf-8');
                }//end if
                //set return
                $return = rtrim($tmp) . ($isMore ? $this->iconmore : '');
            }//end if
        }//end if
        //return
        return $return;
    }

}