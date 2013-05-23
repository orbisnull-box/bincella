<?php

namespace BinCella\UploadHandler\Adapter;

abstract class AbstractAdapter
{
    protected $fileManager;

    function __construct($fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @return mixed
     */
    public function getFileManager()
    {
        return $this->fileManager;
    }

    /**
     * адаптер сам обращается к нужным данным
     * @return bool|array
     */
    abstract public function saveFiles();

}