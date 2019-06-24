<?php

namespace core;

use System\Config;

/**
 * Class Db
 * @package core
 */
class Db extends \PDO
{
    public function __construct($dsn, $user = '', $password = '')
    {
        try {
            parent::__construct($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            ]);
        } catch (\PDOException $pdo) {
            exit($pdo->getMessage());
        }
    }
}
