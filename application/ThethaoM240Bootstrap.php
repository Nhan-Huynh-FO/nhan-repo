<?php

/**
 * @author      :   Lamtx
 * @name        :   ThethaoM240Bootstrap
 * @copyright   :   Fpt Online
 * @todo        :   Bootstrap Controller
 */
class ThethaoM240Bootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{

    public function run()
    {
        try
        {
            $front = $this->getResource('FrontController');
            $front->throwExceptions(true);
            $front->addModuleDirectory(APPLICATION_PATH . '/modules');
            $front->setDefaultModule('default');
            $front->registerPlugin(new Thethao_Plugin_EnvM240());

            $default = $front->getDefaultModule();

            if (null === $front->getControllerDirectory($default))
            {
                throw new Zend_Application_Bootstrap_Exception(
                        'No default controller directory registered with front controller'
                );
            }
            $front->setParam('bootstrap', $this)->setParam('noErrorHandler', true)->returnResponse(true);
            $response = $front->dispatch();

            if ($front->getRequest()->getParam('cache_file', 0))
            {
                $body = $response->getBody();
                $Time = time();
                $ETag = md5($body);
                header('Cache-Control: max-age=180');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $Time) . ' GMT');
                header('ETag: ' . $ETag);
                $key = 'CACHE_PAGE_THETHAO_V3_' . DEVICE_ENV . '_'  . md5($_SERVER['REQUEST_URI']);
                apc_store($key, array('time' => $Time, 'etag' => $ETag, 'content' => $body), TIME_CACHE);
            }
            $response->sendResponse();
        }
        catch (Exception $e)
        {

            header('Location:http://vnexpress.net/merror.html');
            exit;
        }
    }

}
