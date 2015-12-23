<?php

/**
 * @copyright   FOSP
 * @version     20120515
 * @todo        Display link match detail
 * @name        Zend_View_Helper_MatchDetailUrl
 * @author      QuangTM 
 */
class Zend_View_Helper_MatchDetailUrl extends Zend_View_Helper_Abstract
{
    /**
     *
     * @param string $action
     * @param string $teamName1
     * @param string $teamName2
     * @param int $matchID
     * @return string 
     */
    public function matchDetailUrl($action, $teamName1, $teamName2, $matchID)
    {
        return "/$action/tran-" . Fpt_Filter::setSeoLink("$teamName1 vs $teamName2 $matchID");
    }
}