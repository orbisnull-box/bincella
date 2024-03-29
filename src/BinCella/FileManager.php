<?php

namespace BinCella;

use BinCella\FileStorage\FileStorage;
use BinCella\MetadataStorage\MetadataStorage;
use BinCella\UploadHandler\Adapter\AbstractAdapter;

class FileManager
{
    protected $fileStorage;
    protected $metadataStorage;
    protected $config;
    protected $uploadHandlers = [];

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

    /**
     * @param $handler
     * @return AbstractAdapter
     */
    public function getUploadHandler($handler)
    {
        if (!isset($this->uploadHandlers[$handler])) {
            $handler = __NAMESPACE__ . '/UploadHandler/Adapter/' . $handler;
            $objHandler = new $handler($this);
            $this->uploadHandlers[$handler] = $objHandler;
        }
        return $this->uploadHandlers[$handler];
    }

    public function save($nodeId, $filePath, $group, $user, $other = null)
    {
        $fileId = $this->getMetadataStorage()->save($nodeId, $group, $user, $other);
        $saved = $this->getFileStorage()->save($fileId, $filePath);
        if (!$saved) {
            $this->getMetadataStorage()->delete($fileId);
            return false;
        }
        return $fileId;
    }

    public function saveAll($uploadHandler, $nodeId, array $files, array $meta)
    {
        $uploader = $this->getUploadHandler($uploadHandler);
        $files = $uploader->saveFiles();
        if (!is_array($files)) {
            return true;
        }
        return $files;
    }

    public function hydrateFiles(array $filesData)
    {
        $files = [];
        foreach ($filesData as $data) {
            $file = new File($data);
            $file->setManager($this);
            $files[$file->getId()] = $file;
        }
        return $files;
    }

    /**
     * @param $nodeId
     * @return File[]
     */
    public function getNodeFiles($nodeId)
    {
        $files = $this->getMetadataStorage()->find($nodeId);
        return $this->hydrateFiles($files);
    }

    public function getUserFiles($userId)
    {
        $files = $this->getMetadataStorage()->find(null, null, $userId);
        return $this->hydrateFiles($files);
    }
}