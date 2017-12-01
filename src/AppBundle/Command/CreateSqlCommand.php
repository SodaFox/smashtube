<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSqlCommand extends ContainerAwareCommand
{
    private $dbName = "smashtube";

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
        $path = $this->getContainer()->get('kernel')->getRootDir();

        $path = $path . "\Resources\sql\smashtube.sql";

        $output->writeln("Getting Sql form: " . $path);

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => 'smashtube',
              'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'mysqli',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $sql = file_get_contents($path);

        try
        {
            mysqli_multi_query($sql);
        }
        catch(Exception $exception)
        {
            $output->writeln("Error occured");
        }
    }
}