<?php

namespace BinCella\MetadataStorage;

use BinCella\F;
use BinCella\MetadataStorage\Adapter\PdoMySql;

class MetadataStorage
{
    const TABLE = 'files_metadata';

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

    /**
     * @return PdoMySql
     * @throws \Exception
     */
    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $adapterName = $this->getConfig('adapterName');
            if (empty($adapterName)) {
                $adapterName = 'PdoMySql';
            }
            $adapterClass = F::classNameFull($adapterName, '\\BinCella\\MetadataStorage\\Adapter');
            if (is_null($adapterClass)) {
                throw new \Exception("Adapter class $adapterName not found");
            }
            $this->adapter = new $adapterClass($this->getConfig($adapterName));
        }
        return $this->adapter;
    }

    public function save($nodeType, $nodeId, $group, $user, $other = null)
    {
        if (is_array($other)) {
            $other = json_decode($other);
        }
        $data = [
            'nodeType' => $nodeType,
            'nodeId' => $nodeId,
            'group' => $group,
            'user' => $user,
        ];
        if (!is_null($other)) {
            $data['other'] = $other;
        }
        return $this->getAdapter()->insert(self::TABLE, $data);
    }

    public function delete($fileId)
    {
        $this->getAdapter()->delete(self::TABLE, $fileId);
    }

    public function find($nodeId = null, $group = null, $owner = null, $start = null, $limit = null)
    {
        return $this->getAdapter()->find(self::TABLE, $nodeId, $owner, $group);
    }
}