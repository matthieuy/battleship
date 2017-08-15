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
        $container->get('event.manager');
        $bonusManager = $container->get('bonus.registry');
        $translator = $container->get('translator');

        // Create a IA player
        $player = new Player();
        $player->setAi(true);

        // Create table
        $table = new Table($output);
        $table->setHeaders(['Name', 'Probability', 'Description', 'AI', 'Class', 'ID']);

        // Add bonus to table
        $list = $bonusManager->getAllBonus();
        $rows = [];
        $sort = [];
        foreach ($list as $id => $bonus) {
            $rows[] = [
                $translator->trans($bonus->getName()),
                $bonus->getProbabilityToCatch().'%',
                $translator->trans($bonus->getDescription()),
                $bonus->canWeGetIt($player) ? 'X' : '',
                get_class($bonus),
                $bonus->getId(),
            ];
            $sort[] = $bonus->getProbabilityToCatch();
        }
        array_multisort($sort, SORT_DESC, $rows);

        $table->addRows($rows);
        $table->render();
    }
}
