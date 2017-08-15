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
        'Edward Teach',
        'Roberto Cofresi',
        'Peter Easton',
        'Olivier Levasseur',
        'Mary Read',
        'John Roberts',
        'Klaus Stortebeker',
        'John Silver',
        'Edward England',
        'John Halsey',
        'Jean Lafitte',
        'Arudj Reis',
    ];

    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->aiList as $name) {
            $ai = new User();
            $ai
                ->setAi(true)
                ->setUsername($name)
                ->setEmail(str_replace(' ', '', $name).'@ai')
                ->setEnabled(false)
                ->setPassword($name);

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
