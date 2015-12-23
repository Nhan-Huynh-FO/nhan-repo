<?php

/**
 * @author      :   Lamtx
 * @name        :   Thethao_Business_Block
 * @copyright   :   Fpt Online
 * @todo        :   Article business
 * @return      : Thethao_Business_Article_Adapter_Mysql
 */
class Thethao_Business_Article_Adapter_Mysql
{

    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @author LamTX
     */
    public function __construct()
    {

    }

    /**
     * Get singletom instance
     * @return Thethao_Business_Article_Adapter_Mysql
     * @author LamTX
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
     * insert post article
     * @param array $arrParams
     * @return int
     * @author Phongtx
     */
    public function insertPostArticle($arrParams)
    {
        $intResult = false;
        try
        {
            // Get DB Object
            $db = Thethao_Global::getDB('sport', 'master');
            // Prepare SQL
            $stmt = $db->prepare('CALL sp_ent_insertShareMusic(:p_userid, :p_fullname, :p_email, :p_title, :p_lead, :p_content, :p_status, :p_sharetype, :p_categoryid, @p_id);');
            // Bind params
            $stmt->bindParam('p_userid', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_fullname', $arrParams['txtName'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrParams['txtEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_title', $arrParams['txtTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_lead', $arrParams['txtLead'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['txtContent'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_sharetype', $arrParams['type'], PDO::PARAM_INT);
            $stmt->bindParam('p_categoryid', $arrParams['cateid'], PDO::PARAM_INT);
            // Execute
            $stmt->execute();
            // Close cursor
            $stmt->closeCursor();
            $stmt = $db->query("SELECT @p_id");
            //Fetch Result
            $intResult = $stmt->fetchColumn();
            // Release
            unset($stmt);
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        // Return result
        return $intResult;
    }

}
