<?php

namespace NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FieldsetType
 * @package NotificationBundle\Form\Type
 */
class FieldsetType extends AbstractType
{
    /**
     * Build fieldset
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $field) {
            /** @var FormBuilderInterface $field */
            $builder->add($field->getName(), get_class($field->getType()->getInnerType()), $field->getOptions());
        }
    }

    /**
     * Set default options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'legend' => '',
            'fields' => [],
        ]);
    }

    /**
     * Build view
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['legend'] = $options['legend'];
    }

    /**
     * Get block prefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'fieldset_notif';
    }
}
