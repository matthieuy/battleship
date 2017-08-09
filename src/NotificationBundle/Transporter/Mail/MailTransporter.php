<?php

namespace NotificationBundle\Transporter\Mail;

use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Transporter\AbstractTransporter;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\Extension\Core\Type as TypeForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Constraints;
use UserBundle\Validator\Jetable;

/**
 * Class MailTransporter
 * @package NotificationBundle\Transporter\Mail
 */
class MailTransporter extends AbstractTransporter
{
    private $mailer;
    private $twig;
    private $sender;

    /**
     * MailTransporter constructor.
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param array             $sender
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $sender)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
        $this->twig = $twig;
    }

    /**
     * Get the transporter name
     * @return string
     */
    public function getName()
    {
        return 'mail';
    }

    /**
     * Send notification
     * @param Notification              $notification
     * @param GameEventInterface        $event
     * @param TypeNotificationInterface $type
     * @var MailTypeInterface           $type
     *
     * @return bool
     */
    public function send(Notification $notification, GameEventInterface $event, TypeNotificationInterface $type)
    {
        $user = $notification->getUser();
        $address = $notification->getConfigurationValue('email', $user->getEmail());

        // Create message
        $message = new \Swift_Message();
        $message
            ->setFrom($this->sender[0], $this->sender[1])
            ->setSubject($this->sender[1].' - '.$type->getSubject())
            ->setTo($address, $user->getUsername());

        // Body
        $body = $this->twig->render('@Notification/Mail/layout.html.twig', [
            'username' => $user->getUsername(),
            'message' => $type->getTextMail(),
            'game' => $event->getGame(),
        ]);
        $message->setBody($body, 'text/html');

        // Send
        try {
            $send = $this->mailer->send($message);
        } catch (\Exception $e) {
            $send = 0;
        }

        return ($send > 0);
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
        $fields[] = $builder->create('email', TypeForm\EmailType::class, [
            'label' => 'form.email',
            'required' => false,
            'translation_domain' => 'FOSUserBundle',
        ]);
        $this->setDefaultValue($builder, $notification, 'email', $notification->getUser()->getEmail());

        // Validator
        $this->setValidator($builder, $notification, 'email', [
            new Constraints\NotBlank(),
            new Constraints\Email(),
            new Jetable(),
        ]);

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
