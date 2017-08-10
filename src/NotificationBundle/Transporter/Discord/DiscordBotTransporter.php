<?php

namespace NotificationBundle\Transporter\Discord;

use GuzzleHttp\Client;
use MatchBundle\Event\GameEventInterface;
use NotificationBundle\Entity\Notification;
use NotificationBundle\Transporter\AbstractTransporter;
use NotificationBundle\Type\TypeNotificationInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * Class DiscordBotTransporter
 * @package NotificationBundle\Transporter\Discord
 */
class DiscordBotTransporter extends AbstractTransporter
{
    private $router;
    private $urlApi = 'https://discordapp.com';
    private $discordToken;

    /**
     * DiscordBotTransporter constructor.
     * @param Router $router
     * @param string $discordToken
     */
    public function __construct(Router $router, $discordToken = '')
    {
        $this->discordToken = $discordToken;
        $this->router = $router;
    }

    /**
     * Get the transporter name
     * @return string
     */
    public function getName()
    {
        return 'discord_bot';
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
        /** @var DiscordTypeInterface $type */
        try {
            $game = $event->getGame();
            $client = new Client(['base_uri' => $this->urlApi]);
            $res = $client->request('POST', '/api/channels/'.$notification->getConfigurationValue('id', '0').'/messages', [
                'headers' => [
                    'Authorization' => 'Bot '.$this->discordToken,
                    'User-Agent' => 'Battleship',
                ],
                'json' => [
                    'embed' => [
                        'title' => 'Battleship - '.$game->getName(),
                        'description' => $type->getDiscordBotText(),
                        'url' => $this->router->generate('match.display', ['slug' => $game->getSlug()], Router::ABSOLUTE_URL),
                    ],
                ],
            ]);

            return ($res->getStatusCode() == 200);
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

        // Add ID field
        $fields[] = $builder->create('id', TextType::class, [
            'label' => 'id_discord',
            'required' => false,
        ]);
        $this->setDefaultValue($builder, $notification, 'id');
        $this->setValidator($builder, $notification, 'id', [
            new Constraints\NotBlank(),
            new Constraints\Regex([
                'pattern' => '/^([0-9]{18})$/',
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
        return true;
    }
}
