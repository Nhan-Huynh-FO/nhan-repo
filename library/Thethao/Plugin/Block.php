<?php

/**
 * @author      :   Lamtx
 * @name        :   Thethao_Plugin_Block
 * @copyright   :   Fpt Online
 * @todo        :   Plugin Block
 */
class Thethao_Plugin_Block
{

    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    protected $_data;
    protected $_exclude;

    /**
     * Avoid directly create instance
     * @author LamTX
     */
    protected function __construct()
    {
        $this->_layout = null;
        $this->_exclude = array();
    }

    /**
     * Retrieve singleton instance
     * @return Thethao_Plugin_Block
     * @author LamTX
     */
    public static function getInstance()
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Reset the singleton instance
     * @return void
     * @author LamTX
     */
    public static function resetInstance()
    {
        self::$_instance = null;
        $this->_exclude = array();
        return;
    }

    /**
     *  set Id Exclude
     * @param array $arrId
     * @return Thethao_Plugin_Block
     * @author LamTX
     */
    public function setExclude($arrId)
    {
        $this->_exclude = array_merge($this->_exclude, (array) $arrId);
        return $this;
    }

    /**
     * get Id Exclude
     * @return array Exclude
     * @author LamTX
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * get count Id Exclude
     * @return int
     * @author LamTX
     */
    public function getTotalExclude()
    {
        return count($this->_exclude);
    }

    /**
     * get Data for Block
     * @return void
     * @author LamTX
     */
    public function getBlockAll($pagecode = false)
    {
        $class = array();

        //check get block detail
        if ($this->_data == null)
        {
            if ($pagecode === false)
            {
                //get object Request
                $request = Zend_Controller_Front::getInstance()->getRequest();
                //get module application
                $module = $request->getModuleName();
                //get controller application
                $controller = $request->getControllerName();
                //get action application
                $action = $request->getActionName();
                //get cate for block
                $cate = $request->getParam('block_cate', SITE_ID);
            }
            else
            {
                $arrParams = explode('_', $pagecode);
                //get module application
                $module = $arrParams[0];
                //get controller application
                $controller = $arrParams[1];
                //get action application
                $action = $arrParams[2];
                //get cate for block
                $cate = $arrParams[3];
            }
            //get block detail for page
            $pageCode = $module . '_' . $controller . '_' . $action . '_' . $cate;

            if ($controller=='detail')
            {
                //check device is mobile, then not get block
				if (DEVICE_ENV == 1)
				{
					$this->_data = '-1';
					return;
				}
                //check cate to get list block
                if ($cate == SITE_ID)
                {
                    $pageCode = $module . '_index_index_' . $cate;
                }
                else
                {
                    $pageCode = $module . '_category_index_' . $cate;
                }
            }
            //get model block in fw
            $modelBlock = Fpt_Data_News_Block::getInstance();
            $layout = $modelBlock->getBlockByPage($pageCode);

            if (empty($layout) || $layout == '-1')
            {
                $this->_data = '-1';
                return;
            }
            //get Instance Article
            $objArtilce = Fpt_Data_News_Article::getInstance();
            //each pos block
            foreach ($layout as $pos => $blocks)
            {
                //each get data type left or right
                foreach ($blocks as $key => $block)
                {
                    //set default data block
                    $block['data'] = array();
                    //check type block
                    if ($block['type'] == 'dynamic')
                    {
                        //get paramt to block
                        $arrParam = $block['param'];
                        //get paramt to block
                        $arrParam['exclude'] = isset($arrParam['exclude']) ? (int) $arrParam['exclude'] : 0;

                        // get class block
                        $className = $block['class'];
                        // get function block
                        $functionName = $block['function'];
                        //check exclude data require limit

                        if (!isset($class[$className]))
                        {
                            $class[$className] = new $className();
                        }
                        $data = $class[$className]->{$functionName}($arrParam);


                        //get data for block
                        //$data = call_user_func_array(array(new $className(), $functionName), array($arrParam));
                        //check data empty
                        if (is_array($data) && !empty($data))
                        {
                            //set data to block
                            $block['data'] = $data;
                            //print_r($data);
                            if (!empty($data['data']))
                            {
                                $objArtilce->setIdBasic($data['data']);
                            }
                        }
                    }
                    //set data to object
                    $this->_data["$pos"][$key] = $block;
                }
            }
        }
        return;
    }

    /**
     *  get Data by Block add Exclude
     * @param object $objName
     * @param string $functionName
     * @param array $arrParam
     * @return array
     * @author LamTX
     */
    public function getDataBlockByExclude($objName, $functionName, $arrParam)
    {
        //get limit data for block
        $limit = $arrParam['limit'];
        //match limit add to exclude
        $arrParam['limit'] += $this->getTotalExclude();
        //get data for block
        $arrData = call_user_func_array(array($objName, $functionName), array($arrParam));

        //check data empty
        if (!empty($arrData['data']))
        {
            //get data diff Exclude and Limit
            $arrData['data'] = array_slice(array_diff($arrData['data'], $this->getExclude()), 0, $limit);
        }
        return $arrData;
    }

    /**
     * render Block
     * @param string $pos
     * @return string
     * @author LamTX
     */
    public function renderBlock($pos, $pagecode = false)
    {
        //set default data
        $strReturn = '';
        //get data model
        $this->getBlockAll($pagecode);
        //get object
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        //check pos in block
        if ($this->_data && $this->_data != -1 && $pos && array_key_exists($pos, $this->_data))
        {
            //each position detail
            foreach ($this->_data["$pos"] as $block)
            {
                //set data to view
                $view->data = $block['data'];
                //set layout to view
                $view->layout = $block['layout'];

                if ($block['type'] == 'dynamic')
                {
                    //render block
                    $strReturn .= $view->render('block/' . $block['view'] . '.phtml');
                }
                else
                {
                    preg_match_all('/<iframe.*?><\/iframe>/s', $block['view'], $result);
                    if (isset($result[0]) && count($result[0]) > 0)
                    {
                        $new_iframe_arr = array();
                        foreach ($result[0] as $item)
                        {
                            // trường hợp iframe chứa class
                            if (strpos($item, 'class') == true)
                            {
                                $new_iframe_arr[] = '<div class="ad_wrapper_protection">' . str_replace('class="', 'class="ad_frame_protection ', $item) . '</div>';
                            }
                            else
                            {
                                $new_iframe_arr[] = '<div class="ad_wrapper_protection">' . str_replace('<iframe', '<iframe class="ad_frame_protection"', $item) . '</div>';
                            }
                        }
                        $block['view'] = str_replace($result[0], $new_iframe_arr, $block['view']);
                    }

                    $strReturn .= '<div class="box_category width_common">' . $block['view'] . '</div>';
                }
            }
        }
        //return block
        return $strReturn;
    }

}