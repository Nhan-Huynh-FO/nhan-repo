<?php
/**
 * @todo category adapter mysql
 * @return Thethao_Business_Category_Adapter_Mysql
 * @author PhuongTN
 */
class Thethao_Business_Category_Adapter_Mysql extends Thethao_Business_Category_Adapter_Abstract
{
    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @author PhuongTN
     */
    public function __construct()
    {
        
    }

    /**
     * Get singletom instance
     * @return <object>
     * @author PhuongTN
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
     * get all category detail
     * @param int $intCategoryID
     * @return array
     * @author PhuongTN
     */
    public function getFullCategoryByParentId($intCategoryID)
    {
        $arrArticleInfo = array();
        try
        {
            $sport_m = Thethao_Global::getDB('sport', 'slave');
            // Get user activeation key
            $objStmt = $sport_m->prepare('CALL sp_getCategoryFullByParentID(:p_parentid)');
            // Bind param
            $objStmt->bindParam('p_parentid', $intCategoryID, PDO::PARAM_INT);
            // Fetch result
            $objStmt->execute();
            while ($row = $objStmt->fetch())
            {
                $arrArticleInfo[$row['category_id']] = $row;
            }
            // Close cursor
            $objStmt->closeCursor();
            // Release
            unset($objStmt);
        }
        catch (Zend_Db_Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return $arrArticleInfo;
    }
}