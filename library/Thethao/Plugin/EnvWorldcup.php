<?php

/**
 * @author      :   PhongTX
 * @name        :   Fpt_Plugin_Env
 * @version     :   201108
 * @copyright   :   FPT Online
 * @todo        :   Environment plugin
 * @return      :   Thethao_Plugin_EnvWorldcup
 */
class Thethao_Plugin_EnvWorldcup extends Zend_Controller_Plugin_Abstract
{
    protected $_frontController;
    //Debug mode
    private $debug = 0;
    //Xhp mode
    private $xhp = 0;
    /**
     * @author PhongTX
     */
    public function __construct()
    {
        $this->_frontController = Zend_Controller_Front::getInstance();
    }

    /**
     * Called before Zend_Controller_Front calls on the router
     * to evaluate the request against the registered routes.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        //Get debug
        $xhp_key = $request->getParam('xhp_key');
        //check request is not ajax and flash no add router
        //get route
        $router = $this->_frontController->getRouter();
        //get config from application.ini
        $arrConf = $this->_frontController->getParam('bootstrap')->getOptions();
        //Check xhp
        /*if ($arrConf['system']['profiler']['status']==1 && $xhp_key===$arrConf['system']['profiler']['xhp_key'])
        {
           $this->xhp = 1;
           $this->debug = 1;
           xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
        }*/
        if (isset($arrConf['routes']))
        {
            foreach ($arrConf['routes'] as $name => $route)
            {
                if (isset($route['route']))
                {
                    $class = (isset($route['type'])) ? $route['type'] : 'Zend_Controller_Router_Route';
                    $defs = (isset($route['defaults'])) ? $route['defaults'] : array();
                    $map = (isset($route['map'])) ? $route['map'] : array();
                    $reverse = (isset($route['reverse'])) ? $route['reverse'] : null;
                    $route = new $class($route['route'], $defs, $map, $reverse);
                    $router->addRoute($name, $route);
                }
            }
        }

        //set URL to config View
        //$this->_frontController = Zend_Controller_Front::getInstance();
        //$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/configUrl.ini');
        //$router = $this->_frontController->getRouter();
        //$router->addConfig($config, 'routes');
    }

    /**
     * Called after the router finishes routing the request.
     * @param Zend_Controller_Request_Abstract $request
     * @author LamTX
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
         //print_r($request->getParams());die();
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     * @param Zend_Controller_Request_Abstract $request
     * @author LamTX
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        //get z front
        $front = Zend_Controller_Front::getInstance();
        //get dispatch
        $dispatcher = $front->getDispatcher();

        //check can dispatchable or not
        if (!$dispatcher->isDispatchable($request))
        {
            //redirect error page
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl('http://vnexpress.net/'.(DEVICE_ENV == 1 ? 'm' : '').'error.html')->redirectAndExit();
        }
        //set layout for device
        $layout = DEVICE_ENV == 1 ? 'mobile' : 'default';

        $layoutInstance = Zend_Layout::startMvc(
                        array(
                            'layout' => $layout,
                            'layoutPath' => APPLICATION_PATH . '/layouts/scripts/worldcup',
                            'contentKey' => 'content'
                        )
        );
        $view = $layoutInstance->getView();
        $view->setHelperPath(APPLICATION_PATH . '/layouts/helpers');
        $config = $this->_frontController->getParam('bootstrap')->getOptions();
        $view->assign(array(
            'Snippet'       => '',
            'MenuActive'    => true,
            'configView'    => $config['view'],
            'imageSize'     => $config['images'][DEVICE_ENV],
            'objArticle'    => Fpt_Data_News_Article::getInstance(),
            'objObject'     => Fpt_Data_Materials_Object::getInstance()
            )
        );
    }

    /**
     * Called before an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false),
     * the current action may be skipped and/or replaced.
     * @param Zend_Controller_Request_Abstract $request
     * @author LamTX
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

    }

    /**
     * Called after an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false)),
     * a new action may be specified for dispatching.
     * @param Zend_Controller_Request_Abstract $request
     * @author LamTX
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = Zend_Layout::getMvcInstance();

        if ($layout->isEnabled())
        {
            //get cate id & cate id active
            $cateid = $request->getParam('cateid');
            $cateActiveId   = $request->getParam('cateActiveId', $cateid);
            //get instance Thethao_Model_Category
            $objCategory = Thethao_Model_Category::getInstance();
            //get menu all
            $menu = $objCategory->getMenu(SITE_ID);

            //get detail cate current
            $cateDetail = $objCategory->getDetailByCateId($cateid);
            
            //check cate detail exists
            if (!empty($cateDetail))
            {
                //get Menu Active == full parent
                $arrActiveMenu = $cateDetail['full_parent'];

                //add cate current to  Menu Active
                array_push($arrActiveMenu, $cateid, $cateActiveId);
                $arrActiveMenu = array_unique($arrActiveMenu);
                $count = count($arrActiveMenu);
                $folderHidden = false;
                if (!empty($arrActiveMenu))
                {
                    $breakCumb = $objCategory->getDetailByArrCate($arrActiveMenu);
                    if ($breakCumb[$cateActiveId]['show_folder'] == 0)
                    {
                        $folderHidden = true;
                        $breakCumbSub[$cateActiveId] = $breakCumb[$cateActiveId];
                    }
                    if (empty ($breakCumb[$cateActiveId]['child']) && (1 < $count))
                    {
                        unset($breakCumb[$cateActiveId]);
                    }
                }
                //subBreakCumb
                if (!empty($breakCumb[$cateActiveId]))
                {
                    $breakCumbSub   = $objCategory->getDetailByArrCate($breakCumb[$cateActiveId]['child']);
                    if (!empty ($breakCumbSub) || 1 < $count)
                    {
                        $count = count($breakCumbSub);
                        foreach ($breakCumb as $k => $bc)
                        {
                            if (1 != $bc['cate_type'] || !$bc['show_folder'])
                            {
                                $count--;
                                unset($breakCumbSub[$k]);
                            }
                        }

                        if (0 == $count)
                        {
                            unset($breakCumb[$cateActiveId]);
                        }
                    }
                }
                elseif (!empty($cateDetail['child']) && !$folderHidden)
                {
                    $breakCumbSub   = $objCategory->getDetailByArrCate($cateDetail['child']);
                }

            }
            else
            {
                $arrActiveMenu = $breakCumb = $breakCumbSub = array();
            }
            
            $view = $layout->getView();
            $view->assign(array(
                'arrMenu'   => $menu['child'],
                'arrActiveMenu' => $arrActiveMenu,
                'breakCumb' => $breakCumb,
                'breakCumbSub' => $breakCumbSub,
                'menuDetail' => isset($breakCumbSub[$cateActiveId]) ? $breakCumbSub[$cateActiveId] : $cateDetail,
                )
            );
        }

    }

    /**
     * Called after Zend_Controller_Front exits its dispatch loop.
     * @author LamTX
     */
    public function dispatchLoopShutdown()
    {
         //Check xhp
        if ($this->debug && $this->xhp)
        {
            //Get debug data xhprof
            $xhprof_data = xhprof_disable();
            $stringAppend = Fpt_Utility::getXhp($xhprof_data);
            $this->getResponse()->appendBody($stringAppend);
        }
    }

}