<?php
/**
 * @author      :   HungNT1
 * @name        :   JobDirectUrl
 * @version     :   04172013
 * @copyright   :   My company
 */
class Job_Thethao_JobDirectUrl
{
    /**
     * Get data from site Ngoisao and update to redis
     * @param array $params - array (article_id, original_url)
     * @return Boolean
     */
    public function updateShareUrl($params)
    {
        // Default response
        $response = NULL;

        try
        {
            $article_id = intval($params['article_id']);
            $article_url = $params['original_url'];

            //get config key
            $config = Thethao_Global::getApplicationIni();

            // Get redis instance
            $redisInstance  = Thethao_Global::getRedis('article');

            //make key
            $keyCache       = Thethao_Global::makeCacheKey($config['view']['keyReis']['ngoisao']);

            //set redis
            $response = $redisInstance->hSet($keyCache, $article_id, $article_url);

        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }

        return $response;

    }
}