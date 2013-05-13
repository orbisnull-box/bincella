<?php

namespace BinCella\MetadataStorage\Adapter;

use BinCella\F;

class PdoMySql extends AbstractAdapter
{
    protected $pdo;
    protected $initConfig = [];
    protected $config;

    public function __construct($params = null)
    {
        if ($params instanceof \PDO_MYSQL) {
            $this->pdo = $params;
        } else if (is_array($params)) {
            $this->initConfig = $params;
        }
    }

    public function tableExist($table)
    {
        static $sth;
        if (is_null($sth)) {
            $pdo = $this->getPdo();
            $sql = "select table_name from information_schema.tables where table_schema = :table";
            $sth = $pdo->prepare($sql);
        }
        /** @var \PDOStatement $sth */
        $sth->bindValue(':table', $table);
        return (bool) $sth->fetchColumn();
    }

    public function createTable($tableName)
    {
        $pdo = $this->getPdo();
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `nodeId` varchar(255) NOT NULL,
          `path` varchar(255) NOT NULL,
          `group` varchar(10) NOT NULL,
          `owner` varchar(255) NOT NULL,
          `type` varchar(10) NOT NULL,
          `size` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `node` (`nodeId`,`group`,`type`),
          KEY `owner` (`owner`,`group`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        return $pdo->query($sql);
    }

    public function getConfig()
    {
        if (is_null($this->config)) {
            $defaultConfig = [
                'dsn' => null,
                'username' => null,
                'password' => null,
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                ],
            ];
            if (is_array($this->config['dsn'])) {
                $defaultDsnArray = [
                    'dbname' => 'testdb',
                    'host' => '127.0.0.1',
                    'port' => 3333,
                ];
                $dsn = $this->config['dsn'];
                $dsn = F::arrayMergeRecursive($defaultDsnArray, $dsn);
                $dsn = 'mysql:dbname=' . $dsn['dbname'] . ';host=' . $dsn['host'] . ';port=' . $dsn['port'];
                $this->config['dsn'] = $dsn;
            }
            $this->config = F::arrayMergeRecursive($defaultConfig, $this->config);
        }
        return $this->config;
    }

    /**
     * @return \PDO|\PDO_MYSQL
     */
    public function getPdo()
    {
        if (is_null($this->pdo)) {
            $config = $this->getConfig();
            $this->pdo = new \PDO($config['dsn'], $config['username'], $config['password'], $config['driver_options']);
        }
        return $this->pdo;
    }

    public function prepareData($data)
    {
        $fields = [];
        $aliases = [];
        $values = [];
        foreach ($data as $key=>$value) {
            $fields[] = $key;
            $alias = ':' . $key;
            $aliases[] = $alias;
            $values[$alias] = $value;
        }
        return [
            'fields' => $fields,
            'aliases' => $aliases,
            'values' => $values,
        ];
    }


    public function insert($table, $fieldsData)
    {
        $pdo = $this->getPdo();
        $fieldsData = $this->prepareData($fieldsData);
        $fields = implode(', ', $fieldsData['fields']);
        $aliases = implode(', ', $fieldsData['aliases']);

            /** @var \PDOStatement $sth */
        $sql = "insert into {$table} ({$fields}) values ({$aliases})";
        $sth = $pdo->prepare($sql);
        foreach ($fieldsData['values'] as $key => $value) {
            $sth->bindValue($key, $value);
        }

        $result = $pdo->exec($sth);
        if (!$result) {
            throw new \Exception('Insert not success execute. Error: #' . $pdo->errorCode() . ' (' . $pdo->errorInfo() . ')');
        }
        return $pdo->lastInsertId();
    }

    public function delete($table, $rowId)
    {
        $pdo = $this->getPdo();
        $sql = "delete from {$table} where id = :rowId limit 1";
        $sth = $pdo->prepare($sql);
        $sth->bindValue(':rowId', $rowId);
        $result = $pdo->exec($sth);
        if (!$result) {
            throw new \Exception('Insert not success execute. Error: #' . $pdo->errorCode() . ' (' . $pdo->errorInfo() . ')');
        }
        return $result;
    }

    public function find($table, $nodeId = null, $owner = null, $group = null)
    {
        $pdo = $this->getPdo();
        $whereFields = ['nodeId' => $nodeId, 'owner' => $owner, $group => $group];
        $where = '';
        foreach($whereFields as $key=>$value) {
            if (!is_null($value)) {
                $where .= " $key = :{$key}";
            } else {
                unset($whereFields[$key]);
            }
        }
        if (!empty($where)) {
            $where =' where ' . $where;
        }
        $sql = "select from {$table} {$where}";
        /** @var \PDOStatement $sth */
        $sth = $pdo->prepare($sql);
        foreach ($whereFields as $key=>$value) {
            $sth->bindValue(":{$key}", $value);
        }
        $result = $sth->fetchAll();
        return $result;
    }
}