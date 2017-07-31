<?php

namespace UserBundle\Form\Type;

use JMS\TranslationBundle\Annotation as i18n;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class ProfilType
 * @package UserBundle\Form\Type
 */
class ProfilType extends AbstractType
{
    private $rootPath;

    /**
     * ProfilType constructor (DI)
     * @param string $rootPath The root path
     */
    public function __construct($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @i18n\Ignore */
        $choices = [
            '20px' => 20,
            '30px' => 30,
            '40px' => 40,
            '50px' => 50,
            '60px' => 60,
        ];

        $builder
            ->add('avatar', Type\FileType::class)
            ->add('displayGrid', Type\CheckboxType::class)
            ->add('boxSize', Type\ChoiceType::class, [
                'choices' => $choices,
            ]);

        $rootPath = $this->rootPath;
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($rootPath) {
            /** @var User $user */
            $user = $event->getData();
            if (null !== $uploadFile = $user->getAvatar()) {
                $uploadDir = realpath($rootPath.'/var/avatars');
                array_map('unlink', glob($uploadDir.'/'.$user->getId().'-*.png'));
                $uploadFile->move($uploadDir, $user->getId());
            }
        });
    }

    /**
     * Configure
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'translation_domain' => 'form',
        ]);
    }

    /**
     * Get prefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'profil';
    }
}
