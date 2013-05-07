<?php

namespace BinCella\FileStorage;

use BinCella\F;

class FileStorage
{
    protected $config;

    protected $adapter;

    function __construct($config)
    {
        $this->config = $config;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig($name = null)
    {
        if (is_null($name)) {
            return $this->config;
        }
        if (!isset($this->config[$name])) {
            return [];
        }
        return $this->config[$name];
    }

    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $adapterName = $this->getConfig('adapterName');
            if (empty($adapterName)) {
                $adapterName = 'LocalDirectory';
            }
            $adapterClass = F::classNameFull($adapterName, '\\BinCella\\FileStorage\\Adapter');
            if (is_null($adapterClass)) {
                throw new \Exception("Adapter class $adapterName not found");
            }
            $this->adapter = new $adapterClass($this->getConfig($adapterName));
        }
        return $this->adapter;
    }

    public function save($nodeId, $file)
    {
        //
    }

}