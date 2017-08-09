<?php

namespace NotificationBundle\Transporter;

use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Transporter\AbstractTransporter;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\Extension\Core\Type as TypeForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;
use UserBundle\Validator\Jetable;

/**
 * Class MailTransporter
 * @package NotificationBundle\Transporter
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
            ->setSubject($this->sender[1].' - '.$type->getShortMessage())
            ->setTo($address, $user->getUsername());

        // Body
        $body = $this->twig->render('@Notification/Mail/layout.html.twig', [
            'username' => $user->getUsername(),
            'message' => $type->getLongMessage(),
            'game' => $event->getGame(),
        ]);
        $message->setBody($body, 'text/html');

        // Send
        $send = $this->mailer->send($message);

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

        // Default value
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($notification) {
            $data = $event->getData();

            // Set email from config or from user
            if (!isset($data[$notification->getName()]['email'])) {
                $email = $notification->getConfigurationValue('email', $notification->getUser()->getEmail());
                $data[$notification->getName()]['email'] = $email;
                $event->setData($data);
            }
        });

        // Validator
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($notification) {
            $data = $event->getData();

            if (isset($data[$notification->getName()]['enabled']) && $data[$notification->getName()]['enabled']) {
                // Validator
                $validator = Validation::createValidator();
                $violations = $validator->validate($data[$notification->getName()]['email'], [
                    new Constraints\NotBlank(),
                    new Constraints\Email(),
                    new Jetable(),
                ]);

                // Errors
                if (count($violations) !== 0) {
                    /** @var ConstraintViolationInterface $violation */
                    foreach ($violations as $violation) {
                        $event->getForm()->get($notification->getName())->get('email')->addError(new FormError($violation->getMessage()));
                    }
                }
            }
        });

        return $fields;
    }
}
