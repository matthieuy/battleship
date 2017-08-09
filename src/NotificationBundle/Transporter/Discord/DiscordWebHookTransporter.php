<?php

namespace NotificationBundle\Transporter\Discord;

use GuzzleHttp\Client;
use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Transporter\AbstractTransporter;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Class DiscordWebHookTransporter
 * @package NotificationBundle\Transporter\Discord
 */
class DiscordWebHookTransporter extends AbstractTransporter
{
    /**
     * Get the transporter name
     * @return string
     */
    public function getName()
    {
        return 'discord_hook';
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
        /** @var DiscordWebhookTypeInterface $type */
        try {
            $client = new Client();
            $r = $client->request('POST', $notification->getConfigurationValue('hook'), [
                'json' => [
                    'content' => $type->getDiscordHookText(),
                ],
            ]);

            return ($r->getStatusCode() == 200);
        } catch (\Exception $e) {
            return false;
        }
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

        // Add URL field
        $fields[] = $builder->create('hook', UrlType::class, [
            'label' => 'Webhook',
            'required' => false,
        ]);
        $this->setDefaultValue($builder, $notification, 'hook');
        $this->setValidator($builder, $notification, 'hook', [
            new Constraints\NotBlank(),
            new Constraints\Url(),
            new Constraints\Regex([
                'pattern' => '/^https:\/\/discordapp\.com\/api\/webhooks\/([0-9]{18})\/([0-9a-zA-Z]+)$/',
            ]),
        ]);

        return $fields;
    }

    /**
     * Is personal transporter
     * @return boolean
     */
    public function isPersonal()
    {
        return false;
    }
}
