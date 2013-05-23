<?php

use BinCella\UploadHandler\Adapter\AbstractAdapter;

class HtmlFile extends AbstractAdapter
{
    /**
     * адаптер сам обращается к нужным данным
     * @return bool|array
     */
    public function saveFiles()
    {
        if (empty($_FILES)) {
            return false;
        }
    }

}