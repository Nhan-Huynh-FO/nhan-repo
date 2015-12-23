<?php
class Zend_View_Helper_PaginationM240 extends Zend_View_Helper_Abstract
{
    public function PaginationM240($arrParams=array())
    {
        //set string
        $strPrevious    = $strNext = '';

        //check perpage
        if ( $arrParams['total'] > $arrParams['perpage'] )
        {
            //max page
            $maxPage    = ceil($arrParams['total']/$arrParams['perpage']);

            //previous page
            if ( $arrParams['current'] > 1 )
            {
                $strPrevious    = '<a href="'.$arrParams['url'].'/'.($arrParams['current']-1).$arrParams['extEnd'].'" class="left">Quay lại</a>';
            }
            else
            {
                $strPrevious    = '<a class="left txt_quaylai">Quay lại</a>';
            }//end if

            //next page
            if ( $maxPage > $arrParams['current'] )
            {
                $strNext    = '<a href="'.$arrParams['url'].'/'.($arrParams['current']+1).$arrParams['extEnd'].'" class="right">Xem tiếp</a>';
            }
            else
            {
                $strNext    = '<a class="right txt_quaylai">Xem tiếp</a>';
            }//end if
        }//end if

        //return Data
        return '<div class="block_xemthem_240">'.$strPrevious.$strNext.'</div>';
    }

}