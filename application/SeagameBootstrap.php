<?php

/**
 * @author      :   PhongTX
 * @name        :   WorldcupBootstrap
 * @copyright   :   Fpt Online
 * @todo        :   Bootstrap Controller
 */
class SeagameBootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{

    public function run()
    {
        try
        {
            $front = $this->getResource('FrontController');
            $front->throwExceptions(true);
            $front->addModuleDirectory(APPLICATION_PATH . '/modules');
            $front->setDefaultModule('seagame');
            $front->registerPlugin(new Thethao_Plugin_EnvSeagame());

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
                apc_store(KEY_CACHE_PAGE, array('time' => $Time, 'etag' => $ETag, 'content' => $body), 900);
            }
            $response->sendResponse();
        }
        catch (Exception $e)
        {
            print_r($e->getMessage());die;
            header('Location:http://vnexpress.net/'.(DEVICE_ENV == 1 ? 'm' : '').'error.html');
            exit;
        }
    }

}
