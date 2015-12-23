<?php

/**
 * @author   : PhongTX
 * @name : CropWord
 * @copyright   : FPT Online
 * @todo    : Helper CropWord
 */
class Zend_View_Helper_CropWord extends Zend_View_Helper_Abstract
{

    //	Public	function
    public function CropWord($text = '', $numWord = 0, $link = '')
    {
        //Dem tu
        $wordCount = str_word_count(Fpt_Filter::setSeoLink($text, ' '));
        if ($wordCount <= $numWord)
        {
            return $text;
        }
        else
        {
            $arrWord = explode(' ', $text);
            foreach ($arrWord as $word)
            {
                if ($word === '0' || $word)
                {
                    $tmp[] = $word;
                }
            }
            $str = implode(' ', array_slice($tmp, 0, $numWord));

            if (empty($link)) {
                $str = $str . '...';
            }
            else {
                $str = $str . ' <a href="'. $link .'" title=""><img src="http://st.f1.vnecdn.net/responsive/i/v5/icons/icon_more2.gif" title=""/></a>';
            }
            return $str;
        }
    }

}