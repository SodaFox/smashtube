<?php
/**
 * Created by PhpStorm.
 * User: Maxi
 * Date: 27.11.2017
 * Time: 14:15
 */
// src/AppBundle/Security/User/WebserviceUserProvider.php
namespace AppBundle\Security\User;

use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\DBAL\DriverManager;


class WebserviceUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => 'smashtube',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $sql = "SELECT * FROM user u where u.username = :name";
        $query= $conn->prepare($sql);
        $query->bindValue("name", $username);
        $query->execute();

        $userData = $query->fetch();

        if ($userData)
        {
            $password = $userData["password"];
            $salt = $userData["salt"];
            if($salt === null)
            {
                $salt = "";
            }
            //TODO ADD ROLES
            $roles = array("ROLE_USER","ROLE_ADMIN");

            return new WebserviceUser($username, $password, $salt, $roles);
        }

        throw new UsernameNotFoundException
        (
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return WebserviceUser::class === $class;
    }
}