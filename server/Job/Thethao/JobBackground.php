<?php

/**
 * @copyright   FOSP
 * @version     20121109
 * @todo        Job background for tẹnnis
 * @name        JobBackground
 * @author      QuangTM
 */
class Job_Thethao_JobBackground
{

    /**
     * Build key cache for each item in array
     * @param string $item
     * @param int $key
     * @param string $prefix
     */
    static public function buildKeyCache(&$item, $key, $prefix)
    {
        $item = $prefix . $item;
    }

    /**
     * Pre-caching for tennis player information.<br />
     * 2 case in this function.<br />
     * Clear cache after that read from model to write new data into cache.
     * @param array $params Must has key arrPlayerIds. That is array player id
     * @author QuangTM
     */
    static public function preCachingTennisPlayer($params)
    {
        //set array player id
        $arrPlayerIds   = $params['arrPlayerIds'];

        //set array player id to NULL
        $params['arrPlayerIds'] = array();

        //array chunk
        $arrChunk   = array_chunk($arrPlayerIds, 10);

        //loop
        foreach ( $arrChunk as $listId )
        {
			//new object framework
			$modelObject = new Fpt_Data_Materials_Object();

			// Get tennis model
			$tennisModel = new Thethao_Model_Tennis();

            $model = $modelObject->getTennis();
            foreach ( $listId as $playerID )
            {
                $model->updateObject($playerID);
            }//end foreach

            //precache
            $params['arrPlayerIds'] = $listId;
            $tennisModel->preCachingTennisPlayer($params);

            //close resource
            Thethao_Global::closeResource();
            Fpt_Data_Model::_destruct();
        }//end foreach
    }

    /**
     * Pre-caching for tennis table ranking.<br />
     * Clear cache after that read from model to write new data into cache.
     * @param array $params Must has key year and gender
     * @author QuangTM
     */
    static public function preCachingTennisRanking($params)
    {
        // Get tennis model
        $tennisModel = new Thethao_Model_Tennis();
        $tennisModel->preCachingTennisRanking($params);
    }

    /**
     * Pre-cache tennis match information.<br />
     * Push data tennis match and tennis player attend this match into redis key
     * @param array $params Must has key arrMatchIDs
     * @return boolean
     * @author QuangTM
     */
    static public function preCachingTennisMatch($params)
    {
        // Validate input
        if (!isset($params['arrMatchIDs']) || !is_array($params['arrMatchIDs']) || !count($params['arrMatchIDs']))
        {
            return FALSE;
        }
        // Get tennis model
        $tennisModel = new Thethao_Model_Tennis();
        $tennisModel->preCachingTennisMatch($params);
    }

}