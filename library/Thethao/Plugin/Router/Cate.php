<?php

/**
 * @todo router for VnE GiaiTri
 * @return Thethao_Plugin_Router_Cate
 * @author LamTX
 */
class Thethao_Plugin_Router_Cate extends Zend_Controller_Router_Route_Regex
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
        if (!$partial)
        {
            $path = trim(urldecode($path), '/');
            $regex = '#^' . $this->_regex . '$#i';
        }
        else
        {
            $regex = '#^' . $this->_regex . '#i';
        }

        $res = preg_match($regex, $path, $values);

        if ($res === 0)
        {
            return false;
        }

        if ($partial)
        {
            $this->setMatchedPath($values[0]);
        }

        // array_filter_key()? Why isn't this in a standard PHP function set yet? :)
        foreach ($values as $i => $value)
        {
            if (!is_int($i) || $i === 0)
            {
                unset($values[$i]);
            }
        }

        $this->_values = $values;
        $values = $this->_getMappedValues($values);
        $defaults = $this->_defaults;
        $return = $values + $defaults;
        return $return;
    }

    /**
     * Maps numerically indexed array values to it's associative mapped counterpart.
     * Or vice versa. Uses user provided map array which consists of index => name
     * parameter mapping. If map is not found, it returns original array.
     *
     * Method strips destination type of keys form source array. Ie. if source array is
     * indexed numerically then every associative key will be stripped. Vice versa if reversed
     * is set to true.
     *
     * @param  array   $values Indexed or associative array of values to map
     * @param  boolean $reversed False means translation of index to association. True means reverse.
     * @param  boolean $preserve Should wrong type of keys be preserved or stripped.
     * @return array   An array of mapped values
     * @author LamTX
     */
    protected function _getMappedValues($values, $reversed = false, $preserve = false)
    {
        if (count($this->_map) == 0)
        {
            return $values;
        }

        $return = array();

        foreach ($values as $key => $value)
        {
            if (is_int($key))
            {
                if (array_key_exists($key, $this->_map))
                {
                    $index = $this->_map[$key];
                    if ($values[$key] != '')
                    {
                        $return[$index] = $values[$key];
                    }
                }
            }
        }
        return $return;
    }

}

?>