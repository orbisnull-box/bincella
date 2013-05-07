<?php

namespace BinCella;

use BinCella\FileStorage\FileStorage;
use BinCella\MetadataStorage\MetadataStorage;

class FileManager
{
    protected $fileStorage;
    protected $metadataStorage;
    protected $config;

    function __construct(array $config = null)
    {
        if (is_array($config)) {
            $this->setConfig($config);
        }
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig($name = null)
    {
        if (is_null($this->config)) {
            $this->config = (array) (new Module())->getConfig();
        }
        if (is_null($name)) {
            return $this->config;
        }
        if (!isset($this->config[$name])) {
            return [];
        }
        return $this->config[$name];
    }

    public function getFileStorage()
    {
        if (is_null($this->fileStorage)) {
            $this->fileStorage = new FileStorage($this->getConfig('FileStorage'));
        }
        return $this->fileStorage;
    }

    public function getMetadataStorage()
    {
        if (is_null($this->metadataStorage)) {
            $this->metadataStorage = new MetadataStorage($this->getConfig('MetadataStorage'));
        }
        return $this->metadataStorage;
    }

    public function getFileType($filePath)
    {
        
    }

    public function save($nodeId, $filePath, $group, $user, $other = null)
    {
        $fileId = $this->getMetadataStorage()->save($nodeId, $group, $user, $other);
        $result = $this->getFileStorage()->save($fileId, $filePath);
    }

    public function saveAll($nodeId, array $files, array $meta)
    {

    }

    /**
     * @param $nodeId
     * @return File[]
     */
    public function getFiles($nodeId)
    {

    }
}