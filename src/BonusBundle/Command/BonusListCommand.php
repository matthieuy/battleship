<?php

namespace BonusBundle\Command;

use MatchBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BonusListCommand
 * @package BonusBundle\Command
 */
class BonusListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('battleship:bonus')
            ->setDescription('Display list of bonus');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get services
        $container = $this->getContainer();
        $bonusManager = $container->get('bonus.registry');
        $translator = $container->get('translator');

        // Create a IA player
        $player = new Player();
        $player->setAi(true);

        // Create table
        $table = new Table($output);
        $table->setHeaders(['Name', 'Probability', 'Description', 'AI', 'Class']);

        // Add bonus to table
        $list = $bonusManager->getAllBonus();
        foreach ($list as $id => $bonus) {
            $row = [
                $translator->trans($bonus->getName()),
                $bonus->getProbabilityToCatch().'%',
                $translator->trans($bonus->getDescription()),
                $bonus->canWeGetIt($player) ? 'X' : '',
                get_class($bonus),
            ];

            $table->addRow($row);
        }

        $table->render();
    }
}
