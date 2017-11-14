<?php

namespace BonusBundle\Command;

use BonusBundle\BonusEvents;
use BonusBundle\Entity\Inventory;
use BonusBundle\Event\BonusEvent;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class BonusAddCommand
 *
 * @package BonusBundle\Command
 */
class BonusAddCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('battleship:bonus:add')
            ->setDescription('Add bonus to a player')
            ->addOption('slug', null, InputOption::VALUE_OPTIONAL, "The game slug")
            ->addOption('player', null, InputOption::VALUE_OPTIONAL, "The player's name")
            ->addArgument('bonus', InputArgument::OPTIONAL, "The bonus ID");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        // Get game
        $game = null;
        $repoGame = $em->getRepository('MatchBundle:Game');
        $slug = $input->getOption('slug');
        if ($slug === null) {
            $games = $repoGame->findBy(['status' => Game::STATUS_RUN]);
            $nbGame = count($games);
            if ($nbGame == 0) {
                $io->error("No running game");

                return false;
            } elseif ($nbGame == 1) {
                $game = $games[0];
                $io->note('Select the only one game : "'.$game->getName().'"');
            } else {
                $slug = $io->choice('Game', $games);
            }
        }
        if ($game === null) {
            $game = $repoGame->findOneBy(['slug' => $slug]);
        }

        if (!$game instanceof Game) {
            $io->error("Game with this slug not found !");

            return false;
        }

        // Get player
        $repoPlayer = $em->getRepository('MatchBundle:Player');
        $playerName = $input->getOption('player');
        if ($playerName === null) {
            $players = $repoPlayer->findBy(['game' => $game], ['position' => 'ASC']);
            $playerName = $io->choice('Player', $players);
        }
        $player = $repoPlayer->findOneBy(['name' => $playerName]);
        if (!$player instanceof Player) {
            $io->error('Player not found !');

            return false;
        }

        // Inventory full
        if ($player->getNbBonus() >= $player->getInventorySize()) {
            $io->error("Inventory of $playerName is full");

            return false;
        }


        // Get bonus
        try {
            $bonusName = $input->getArgument('bonus');
            if ($bonusName === null) {
                $bonusList = array_keys($container->get('bonus.registry')->getAllBonus());
                $bonusName = $io->choice('Bonus', $bonusList);
            }

            $bonus = $container->get('bonus.'.$bonusName);
        } catch (ServiceNotFoundException $e) {
            $io->error('Bonus not found');

            return false;
        }

        // Create inventory
        $inventory = new Inventory();
        $inventory
            ->setName($bonus->getId())
            ->setOptions($bonus->getOptions($player))
            ->setPlayer($player);
        $em->persist($inventory);
        $em->flush();

        // Dispatch
        $event = new BonusEvent($game, $player);
        $event
            ->setBonus($bonus)
            ->setInventory($inventory);
        $container->get('event.manager')->dispatch(BonusEvents::CATCH_ONE, $event);
        $io->text(sprintf('Add the bonus "%s" to "%s" into game "%s"', $bonusName, $playerName, $game->getName()));
    }
}
