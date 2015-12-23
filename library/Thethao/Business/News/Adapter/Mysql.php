<?php

/**
 * @author      :   PhuongTN
 * @name        :   Thethao_Business_News_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   News business
 * @return      : Thethao_Business_News_Adapter_Mysql
 */
class Thethao_Business_News_Adapter_Mysql extends Thethao_Business_News_Adapter_Abstract
{

    protected static $_instance = null;

    /**
     * Constructor of class
     * @author PhuongTN
     */
    public function __construct()
    {

    }

    /**
     * Get singleton instance
     * @return Thethao_Business_News_Adapter_Mysql
     */
    public final static function getInstance()
    {
        //Check instance
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        //Return instance
        return self::$_instance;
    }

    /**
     * Get list article ids by rule 1, set on folder
     * @param type $arrParams
     * @return type array
     * @author HungNT1
     */
    public function getListArticleIdsByRule1($arrParams)
    {
        if (!isset($arrParams['article_type']) || $arrParams['article_type'] == 0)
        {
            $arrParams['article_type'] = NULL;
        }
        $site_id = $arrParams['category_id']==SITE_ID?NULL:SITE_ID;

        $arrResult = array('total' => 0, 'data' => array());

        $arrParams['from_date'] = isset($arrParams['from_date']) ? $arrParams['from_date'] : NULL;
        $arrParams['to_date']   = isset($arrParams['to_date']) ? $arrParams['to_date'] : NULL;

        try
        {
            // Get DB
            $db_s = Thethao_Global::getDB('sport', 'slave');
            $stmt = $db_s->prepare('CALL sp_getNewsByRule1(:p_siteid, :p_categoryid, :p_articletype, :p_fromdate, :p_todate, :p_offset, :p_LIMIT)');
            //Bind param
            $stmt->bindParam('p_siteid', $site_id);
            $stmt->bindParam('p_categoryid', $arrParams['category_id']);
            $stmt->bindParam('p_articletype', $arrParams['article_type'], PDO::PARAM_STR);
            $stmt->bindParam('p_fromdate', $arrParams['from_date'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['to_date'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['offset'], PDO::PARAM_INT);
            $stmt->bindParam('p_LIMIT', $arrParams['limit'], PDO::PARAM_INT);

            // Fetch result
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {

                $arrResult['data'][$row['article_id']] = $row['score'];
            }
            // Close cursor
            $stmt->closeCursor();

            // Query total row
            $stmt = $db_s->prepare('CALL sp_getTotalByRule1(:p_siteid, :p_categoryid, :p_articletype, :p_fromdate, :p_todate)');
            // Bind param
            $stmt->bindParam('p_siteid', $site_id);
            $stmt->bindParam('p_categoryid', $arrParams['category_id']);
            $stmt->bindParam('p_articletype', $arrParams['article_type'], PDO::PARAM_STR);
            $stmt->bindParam('p_fromdate', $arrParams['from_date'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['to_date'], PDO::PARAM_INT);
            // Exec func
            $stmt->execute();
            // Fetch total
            $arrResult['total'] = $stmt->fetchColumn();

            // Release
            unset($stmt);
        }
        catch (Zend_Exception $ex)
        {
            Fpt_Data_Model::sendLog($ex);
        }
        return $arrResult;
    }

    /**
     * Get list article with rule 2, list on folder
     * @param type $arrParams
     * @return type
     * @author PhongTX
     */
    public function getListArticleIdsByRule2($arrParams)
    {

        $arrResult = array('total' => 0, 'data' => array());
        try
        {
            // Get DB
            $db_s = Thethao_Global::getDB('sport', 'slave');
            $stmt = $db_s->prepare('CALL sp_getNewsByRule2(:p_categoryid, :p_fromdate, :p_todate, :p_offset, :p_LIMIT)');
            //Bind param
            $stmt->bindParam('p_categoryid', $arrParams['category_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_fromdate', $arrParams['from_date'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['to_date'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['offset'], PDO::PARAM_INT);
            $stmt->bindParam('p_LIMIT', $arrParams['limit'], PDO::PARAM_INT);

            // Fetch result
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {

                $arrResult['data'][$row['article_id']] = $row['score'];
            }
            // Close cursor
            $stmt->closeCursor();

            // Query total row
            $stmt = $db_s->prepare('CALL sp_getTotalByRule2(:p_categoryid, :p_fromdate, :p_todate)');
            // Bind param
            $stmt->bindParam('p_categoryid', $arrParams['category_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_fromdate', $arrParams['from_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_todate', $arrParams['to_date'], PDO::PARAM_INT);
            // Exec func
            $stmt->execute();
            // Fetch total
            $arrResult['total'] = $stmt->fetchColumn();

            // Release
            unset($stmt);
        }
        catch (Zend_Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return $arrResult;
    }

    /**
     * Get news by rule 3: folder X + subfolder X  => order by publish_time
     * @param array $arrParams array(category_id, offset, limit)
     * @return array
     * @author PhuongTN
     */
    public function getListArticleIdsByRule3($arrParams)
    {
        $arrResult = array('data' => array(), 'total' => 0);

        $arrParams['article_type']  = $arrParams['article_type'] == 0 ? NULL : $arrParams['article_type'];
        $arrParams['p_excludecate'] = NULL;
        $site_id = SITE_ID;
        try
        {
            // Get DB
            $db_s = Thethao_Global::getDB('sport', 'slave');
            $stmt = $db_s->prepare('CALL sp_getNewsByRule3(:p_siteid, :p_articletype, :p_categoryid, :p_excludecate, :p_offset, :p_limit)');

            //Bind param
            $stmt->bindParam('p_siteid', $site_id, PDO::PARAM_INT);
            $stmt->bindParam('p_articletype', $arrParams['article_type'], PDO::PARAM_STR);
            $stmt->bindParam('p_categoryid', $arrParams['category_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_excludecate', $arrParams['p_excludecate'], PDO::PARAM_STR);
            $stmt->bindParam('p_offset', $arrParams['offset'], PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $arrParams['limit'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();

            // Fetch result
            while ($row = $stmt->fetch())
            {
                $arrResult['data'][$row['article_id']] = $row['publish_time'];
            }
            //set total
            $arrResult['total'] = count($arrResult['data']);

            /*
            // Query total row
            $stmt = $db_s->prepare('CALL sp_getTotalByRule3(:p_siteid, :p_articletype, :p_categoryid, :p_excludecate)');
            // Bind param
            $stmt->bindParam('p_siteid', $site_id, PDO::PARAM_INT);
            $stmt->bindParam('p_articletype', $arrParams['article_type'], PDO::PARAM_STR);
            $stmt->bindParam('p_categoryid', $arrParams['category_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_excludecate', $arrParams['p_excludecate'], PDO::PARAM_STR);

            // Exec func
            $stmt->execute();
            // Fetch total
            $arrResult['total'] = $stmt->fetchColumn();
            */
            // Release
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        }
        catch (Zend_Exception $ex)
        {
            Thethao_Global::sendLog($ex, 1);
        }
        return $arrResult;
    }

    /**
     * Get Article Id By Product Id
     * @param array $arrParams
     * @return array $arrResult
     * @author HungNT1
     */
    public function getArticleIdByProductId($intId)
    {
        $arrResult = array();
        $intSite = SITE_ID;

        try
        {
            //get db slave
            $db_s = Thethao_Global::getDB('sport', 'slave');
            $stmt = $db_s->prepare('CALL sp_getArticleByProductId(:p_productid, :p_siteid)');

            //Bind param
            $stmt->bindParam('p_productid', $intId, PDO::PARAM_INT);
            $stmt->bindParam('p_siteid', $intSite, PDO::PARAM_INT);

            // Fetch result
            $stmt->execute();

            //do while
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $arrResult[] = $row['article_id'];
            }

            // Close cursor
            $stmt->closeCursor();

            // Release
            unset($stmt);
        }
        catch (Zend_Db_Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $arrResult;
    }
    
    public function getListQuestion($arrParams)
    {
        //default return
        $arrResult = array();
        try
        {
            
            //get db slave
            $db_s    = Thethao_Global::getDB('interaction', 'slave');
            // Call sp get detail book
            $stmt = $db_s->prepare('CALL sp_getGroupQuestionsByDate(:p_fromdate , :p_todate)');
            // Bind param
           
			$stmt->bindParam('p_fromdate', $arrParams['fromdate'], PDO::PARAM_STR);
            $stmt->bindParam('p_todate', $arrParams['todate'], PDO::PARAM_INT);
            
            // Execute sp
            $stmt->execute();
			
            //fetch result
            $arrResult = $stmt->fetchAll();
            			
            // Close cursor
            $stmt->closeCursor();
            // Release
			
            unset($stmt);
        }
        catch (Zend_Db_Exception $ex)
        {
            //log error
			
            Thethao_Global::sendLog($ex);
        }
		
        return $arrResult;
    }

    public function getListAward($arrParams)
    {
        //default return
        $arrResult = array();
        try
        {            
            //get db slave
            $db_s    = Thethao_Global::getDB('interaction', 'slave');
            // Call sp get detail book
            $stmt = $db_s->prepare('CALL sp_getListAwardResult(:p_object_type, :p_award , :p_fromdate , :p_todate)');
            // Bind param
            $stmt->bindParam('p_object_type', $arrParams['object_type'], PDO::PARAM_INT);
			$stmt->bindParam('p_award', $arrParams['award'], PDO::PARAM_INT);
            $stmt->bindParam('p_fromdate', $arrParams['fromdate'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['todate'], PDO::PARAM_INT);
            
            // Execute sp
            $stmt->execute();
			
            //fetch result
            $arrResult = $stmt->fetchAll();            			
            // Close cursor
            $stmt->closeCursor();
            // Release
			
            unset($stmt);
        }
        catch (Zend_Db_Exception $ex)
        {
            //log error			
            Thethao_Global::sendLog($ex);
        }
		
        return $arrResult;
    }
}