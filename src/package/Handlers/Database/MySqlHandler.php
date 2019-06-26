<?php

namespace Vkovic\LaravelCommandos\Handlers\Database;

use PDO;
use Vkovic\LaravelCommandos\Handlers\Database\Exceptions\DbCreateException;
use Vkovic\LaravelCommandos\Handlers\Database\Exceptions\DbDropException;
use Vkovic\LaravelCommandos\Handlers\Database\Exceptions\DbExistCheckException;

class MySqlHandler extends AbstractDbHandler
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Raw database connection configuration.
     * See config/database.php for more info
     *
     * @var array
     */
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;

        // TODO
        // User should be able to add more options to connection
        $this->pdo = $this->getPdo($config['host'], $config['port'], $config['username'], $config['password']);
    }

    public function databaseExists($database): bool
    {
        try {
            $stmt = $this->pdo->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '$database'");
        } catch (\Exception $e) {
            throw new DbExistCheckException($e->getMessage());
        }

        return $stmt->fetch() !== false;
    }

    public function createDatabase($database): void
    {
        try {
            $stmt = $this->pdo->exec("CREATE DATABASE `$database`");
        } catch (\Exception $e) {
            throw new DbCreateException($e->getMessage());
        }
    }

    public function dropDatabase($database): void
    {
        try {
            $stmt = $this->pdo->exec("DROP DATABASE `$database`");
        } catch (\Exception $e) {
            throw new DbDropException($e->getMessage());
        }
    }

    public function getColumns($database, $table): array
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM `$database`.`$table`");

        $data = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $i => $field) {
            // `name`, `position`, `type`, `nullable`, `default_value
            $data[] = [
                'name' => $field['Field'],
                'position' => $i,
                'type' => $field['Type'],
                'nullable' => $field['Null'] == 'YES',
                'default_value' => $field['Default']
            ];
        }

        return $data;
    }

    /**
     * Get PDO connection in case we want to perform custom queries
     *
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @param $database
     *
     * @return PDO
     */
    public function getPdo($host, $port, $username, $password): PDO
    {
        if ($this->pdo === null) {
            $pdo = new PDO(sprintf('mysql:host=%s;port=%d;', $host, $port), $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->pdo = $pdo;
        }

        return $this->pdo;
    }

//    public function getPdo($host, $port, $username, $password, $database = null): PDO
//    {
//        if ($this->pdo === null) {
//            $dsn = "mysql:host=$host;port=$port;" . $database ?: "dbname=$database;" . '';
//
//            dump($dsn);
//
//            $pdo = new PDO($dsn, $username, $password);
//            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//            $this->pdo = $pdo;
//        }
//
//        return $this->pdo;
//    }
}