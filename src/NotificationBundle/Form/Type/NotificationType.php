<?php

namespace NotificationBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Registry\TransporterRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NotificationType
 * @package NotificationBundle\Form\Type
 */
class NotificationType extends AbstractType
{
    private $entityManager;
    private $registry;

    /**
     * NotificationType constructor.
     * @param EntityManager       $entityManager
     * @param TransporterRegistry $registry
     */
    public function __construct(EntityManager $entityManager, TransporterRegistry $registry)
    {
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    /**
     * Build form
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Get variable from controller
        $game = $options['data']['_game'];
        $user = $options['data']['_user'];
        unset($options['data']['_game'], $options['data']['_user']);

        // Get notifications
        $repo = $this->entityManager->getRepository('NotificationBundle:Notification');
        $notifications = $repo->getUserNotification($game, $user);

        // Get transporters
        $transporters = $this->registry->getAll();

        // Foreach transporters : add the form fieldset
        foreach ($transporters as $transporterName => $transporter) {
            $entity = (key_exists($transporterName, $notifications)) ? $notifications[$transporterName] : new Notification($transporterName, $game, $user);
            $fields = $transporter->getFormFields($builder, $entity, $options);
            $this->entityManager->persist($entity);

            // Add fields to a fieldset
            $builder->add($transporterName, FieldsetType::class, [
                'legend' => $transporterName,
                'fields' => $fields,
                'translation_domain' => 'notifications',
            ]);
        }
    }

    /**
     * Configure default options
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * Get prefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'notification';
    }
}
