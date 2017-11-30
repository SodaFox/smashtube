<?php
/**
 * Created by PhpStorm.
 * User: Maxi
 * Date: 27.11.2017
 * Time: 15:07
 */

namespace AppBundle\Common;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Statement;
use Symfony\Component\Config\Definition\Exception\Exception;
use function Symfony\Component\Debug\Tests\testHeader;

final class QueryBuilder
{
    protected static $_instance = null;
    private $configuration;
    private $connectionParameters =  array(
        'dbname' => 'smashtube',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    );
    private $connection;
    private $statement;
    private $sqlCommand;
    private $sqlParameter;

    public function getConfig() : Configuration
    {
        if($this->configuration === null)
        {
            $this->configuration = new Configuration();
        }
        return $this->configuration;
    }
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }
    public function getConnection() : Connection
    {
        if($this->connection === null)
        {
            $this->connection = DriverManager::getConnection($this->getConnectionParameters(),$this->getConfig());
        }
        return $this->getConnection();
    }
    public function getStatement() : Statement
    {
        if($this->statement === null)
        {
            $this->statement = $this->getConnection()->prepare();
        }
        return $this->statement;
    }

    public function setSQL($Command)
    {
        if(empty($Command))
        {
            throw new Exception("Sql Command is null or empty");
        }

        $this->sqlCommand = $Command;
    }
    public function setParameter($name,$value)
    {
        $this->getStatement()->bindValue($name,$value);
    }

    public function execute()
    {
        $this->statement = $this->getStatement()->execute();
    }

    public function executeQuery($sql)
    {
        if($sql)
        {
            $this->setSQL($sql);
        }

        $this->statement = $this->getConnection()->executeQuery($this->sqlCommand,$this->sqlParameter);
    }
    public function fetchAll() : object
    {
        $this->getStatement()->fetchAll();
    }
    public function executeSelect($command, $parameters = array()) : object
    {
        $this->setSQL($command);
        $this->execute();
        return $this->fetchAll();
    }

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }
    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }
    public static function getInstance() : QueryBuilder
    {
        if (null === self::$_instance)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}