<?php

/**
 * @copyright   FOSP
 * @version     20120620
 * @todo        Helper display competition's information
 * @name        Zend_View_Helper_CompetitionInfo
 * @author      QuangTM
 */
class Zend_View_Helper_CompetitionInfo extends Zend_View_Helper_Abstract
{

    public function competitionInfo($competitionInfo, $arrTeams, $activeTeam = FALSE, $teamActiveID = 0)
    {
        $formatLink = '<a class="txt_black_hover_blue" href="%s" title="%s">%s</a>';
        $p1 = sprintf($formatLink, '/doi-bong/' . Fpt_Filter::setSeoLink($arrTeams[$competitionInfo['team1']]['name']. ' ' . $competitionInfo['team1']) . '.html', $arrTeams[$competitionInfo['team1']]['name'], $arrTeams[$competitionInfo['team1']]['name']);
        $p2 = sprintf($formatLink, '/thong-tin-truoc-tran/tran-' . Fpt_Filter::setSeoLink($arrTeams[$competitionInfo['team1']]['name'] . ' vs ' . $arrTeams[$competitionInfo['team2']]['name']) . '-' . $competitionInfo['id'], $arrTeams[$competitionInfo['team1']]['name'] . ' vs ' . $arrTeams[$competitionInfo['team2']]['name'], "{$competitionInfo['goal1']} - {$competitionInfo['goal2']}");
        $p3 = sprintf($formatLink, '/doi-bong/' . Fpt_Filter::setSeoLink($arrTeams[$competitionInfo['team2']]['name']. ' ' . $competitionInfo['team2']) . '.html', $arrTeams[$competitionInfo['team2']]['name'], $arrTeams[$competitionInfo['team2']]['name']);
        if (!$activeTeam)
        {
            if ($competitionInfo['goal1'] > $competitionInfo['goal2'])
            {
                $p1 = "<b>$p1</b>";
            }
            else if ($competitionInfo['goal1'] < $competitionInfo['goal2'])
            {
                $p3 = "<b>$p3</b>";
            }
        }
        else
        {
            if($competitionInfo['team1'] == $teamActiveID)
                $p1 = "<b>$p1</b>";
            else if($competitionInfo['team2'] == $teamActiveID)
                $p3 = "<b>$p3</b>";
        }
        return "$p1 $p2 $p3";
    }

}