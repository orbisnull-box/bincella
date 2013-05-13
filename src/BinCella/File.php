<?php

namespace BinCella;

class File
{
    protected $id;

    protected $path;

    protected $nodeId;

    protected $group;

    protected $owner;

    protected $type;

    protected $size;

    /**
     * @var FileManager
     */
    protected $manager;

    function __construct(array $params)
    {
        $this->setParams($params);
    }

    public function setParams(array $params)
    {
        foreach ($params as $key=>$value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    public function getNodeId()
    {
        return $this->nodeId;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function __toString()
    {
        return 'uri : ' . $this->getPath();
    }

}