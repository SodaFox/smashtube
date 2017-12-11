<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataSqlCommand extends ContainerAwareCommand
{
    private $dbName = "smashtube";

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('sql:data')

            // the short description shown while running "php bin/console list"
            ->setDescription('Add dummy data');

            // the full command description shown when running the command with
            // the "--help" option
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getContainer()->get('kernel')->getRootDir();

        $path = $path . "\sql\data.sql";

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

        $mysqliCon = mysqli_connect(
            $conn->getHost(),
            $conn->getUsername(),
            $conn->getPassword()
        );
        try
        {
            mysqli_multi_query($mysqliCon,$sql);
        }
        catch(Exception $exception)
        {
            $output->writeln("Error occured");
        }
    }
}