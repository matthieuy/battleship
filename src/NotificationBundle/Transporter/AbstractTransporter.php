<?php

namespace NotificationBundle\Transporter;

use NotificationBundle\Entity\Notification;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

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

    /**
     * Set default value or load the configuration value
     * @param FormBuilderInterface $builder
     * @param Notification         $notification
     * @param string               $key
     * @param string               $default
     */
    protected function setDefaultValue(FormBuilderInterface $builder, Notification $notification, $key, $default = '')
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($notification, $key, $default) {
            $data = $event->getData();
            if (!isset($data[$notification->getName()][$key])) {
                $data[$notification->getName()][$key] = $notification->getConfigurationValue($key, $default);
                $event->setData($data);
            }
        });
    }

    /**
     * Add validator listener
     * @param FormBuilderInterface $builder
     * @param Notification         $notification
     * @param string               $name
     * @param array                $constraints
     */
    protected function setValidator(FormBuilderInterface $builder, Notification $notification, $name, array $constraints = [])
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($notification, $name, $constraints) {
            $data = $event->getData();

            if (isset($data[$notification->getName()]['enabled']) && $data[$notification->getName()]['enabled']) {
                // Validator
                $validator = Validation::createValidator();
                $violations = $validator->validate($data[$notification->getName()][$name], $constraints);

                // Errors
                if (count($violations) !== 0) {
                    /** @var ConstraintViolationInterface $violation */
                    foreach ($violations as $violation) {
                        $event->getForm()->get($notification->getName())->get($name)->addError(new FormError($violation->getMessage()));
                    }
                }
            }
        });
    }
}
