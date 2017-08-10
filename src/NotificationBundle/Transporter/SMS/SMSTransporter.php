<?php

namespace NotificationBundle\Transporter\SMS;

use Doctrine\ORM\EntityManager;
use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Entity\SMS;
use NotificationBundle\Transporter\AbstractTransporter;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Class SMSTransporter
 * @package NotificationBundle\Transporter\SMS
 */
class SMSTransporter extends AbstractTransporter
{
    private $entityManager;

    /**
     * SMSTransporter constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the transporter name
     * @return string
     */
    public function getName()
    {
        return 'sms';
    }

    /**
     * Send notification
     * @param Notification              $notification
     * @param GameEventInterface        $event
     * @param TypeNotificationInterface $type
     *
     * @return bool
     */
    public function send(Notification $notification, GameEventInterface $event, TypeNotificationInterface $type)
    {
        /** @var SMSTypeInterface $type */
        // Get config
        $start = $notification->getConfigurationValue('start', '00:00');
        $end = $notification->getConfigurationValue('end', '23:59');
        $number = $notification->getConfigurationValue('number', false);

        // Get date
        $startDate = \DateTime::createFromFormat('H:i', $start);
        $endDate = \DateTime::createFromFormat('H:i', $end);
        $now = new \DateTime();
        if ($startDate >= $endDate) {
            $endDate->add(\DateInterval::createFromDateString('+1 day'));
            $startDate->add(\DateInterval::createFromDateString('-1 day'));
        }

        if ($number && $startDate <= $now && $endDate >= $now) {
            $sms = new SMS();
            $sms
                ->setNumber($number)
                ->setMessage($type->getSMSText());
            $this->entityManager->persist($sms);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

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
        $fields = parent::getFormFields($builder, $notification, $options);

        // Add Number field
        $fields[] = $builder->create('number', Type\TextType::class, [
            'label' => 'number_phone',
            'required' => false,
        ]);
        $this->setDefaultValue($builder, $notification, 'number');
        $this->setValidator($builder, $notification, 'number', [
            new Constraints\NotBlank(),
            new Constraints\Regex([
                'pattern' => '/^([0-9]+)$/',
            ]),
        ]);

        // Start hour
        $fields[] = $builder->create('start', Type\TimeType::class, [
            'label' => 'start_hour',
            'required' => false,
            'input' => 'string',
            'widget' => 'single_text',
        ]);
        $this->setDefaultValue($builder, $notification, 'start');

        // End hour
        $fields[] = $builder->create('end', Type\TimeType::class, [
            'label' => 'end_hour',
            'required' => false,
            'input' => 'string',
            'widget' => 'single_text',
        ]);
        $this->setDefaultValue($builder, $notification, 'end');

        return $fields;
    }

    /**
     * Is personal transporter
     * @return boolean
     */
    public function isPersonal()
    {
        return true;
    }
}
