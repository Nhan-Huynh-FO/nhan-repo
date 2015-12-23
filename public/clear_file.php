<?php

//set secret key + array remote address
$secret_key = 'e146b700f216df93a2dd5ca6e26c7c2c';
$arrRemoteAddr = array('180.148.142.92', '180.148.142.53', '180.148.142.170', '180.148.142.171', '180.148.128.197', '180.148.142.90');

//get action + secret key + remote address
$action = isset($_GET['action']) ? $_GET['action'] : '';
$secretK = isset($_GET['sck']) ? $_GET['sck'] : '';
$remote_addr = $_SERVER['REMOTE_ADDR'];

//check remote address & secret key
if (in_array($remote_addr, $arrRemoteAddr) && ($secretK == $secret_key || $action == 'apc'))
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

            // define headers
            $headers = array();
            $headers[] = 'Host: thethao.vnexpress.net';
            $headers[] = 'APC: 1';
            //set array device env
            $arrDevice = array(1, 2, 4);
            foreach ($arrDevice as $i => $device) {
                $headers[] = 'DEVICE: ' . $device;
                $arrCurl[$i] = curl_init();
                curl_setopt($arrCurl[$i], CURLOPT_HTTPHEADER, $headers);
                curl_setopt($arrCurl[$i], CURLOPT_TIMEOUT, 5);
                curl_setopt($arrCurl[$i], CURLOPT_RETURNTRANSFER, true);
                curl_setopt($arrCurl[$i], CURLOPT_URL, "http://".$_SERVER['SERVER_ADDR']."/");
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            foreach($arrCurl as $ch)
            {
                curl_multi_add_handle($mh, $ch);
            }
            //execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while($running > 0);
            // get content and remove handles to test
            /*foreach($arrCurl as $id => $c) {
                $result[$id] = curl_multi_getcontent($c);
            }*/
            foreach($arrCurl as $ch)
            {
                curl_multi_remove_handle($mh, $ch);
            }
            //close the handles
            curl_multi_close($mh);

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
