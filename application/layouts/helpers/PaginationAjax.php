<?php

/**
 * @author  : HungNT1
 * @name    : Pagination
 * @copyright   : FPT Online
 * @todo    : Helper Pagination
 */
class Zend_View_Helper_PaginationAjax extends Zend_View_Helper_Abstract
{

    /**
    * Pagination
    * @param <array> $arrParams
    * @return string
    * @author : HungNT1
    */
    public function PaginationAjax($arrParams = array(), $obj='photoPaging')
    {
        $strReturn = '';

        if($arrParams['total'] > $arrParams['perpage'])
        {
            //Init Check Param
            $intTotalRecord = $arrParams['total'];
            $record_page = $arrParams['perpage'];
            $intPage = $arrParams['page'];
            $idPaging = isset($arrParams['classPagination'])?$arrParams['classPagination']:'pagination';
            $extraEnd = isset($arrParams['extEnd'])?$arrParams['extEnd']:'.html';

            //Get Data Page
            $intTotalPage = ceil($intTotalRecord / $record_page);
            $intPage = max($intPage, 1);
            $intPage = min($intTotalPage, $intPage);
            //init Item
            $intItem = 2;

            $intPageStart = $intPage - ($intItem);
            $intPageEnd = $intPage + ($intItem);

            if($intPageStart <= 0)
            {
                $intPageEnd += abs($intPageStart)+1;
                $intPageStart = 1;
            }

            if($intPageEnd > $intTotalPage)
            {
                if ($intPageStart != 1)
                {
                    $intPageStart -= $intPageEnd-$intTotalPage;
                }
                $intPageEnd = $intTotalPage;
            }

            $strReturn = '<div class="'.$idPaging.'">';

            if($intPage > 1)
            {
                $strReturn .='<a href="javascript:void(0)" class="pagination_btn pa_prev" onclick="'. $obj.'.prevPage();">&nbsp;</a>';
            }
            if($intPageStart == 0)
            {
                $temp = 1;
            }
            else
            {
                $temp = $intPageStart;
            }
            for ($intTemp = $temp; $intTemp <= $intPageEnd; $intTemp ++)
            {
                $strTempClass = ($intTemp == $intPage) ? 'class="active"':'';
                $strReturn .= '<a href="javascript:void(0)" '.$strTempClass.' onclick="'.$obj.'.numPage('.$intTemp.');">'.$intTemp.'</a>';
            }

            if($intTotalPage > $intPage)
            {
                $strReturn .='<a href="javascript:void(0)" class="pagination_btn pa_next" onclick="'.$obj.'.nextPage();">&nbsp;</a>';
            }
            $strReturn .= '</div>';
        }

        //return Data
        return $strReturn;
    }

}