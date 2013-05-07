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
        $sql = "insert into $table ($fields) values ($aliases)";
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
}