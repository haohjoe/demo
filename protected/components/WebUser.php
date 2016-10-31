<?php

class WebUser extends CWebUser
{

    private $_info = array();

    public function setState($key, $value, $defaultValue = null)
    {
        $key = $this->getStateKeyPrefix() . $key;
        if ($value === $defaultValue) {
            unset($this->_info[$key]); 
        } else {
            $this->_info[$key] = $value; 
        }
    }

    public function getState($key, $defaultValue = null)
    {
        $key = $this->getStateKeyPrefix() . $key;
        return isset($this->_info[$key]) ? $this->_info[$key] : $defaultValue;
    }
}
