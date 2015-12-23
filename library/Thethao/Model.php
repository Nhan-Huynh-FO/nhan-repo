<?php
/**
 * @todo define giaitri model adapter
 * @param <string> $module
 * @param <array> $adapter
 * @return Thethao_Model
 * @author LamTX
 */
class Thethao_Model
{
    protected function factory($module, $adapter)
    {
        try
        {
            $adapterName = 'Thethao_Business_' . ucfirst($module) . '_Adapter_' . ucfirst(strtolower($adapter));
            return new $adapterName();
        }
        catch(Exception $ex)
        {
            return new Thethao_Stdclass();
        }
    }
}
?>
