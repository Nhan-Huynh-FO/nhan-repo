<?php

/**
 * @todo router for VnE GiaiTri
 * @return Thethao_Plugin_Router_DetailM240
 * @author LamTX
 */
class Thethao_Plugin_Router_Extend extends Zend_Controller_Router_Route_Regex
{

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     * @param  string $path Path used to match against this routing map
     * @param boolean $partial
     * @return array|false  An array of assigned values or a false on a mismatch
     * @author LamTX
     */
    public function match($path, $partial = false)
    {

        $return = parent::match($path, $partial);

        switch ($return['cate_code'])
        {
            case 'photo':
                $return['controller'] = 'photo';
                $return['action'] = 'index';
                break;
            case 'video':
                $return['controller'] = 'video';
                $return['action'] = 'index';
                break;
            case '32-doi':
                $return['controller'] = 'team';
                $return['action'] = 'index';
                break;
            case 'du-doan':
                $return['controller'] = 'team';
                $return['action'] = 'dudoan';
                break;
            default :
                break;
        }
        return $return;
    }

}

?>