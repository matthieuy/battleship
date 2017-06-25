<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

/**
 * Class AiFixtures
 * @package UserBundle\DataFixtures\ORM
 */
class AiFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $aiList = [
        ['name' => 'Alan Turing', 'email' => 'a.turing'],
        ['name' => 'Tim Berners-Lee', 'email' => 'www'],
        ['name' => 'Nolan Bushnell', 'email' => 'pong'],
        ['name' => 'Alan Cox', 'email' => 'cox'],
        ['name' => 'François Gernelle', 'email' => 'f.gernelle'],
        ['name' => 'Rasmus Lerdorf', 'email' => 'php'],
        ['name' => 'Ian Murdock', 'email' => 'debian'],
        ['name' => 'Werner Koch', 'email' => 'gpg'],
        ['name' => 'Shigeru Miyamoto', 'email' => 'nintendo'],
        ['name' => 'Vint Cerf', 'email' => 'tcp'],
        ['name' => 'John McCarthy', 'email' => 'ai'],
        ['name' => 'Ken Thompson', 'email' => 'unix'],
        ['name' => 'Linus Torvald', 'email' => 'linux'],
        ['name' => 'Ronald Rivest', 'email' => 'rsa'],
        ['name' => 'Martin Hellman', 'email' => 'hellman'],
        ['name' => 'Abraham Lempel', 'email' => 'lz'],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->aiList as $data) {
            $ai = new User();
            $ai
                ->setAi(true)
                ->setUsername($data['name'])
                ->setEmail($data['email'].'@ai')
                ->setLocked(true)
                ->setPassword($data['name']);

            $manager->persist($ai);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
