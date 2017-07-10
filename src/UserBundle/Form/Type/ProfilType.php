<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    }

    /**
     * Configure
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
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
