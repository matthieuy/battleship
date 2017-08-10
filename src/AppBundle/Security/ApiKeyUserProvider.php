<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiKeyUserProvider
 * @package AppBundle\Security
 */
class ApiKeyUserProvider implements UserProviderInterface
{
    private $token;
    protected $user;

    /**
     * ApiKeyUserProvider constructor.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->user = new User('api', null, ['ROLE_API']);
        $this->token = $token;
    }

    /**
     * Get username from apiKey
     * @param string $apiKey
     *
     * @return bool|string
     */
    public function getUsernameForApiKey($apiKey)
    {
        if ($apiKey == $this->token && $this->token !== 'ThisTokenIsNotSoSecretChangeIt') {
            return $this->user->getUsername();
        }

        return false;
    }

    /**
     * Loads the user for the given username.
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        if ($username !== $this->user->getUsername()) {
            throw new UsernameNotFoundException();
        }

        return $this->user;
    }

    /**
     * Refreshes the user.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return new User($this->user->getUsername(), $this->user->getPassword(), $this->user->getRoles());
    }

    /**
     * Whether this provider supports the given user class.
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
