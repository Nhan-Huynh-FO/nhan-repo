<?php

/**
 * @todo router for VnE GiaiTri
 * @return Thethao_Plugin_Router_Cate
 * @author LamTX
 */
class Thethao_Plugin_Router_Detail extends Zend_Controller_Router_Route_Regex
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
        if ($return)
        {
            if ($return['action'] == 'video')
            {
                $return['controller'] = 'video';
                $return['action'] = 'index';
            }
            elseif(($return['action'] == 'tin-tuc'))
            {

                $return['action'] = 'index';
            }
        }
        return $return;
    }

}

?>