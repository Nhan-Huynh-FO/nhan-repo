<?php

/**
 * @copyright   FOSP
 * @version     20120412
 * @todo        Helper render player rating
 * @name        Zend_View_Helper_PlayerRating
 * @author      QuangTM 
 */
class Zend_View_Helper_PlayerRating extends Zend_View_Helper_Abstract
{

    private $_view;

    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
    }

    public function playerRating($playerDetail, $index, $image, $vneRating, $userRating, $avgRating, $active = TRUE, $teamOrder = 1)
    {
        $playerID = $playerDetail['PlayerID'];
        $template = '<li %s>
                    <div class="w130">
                        <div class="pic_left"><img width="50" height="50" src="%s" onerror="%s" /></div>
                        <div class="infor_cauthu_right"><b>%s</b><span>%s</span></div><div class="clear"></div>
                    </div>
                    <div class="w50">
                        <b>%s</b>
                    </div>
                    <div class="w70">
                        <b>%s</b>
                    </div>
                    <div class="w50">
                        %s
                    </div>
                    <div class="clear"></div>
                </li>';
        $arrPositions = array(
            'G'   => 'Thủ môn',
            'D'   => 'Hậu vệ',
            'M'   => 'Trung vệ',
            'F'   => 'Tiền đạo',
            'SUB' => 'Dự bị',
        );
        $p1 = $index % 2 == 0 ? 'class="bg_chan"' : '';
        $p1 .= " id='player-$playerID' ";
        $p2 = $this->_view->Imageurl($image, '50x50');
        $p3 = "this.src='".$this->_view->Imageurl('no-avatar-player.gif', '50x50') . "'";
        $p4 = $playerDetail['PlayerName'];
        $p5 = $arrPositions[$playerDetail['Position']];
        $p6 = $vneRating == 0 ? 'Chưa đánh giá' : $vneRating;
        $p7 = $userRating;
        if ($active)
        {
            $p8 = '<select name="player-' . $playerID . '" id="player-' . $playerID . '" onchange="PlayerRating.changePoint(this.id, this.value, ' . $teamOrder . ')">';
            for ($i  = 1; $i <= 10; $i++)
            {
                $p8 .= '<option ';
                $p8 .= $i == 5 ? 'selected="selected" ' : ' ';
                $p8 .= 'value="' . $i . '">' . $i . '</option>';
            }
            $p8 .= '</select>';
        }
        else
        {
            $p8 = $avgRating;
        }
        $html = sprintf($template, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8);
        return $html;
    }

}