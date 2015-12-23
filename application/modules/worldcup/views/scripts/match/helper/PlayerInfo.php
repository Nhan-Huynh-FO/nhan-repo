<?php

/**
 * @copyright   FOSP
 * @version     20120410
 * @todo        Helper display player info
 * @name        Zend_View_Helper_PlayerInfo
 * @author      QuangTM
 */
class Zend_View_Helper_PlayerInfo extends Zend_View_Helper_Abstract
{
    public function playerInfo($playerInfo, $substitution = FALSE, $bool = FALSE)
    {
        $arrEvents = array(
            1 => '<img alt="Ghi bàn phút %s" title="Ghi bàn phút %s" src="' . $this->view->configView['images'] . '/icons/icon_goal.gif">',
            2 => '<img alt="Thẻ vàng phút %s" title="Thẻ vàng phút %s" src="' . $this->view->configView['images'] . '/icons/icon_thevang_tk.gif">',
            3 => '<img alt="Thẻ đỏ phút %s" title="Thẻ đỏ phút %s" src="' . $this->view->configView['images'] . '/icons/icon_thedo.gif">',
            4 => '<img alt="Vào sân phút %s" title="Vào sân phút %s" src="' . $this->view->configView['images'] . '/icons/icon_vaosan.gif">',
            5 => '<img alt="Ra sân phút %s" title="Ra sân phút %s" src="' . $this->view->configView['images'] . '/icons/icon_rasan.gif">'
        );

        $arrPositions = array(
            'G'   => 'Thủ môn',
            'D'   => 'Hậu vệ',
            'M'   => 'Tiền vệ',
            'F'   => 'Tiền đạo',
        );

        //$playerInfo['PlayerName'] = '<a title="' . $playerInfo['PlayerName'] . '" href="' . $this->view->configView['url'] . '/cau-thu/' . Fpt_Filter::setSeoLink($playerInfo['PlayerName']) . '-' . $playerInfo['PlayerID'] . '.html" class="txt_black_hover_blue">' . $playerInfo['PlayerName'] . '</a>';
        $playerInfo['PlayerName'] = $playerInfo['PlayerName'];

        $html = NULL;
        $divTemplate = '<div class="item_cauthu %s">
                        <div class="w70">
                        %s
                        <span class="so_ao">%s</span>
                        </div>
                        <div class="w190">
                        <b>%s</b>
                        %s
                        </div>
                        <div class="w50">
                        <b>%s</b>
                        </div>
                        <div class="clear">
                        </div>
                        </div>';
        if ((!$substitution && $playerInfo['Type'] != 2) || ($bool == TRUE))
        {
            if (empty($playerInfo['Event']))
            {
                if ($playerInfo['Type'] != 2)
                    $html = sprintf($divTemplate, NULL, NULL, $playerInfo['NumCoat'], $playerInfo['PlayerName'], NULL, $arrPositions[$playerInfo['Position']]);
            }
            else
            {
                $arrGroupEvent = explode('|', $playerInfo['Event']);
                $tempEvent = '';
                $pos1 = $pos2 = NULL;
                foreach ($arrGroupEvent as $groupEvent)
                {
                    $event = explode(':', $groupEvent);
                    $tempEvent .= sprintf($arrEvents[$event[1]], $event[0], $event[0]);
                    if ($event[1] == 4)
                    {
                        $pos1 = 'cauthu_vaosan';
                        $pos2 = '<span class="tree_vao_ra_san">&nbsp;</span>';
                    }
                    else if ($event[1] == 5)
                    {
                        $pos1 = 'cauthu_rasan';
                    }
                }
                $html = sprintf($divTemplate, NULL, NULL, $playerInfo['NumCoat'], $playerInfo['PlayerName'], NULL, $arrPositions[$playerInfo['Position']]);
                //$html = sprintf($divTemplate, $pos1, $pos2, $playerInfo['NumCoat'], $playerInfo['PlayerName'], $tempEvent, $arrPositions[$playerInfo['Position']]);
            }
        }
        else
        {
            if ($playerInfo['Type'] == '2')
            {
                $html = sprintf($divTemplate, NULL, NULL, $playerInfo['NumCoat'], $playerInfo['PlayerName'], NULL, $arrPositions[$playerInfo['Position']]);
            }
        }

        return $html;
    }
}