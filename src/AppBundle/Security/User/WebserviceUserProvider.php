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
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
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
            'driver' => 'mysqli'
        );


        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $userData = $conn->fetchArray("select password,salt,roles,id from user where username = ?", array($username));

        if ($userData)
        {
            $password = $userData[0];
            $salt = $userData[1];
            $roles = $userData[2];
            $id = $userData[3];

            if($salt === null)
            {
                $salt = "";
            }
            //TODO ADD ROLES
            if($roles === null)
            {
                $roles = array("ROLE_USER","ROLE_ADMIN");
            }

            return new WebserviceUser($id,$username, $password, $salt, $roles);
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