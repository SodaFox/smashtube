<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSqlCommand extends Command
{
    private $pathToSql = "";
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('sql:create')

            // the short description shown while running "php bin/console list"
            ->setDescription('Creates new Database smashtube database');

            // the full command description shown when running the command with
            // the "--help" option
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO ADD QUERY BUILDER

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => 'smashtube',
              'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $sql = "";


    }
}