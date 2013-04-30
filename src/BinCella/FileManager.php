<?php

namespace BinCella;

use BinCella\FileStorage\FileStorage;
use BinCella\MetadataStorage\MetadataStorage;

class FileManager
{
    protected $fileStorage;
    protected $metadataStorage;

    public function getFileStorage()
    {
        if (is_null($this->fileStorage)) {
            $this->fileStorage = new FileStorage();
        }
        return $this->fileStorage;
    }

    public function getMetadataStorage()
    {
        if (is_null($this->metadataStorage)) {
            $this->metadataStorage = new MetadataStorage();
        }
        return $this->metadataStorage;
    }

    public function save($nodeId, $file)
    {

    }

    public function saveAll($nodeId, array $files)
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