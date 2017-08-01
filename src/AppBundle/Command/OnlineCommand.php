<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\User;

/**
 * Class OnlineCommand
 * @package AppBundle\Command
 */
class OnlineCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('battleship:who')
            ->setDescription('Display online user');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Service
        $container = $this->getContainer();
        $online = $container->get('online.manager');
        $repoGame = $container->get('doctrine.orm.entity_manager')->getRepository('MatchBundle:Game');

        // Create table
        $table = new Table($output);
        $headers = ['Date', 'Hour', 'User', 'Topic', 'Game', 'SessionID'];
        $table->setHeaders($headers);


        // Add session to the table
        $list = $online->getSessionList();
        foreach ($list as $sessionId => $info) {
            /** @var \DateTime $date */
            $date = $info['date'];
            $game = (isset($info['game_id'])) ? $repoGame->find($info['game_id']) : null;

            // Add row
            $table->addRow([
                $date->format('Y-m-d'),
                $date->format('H:i:s'),
                ($info['user'] instanceof User) ? $info['user']->getUsername() : $info['user'],
                $info['topic'],
                ($game) ? $game->getName() : '',
                $sessionId,
            ]);
        }

        // No session
        if (empty($list)) {
            $table->addRow([
                new TableCell('No session', ['colspan' => count($headers)]),
            ]);
        }

        $table->render();
    }
}
