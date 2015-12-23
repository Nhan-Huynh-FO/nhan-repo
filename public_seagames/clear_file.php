<?php

//set secret key + array remote address
$secret_key = 'e146b700f216df93a2dd5ca6e26c7c2c';
$arrRemoteAddr = array('180.148.142.92', '180.148.142.53', '180.148.142.170', '180.148.142.171');

//get action + secret key + remote address
$action = isset($_GET['action']) ? $_GET['action'] : '';
$secretK = isset($_GET['sck']) ? $_GET['sck'] : '';
$remote_addr = $_SERVER['REMOTE_ADDR'];

//check remote address & secret key
if (1||in_array($remote_addr, $arrRemoteAddr) && ($secretK == $secret_key || $action == 'apc'))
{
    switch ($action)
    {
        case 'file':
            //fetch cache info from apc
            $apc = apc_cache_info('user');

            //check cache list
            if (!empty($apc['cache_list']))
            {
                foreach ($apc['cache_list'] as $row)
                {
                    if (preg_match('/^CACHE_PAGE_THETHAO_V3_/', $row['info']))
                    {
                        apc_delete($row['info']);
                    }
                    elseif (preg_match('/^CACHE_PAGE_WORLDCUP_/', $row['info']))
                    {
                        apc_delete($row['info']);
                    }
                }
            }

            //set array device env
            $arrDevice = array(1, 2, 4);
            
            // curl update home
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['SERVER_ADDR'] . "/");
            curl_setopt($ch, CURLOPT_GET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3');
            //each device to precache homepage
            foreach ($arrDevice as $i)
            {
                $headers = array();
                $headers[] = 'Host: nhuantp.v3.thethao.vnexpressdev.net';
                $headers[] = 'APC: 1';
                $headers[] = 'DEVICE: ' . $i;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $server_output = curl_exec($ch);
            }
            curl_close($ch);

            //echo response
            echo $_SERVER['SERVER_ADDR'] . "\n";
            break;
        case 'apc':
            apc_clear_cache();
            apc_clear_cache('user');
            echo $_SERVER['SERVER_ADDR'] . "\n";
            break;
        default:
            echo "No action!";
            break;
    }
}
else
{
    header('HTTP/1.0 404 Not Found');
    echo "File not found.";
    exit();
}
