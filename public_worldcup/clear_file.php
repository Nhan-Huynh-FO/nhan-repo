<?php

$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action)
{
    case 'file':
        $apc = apc_cache_info('user');
        if (!empty($apc['cache_list']))
        {
            foreach ($apc['cache_list'] as $row)
            {
                if (preg_match('/^CACHE_PAGE_WORLDCUP/', $row['info']))
                {
                    apc_delete($row['info']);
                }
            }
        }
        echo $_SERVER['SERVER_ADDR'] . "\n";
        break;
    case 'apc':
        apc_clear_cache();
        apc_clear_cache('user');
        echo $_SERVER['SERVER_ADDR'] . "\n";
        break;
    default:
        break;
}
?>