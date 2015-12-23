<?php
class Application
{
    protected $_options = array();
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }
    public function getOption($key)
    {
        return $this->_options["$key"];
    }
    public function getOptions()
    {
        return $this->_options;
    }
}