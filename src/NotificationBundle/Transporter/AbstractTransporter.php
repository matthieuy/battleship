<?php

namespace NotificationBundle\Transporter;

use NotificationBundle\Entity\Notification;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AbstractTransporter
 * @package NotificationBundle\Transporter
 */
abstract class AbstractTransporter implements TransporterInterface
{
    /**
     * Get form fields (on setting page)
     * @param FormBuilderInterface $builder
     * @param Notification         $notification
     * @param array                $options
     *
     * @return FormBuilderInterface[]
     */
    public function getFormFields(FormBuilderInterface $builder, Notification &$notification, array $options)
    {
        $fields = [];
        $fields[] = $builder->create('enabled', FormType\CheckboxType::class, [
            'label' => 'enabled',
            'required' => false,
            'data' => $notification->isEnabled(),
        ]);

        // Save
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($notification) {
            $data = $event->getData();

            // Enable / Disable
            $enabled = (isset($data[$notification->getName()]['enabled'])) ? $data[$notification->getName()]['enabled'] : false;
            $notification->setEnabled($enabled);

            // Configuration
            $config = $data[$notification->getName()];
            foreach ($config as $key => $value) {
                if (empty($value)) {
                    $notification->removeConfiguration($key);
                } elseif ($key !== 'enabled') {
                    $notification->addConfiguration($key, $value);
                }
            }
        });

        return $fields;
    }
}
