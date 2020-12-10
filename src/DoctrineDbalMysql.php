<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DoctrineDbalMysql extends Mysql
{
    private $connection;

    public function __construct()
    {
        $config = new Configuration();

        $connectionParams = array(
            'dbname' => 'playground',
            'user' => 'root',
            'password' => 'root',
            'host' => 'mysql',
            'driver' => 'pdo_mysql',
        );

        $this->connection = DriverManager::getConnection($connectionParams, $config);

        parent::__construct();
    }

    public function selectOne(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('m.*')
            ->from('messages', 'm')
            ->where("m.id = ?");

        return $this->connection->fetchAssoc($queryBuilder, [1]);
    }
}
