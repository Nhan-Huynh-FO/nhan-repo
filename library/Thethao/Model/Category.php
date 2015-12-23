<?php

/**
 * @author      :   LamTX
 * @name        :   Thethao_Category
 * @copyright   :   Fpt Online
 * @todo        :   Cate model
 * @return      :   Thethao_Model_Category
 */
class Thethao_Model_Category extends Thethao_Model
{

    protected static $_instance = null;
    protected $_menu;
    protected $_cate;
    protected $_key_cache;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @return Thethao_Model_Category
     * @author LamTX
     */
    public function __construct()
    {
        //get config key caching
        $key = Thethao_Global::getConfig('caching');
        //init News key
        $this->_cate = null;
        $this->_menu = null;
        $this->_key_cache = $key['cate_full'];
        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Category
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
     * @todo get id by code
     * @param <string> $strCode
     * @return <int>
     * @author LamTX
     */
    public function setCate($arrCate)
    {
        $this->getFullCategoryByParentId();
        $this->_cate += $arrCate;
        return $this;
    }

    /**
     * @todo get menu
     * @param <string> $section
     * @return <array>
     * @author LamTX
     */
    public function getMenu($section = null)
    {
        if ($this->_menu == null)
        {
            $this->_cate = null;
            $this->getFullCategoryByParentId();
        }
        if ($section != null)
        {
            return $this->_menu["$section"];
        }
        return $this->_menu;
    }

    /**
     * @todo get list cate id of parent
     * @param <string> $cate_parent
     * @return <array>
     * @author LamTX
     */
    public function getListCateIdOfParent($cate_parent)
    {
        $this->getFullCategoryByParentId();
        if (array_key_exists($cate_parent, $this->_cate))
        {
            return $this->_cate["$cate_parent"]['child'];
        }
        return array();
    }

    /**
     * @todo get id by code
     * @param <string> $strCode
     * @return <int>
     * @author LamTX
     */
    public function getIdByCode($strCode, $intParent = false)
    {
        $this->getFullCategoryByParentId();
        foreach ($this->_cate as $row)
        {
            if ($intParent)
            {
                if ($strCode == $row['catecode'] && $row['parent_id'] == $intParent)
                {
                    return $row['category_id'];
                }
            }
            else
            {
                if ($strCode == $row['catecode'])
                {
                    return $row['category_id'];
                }
            }
        }
        return 0;
    }

    /**
     * @todo get detail by cate id
     * @param <int> $intId
     * @return <array>
     * @author LamTX
     */
    public function getDetailByCateId($intId)
    {
        $this->getFullCategoryByParentId();
        return array_key_exists($intId, $this->_cate) ? $this->_cate[$intId] : array();
    }

    /**
     * @todo get detail by array cate
     * @param <array> $arrCateId
     * @return <array>
     * @author LamTX
     */
    public function getDetailByArrCate($arrCateId)
    {
        $return = array();
        $this->getFullCategoryByParentId();
        foreach ($arrCateId as $cateId)
        {
            $return[$cateId] = array_key_exists($cateId, $this->_cate) ? $this->_cate[$cateId] : array();
        }
        return $return;
    }

    /**
     * @todo get code by cate id
     * @param <int> $intId
     * @return <string>
     * @author LamTX
     */
    public function getCodeByCateId($intId)
    {
        $this->getFullCategoryByParentId();
        return array_key_exists($intId, $this->_cate) ? $this->_cate[$intId]['catecode'] : '';
    }

    /**
     * Get full category include all sub category
     * @return array
     * @author LamTX
     */
    public function getFullCategoryByParentId( $preCache = false )
    {
        if ($this->_cate == null)
        {
            $intCategoryID = SITE_ID;

            // Get instance memcache
            $memcacheInstance = Thethao_Global::getCache();

            $keyCache = vsprintf($this->_key_cache, array($intCategoryID));
            // Get full category from Memcache
            $arrMenuCate = $memcacheInstance->read($keyCache);
            //$arrFullCatInfo = array();
            // Data not in cache
            if ($preCache || empty($arrMenuCate['menu']) && empty($arrMenuCate['cate']))
            {
                // Get application config
                $config = Thethao_Global::getApplicationIni();

                // Get instance mysql
                $cateMysql = $this->factory('Category', $config['database']['default']['adapter']);

                $arrMenuCate = array('menu'=>array(), 'cate' => array());

                // Get cate in DB
                $arrFullCatInfo = $cateMysql->getFullCategoryByParentId($intCategoryID);

                if (!empty($arrFullCatInfo))
                {
                    $arrTemp = array();
                    foreach ($arrFullCatInfo as $detail)
                    {
                        $detail['child']    = $detail['child_recursive'] = array();

                        //update cate share_url
                        if ( $detail['parent_id']==SITE_ID)
                        {
                            switch ($detail['category_id'])
                            {
                                case CATE_ID_VIDEO:
                                case CATE_ID_PHOTO:
                                case CATE_ID_FIXTURE:
                                    $detail['link'] = '/'  . $detail['catecode'];
                                    break;
                                case SITE_ID:
                                    $detail['link'] = '/';
                                    break;
                                default :
                                    $detail['link'] = '/tin-tuc/' . $detail['catecode'];
                                    break;
                            }
                        }
                        else
                        {
                            //check world cup
                            if ($detail['parent_id']==WORLD_CUP || $detail['parent_id'] == SEA_GAMES)
                            {
                                $detail['link'] = '/' . $detail['catecode'];
                            }
                            else
                            {
                                $detail['link'] = $arrTemp[$detail['parent_id']]['link'] . '/' . $detail['catecode'];
                            }

                        }//end if
                        //explode parent => array
                        $detail['full_parent'] = $detail['full_parent_original'] = explode(',', $detail['full_parent']);
                        //remove site_id
                        unset($detail['full_parent'][0]);
                        //pre order key
                        $detail['full_parent']          = array_values($detail['full_parent']);

                        //assign array temp
                        $arrTemp[$detail['category_id']] = $detail;

                        //update share_url + parent to original array
                        $arrFullCatInfo[$detail['category_id']] = $detail;

                        //if this cate have parent
                        if ( !empty($arrTemp[$detail['category_id']]['full_parent_original']) )
                        {
                            foreach ( $arrTemp[$detail['category_id']]['full_parent_original'] as $intParentId )
                            {
                                //if not SITE_ID (cate_id=parent_id)
                                if ( $intParentId != $detail['category_id'] )
                                {
                                    //update child
                                    $arrTemp[$intParentId]['child_recursive'][] = $detail['category_id'];
                                    $arrFullCatInfo[$intParentId]['child_recursive'][] = $detail['category_id'];
                                }//end if
                            }//end foreach

                            //update current cate child list
                            if ( $intParentId != $detail['category_id'] )
                            {
                                $arrTemp[$detail['parent_id']]['child'][$detail['category_id']] = $detail;
                                $arrFullCatInfo[$detail['parent_id']]['child'][] = $detail['category_id'];
                            }//end if
                        }//end if

                    }//end foreach
                    //format output
                    $arrMenuCate['menu'] = array(SITE_ID => $arrTemp[SITE_ID]);
                    $arrMenuCate['cate'] = $arrFullCatInfo;

                    //wriet memcache
                    $memcacheInstance->write($keyCache, $arrMenuCate);

                    //check from job to write memcached hcm
                    if ( $preCache )
                    {
                        Thethao_Global::writeMemcache($keyCache, $arrMenuCate, 'all');
                    }//end if
                }//end if
            }//end if
            $this->_menu = $arrMenuCate['menu'];
            $this->_cate = $arrMenuCate['cate'];
        }//end if

        return $this->_cate;
    }

    /**
     * @todo get link by cate id
     * @param <int> $intCateId
     * @return <string>
     * @author LamTX
     */
    public function getLinkByCateId($intCateId)
    {
        if ($this->_cate == null)
        {
            return '#';
        }
        if (!array_key_exists($intCateId, $this->_cate))
        {
            return '/';
        }

        return $this->_cate[$intCateId]['link'];
    }

    /**
     * get Info Cate By Block
     * @param array $arrParams
     * @return array
     * @author LamTX
     */
    public function getInfoCateByBlock($arrParams)
    {
        $arrCate = $this->getDetailByCateId($arrParams['category_id']);
        $arrResult['cate'] = array(
            'id' => $arrParams['category_id'],
            'name' => $arrCate['catename'],
            'link' => $arrCate['link'],
            'share_url' => $arrCate['link'],
            'child' => array());
        if (isset($arrParams['child']) && $arrParams['child'] == 1 && !empty($arrCate['child']))
        {
            $arrChild = $this->getDetailByArrCate($arrCate['child']);

            $arrDataChild = array();
            foreach ($arrChild as $child)
            {
                if ($child['cate_type'] != 1)
                {
                    continue;
                }
                $arrDataChild[] = array(
                    'name' => $child['catename'],
					'link' => $child['link'],
                    'share_url' => $child['link']
                );
            }
            $arrResult['cate']['child'] = $arrDataChild;
        }
        return $arrResult;
    }

    /**
     * clear cache CategoryFull
     * @return array
     * @author PhuongTN
     */
    public function editCategory()
    {
        //default response
        $response = array();
        //generate key cache
        $keyCache = vsprintf($this->_key_cache, array(SITE_ID));
        //delete memcache multi zone
        $response = Thethao_Global::deleteMemcache(array($keyCache), 'all');
        //precaching
        $this->getFullCategoryByParentId(true);
        //return response
        return $response;
    }

    /**
     * Get article by category code
     * @param string $strCode
     * @return <array> Article information
     * @author: phongtx@fpt.net
     */
    public function getCategoryByCode($strCode)
    {
        try
        {
            $cateID = $this->getIdByCode($strCode);
            if ($cateID > 0)
            {
                $arrInfo = $this->getDetailbyCateId($cateID);
                return $arrInfo;
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return false;
    }

}