<?php

namespace MatchBundle\Form\Type;

use MatchBundle\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class GameType
 * @package MatchBundle\Form\Type
 */
class GameType extends AbstractType
{
    protected $user;

    /**
     * GameType constructor (DI)
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $token = $tokenStorage->getToken();
        if ($token && null !== $user = $token->getUser()) {
            $this->user = $user;
        }
    }

    /**
     * Build form
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Create form
        $builder
            ->add('name', FormType\TextType::class, [
                'label' => 'name',
                'attr' => [
                    'placeholder' => 'Name of the game',
                    'maxlength' => 128,
                ],
            ])
            ->add('maxPlayer', FormType\HiddenType::class, [
                'attr' => [
                    'class' => 'hidden-maxplayer',
                ],
            ]);

        // Closure to set default values on post submit
        $user = $this->user;
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($user) {
            /** @var Game $game */
            $game = $event->getData();
            if ($user) {
                $game->setCreator($user);
            }

            $game->setOptions([
                'penalty' => 20,
                'weapon' => true,
                'bonus' => true,
            ]);
        });
    }

    /**
     * Configure
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'MatchBundle\Entity\Game',
            'translation_domain' => 'form',
        ]);
    }
}
