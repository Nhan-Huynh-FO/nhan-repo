<?php

class Zend_View_Helper_DateTimeFormat extends Zend_View_Helper_Abstract {

    public function DateTimeFormat($dateTime = 0, $formatType = '', $endTime = 0) {
        if (!$dateTime) {
            return '';
        }

        switch ($formatType) {
            case 'long':
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime) . ' | ' . date('H:i', $dateTime) . ' GMT+7';
                break;
            case 'photo':
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y ', $dateTime). '<span class="color_time">'. date('H:i', $dateTime) . '</span> GMT+7';
                break;
            case 'custom':
                $strDate = '<span class="color_time">'.date('H:i', $dateTime) . "</span> | " . date('j/n/Y', $dateTime);//date("d/m/Y", $dateTime);
                break;
            case 'short':
                $strDate = date('j/n', $dateTime);
                break;
            case 'birthday':
                $arrDate = date("d/m/Y", $dateTime);
                $arrDate = explode('/', $arrDate);
                $strDate = $arrDate[0] . ' tháng ' . $arrDate[1] . ' năm ' . $arrDate[2];
                break;
            case 'timeGMT': //08:40 GMT+7
                $strDate = strftime('%k', $dateTime) . 'h' . strftime('%M', $dateTime);
                break;
            case 'timeDay': //Thứ năm, 12/07/12
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime);
                break;
            case 'liveinterview': //Thời gian: 13h00 - Ngày: Thứ ba, 17/9/2013
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = 'Thời gian: ' . date('H', $dateTime) . 'h' . date('i', $dateTime) . '-'. date('H', $endTime). 'h'. date('i', $endTime) .' - ' . $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime);
                break;
            case 'liveinterviewbold': //Thời gian: <strong>13h00</strong> - Ngày: <strong>Thứ ba, 17/9/2013</strong>
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7');
                $strDate = 'Thời gian: <strong>' . date('H', $dateTime) . 'h' . date('i', $dateTime) . ' GMT+7</strong> - Ngày: <strong>' . $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime) . '</strong>';
                break;
            case 'short_article': // 08:10 | 5/10/2012
                //time of article
                $year   = date('Y', $dateTime);
                $month  = date('n', $dateTime);
                $day    = date('j', $dateTime);
                $hour   = date('H', $dateTime);
                $minute = date('i', $dateTime);
                //current time
                $yearC  = date('Y');
                $monthC = date('n');
                $dayC   = date('j');
                if ( $day==$dayC && $month==$monthC && $year==$yearC )
                {
                    $strDate    = "<span class=\"color_time\">{$hour}:{$minute}</span>";
                }
                elseif ( $year == $yearC )
                {
                    $strDate    = "<span class=\"color_time\">{$hour}:{$minute}</span> | {$day}/{$month}";
                }
                else
                {
                    $strDate    = "<span class=\"color_time\">{$hour}:{$minute}</span> | {$day}/{$month}/{$year}";
                }//end if
                break;
            case 'long_fixture': //Thứ năm, 12/06/2014
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime);
                break;
            case 'long_predict': //Thứ năm, 12 tháng 6,2014
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j', $dateTime) . ' tháng ' . date('n',$dateTime) .', ' . date('Y', $dateTime);
                break;
            default :
                $strDate =  '<span class="color_time">'.date('H:i', $dateTime) . "</span> | " . date('j/n/Y', $dateTime);
        }
        return $strDate;
    }

}