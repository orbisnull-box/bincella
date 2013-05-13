<?php

namespace BinCella\FileStorage\Adapter;

abstract class LocalDirectory extends AbstractAdapter
{
    const DIR = '/tmp/fstest';

    public function checkDir($dir)
    {
        $result = true;
        if (!file_exists($dir)) {
            $result = mkdir($dir, 0770, true);
        }
        $result = $result && is_writable($dir);
        return $result;
    }

    public function save($fileId, $filePath)
    {
        $outputDir =self::DIR;
        if (!$this->checkDir($outputDir)) {
            throw new \Exception("Directory $outputDir not exist, or not writable");
        }
        return rename($filePath, $fileId);
    }
}