<?php

/**
 * @author  : HungNT1
 * @name    : Pagination
 * @copyright   : FPT Online
 * @todo    : Helper Pagination
 */
class Zend_View_Helper_PaginationWorldcup extends Zend_View_Helper_Abstract
{

    /**
    * Pagination
    * @param <array> $arrParams
    * @return string
    * @author : HungNT1
    */
    public function PaginationWorldcup($arrParams = array())
    {
        $strReturn = '';

        if($arrParams['total'] > $arrParams['perpage'])
        {
            //Init Check Param
            $intTotalRecord = $arrParams['total'];
            $record_page = $arrParams['perpage'];
            $intPage = $arrParams['page'];
            $dot = isset($arrParams['separate'])?$arrParams['separate']:'-';
            $strLink = $arrParams['url'];
            $idPaging = isset($arrParams['classPagination'])?$arrParams['classPagination']:'pagination';
            $extraEnd = isset($arrParams['extEnd'])?$arrParams['extEnd']:'.html';

            //Get Data Page
            $intTotalPage = ceil($intTotalRecord / $record_page);
            $strUri = $dot;
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

            $intPageStart = $intPageStart > 0 ? $intPageStart : 1;

            $strReturn = '<div class="'.$idPaging.'"><div class="center">';
            $strtPreviousPageLink = ($intPage > 1) ? $strLink . $strUri . ($intPage - 1).$extraEnd : 'javascript:void(0)';

            if($intPage > 1)
            {
                $strReturn .='<a href="'.$strtPreviousPageLink.'" class="pagination_btn pa_prev">&nbsp;</a>';
            }

            for ($intTemp = $intPageStart; $intTemp <= $intPageEnd; $intTemp ++)
            {

                $strTempLink = ($intTemp == $intPage) ? 'javascript:void(0)' : $strLink . $strUri . $intTemp.$extraEnd;
                $strTempClass = ($intTemp == $intPage) ? 'class="active"':'';

                $strReturn .= '<a href="'.$strTempLink.'" '.$strTempClass.'>'.$intTemp.'</a>';
            }

            $strtNextPageLink = ($intTotalPage > $intPage) ? $strLink . $strUri . ($intPage + 1).$extraEnd : 'javascript:void(0)';


            if($intTotalPage > $intPage)
            {
                $strReturn .='<a href="'.$strtNextPageLink.'" class="pagination_btn pa_next">&nbsp;</a>';
            }
            $strReturn .= '</div></div>';
        }

        //return Data
        return $strReturn;
    }

}