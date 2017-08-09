<?php

namespace NotificationBundle\Type;

use NotificationBundle\Transporter\Discord\DiscordTypeInterface;
use NotificationBundle\Transporter\Mail\MailTypeInterface;

/**
 * Class TourTypeNotification
 * @package NotificationBundle\Type
 */
class TourTypeNotification extends AbstractTypeNotification implements MailTypeInterface, DiscordTypeInterface
{
    const NAME = 'TOUR';

    /**
     * Get type name
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Get type of transporters denied
     * @return array
     */
    public function getDeniedTransporters()
    {
        return [];
    }

    /**
     * Get mail subject
     * @return string
     */
    public function getSubject()
    {
        $context = [
            '%game%' => $this->event->getGame()->getName(),
        ];

        return $this->translator->trans('tour.short', $context, 'notifications');
    }

    /**
     * Get mail text
     * @return string
     */
    public function getTextMail()
    {
        return $this->getSubject();
    }

    /**
     * Get the hook text
     * @return string
     */
    public function getDiscordHookText()
    {
        $names = [];
        $players = $this->event->getGame()->getPlayersTour();
        foreach ($players as $player) {
            $names[] = $player->getName();
        }
        $text = $this->translator->trans('waiting_list', ['%list%' => implode(', ', $names)]);

        return $text;
    }

    /**
     * Get the text for PM discord bot
     * @return string
     */
    public function getDiscordBotText()
    {
        return $this->translator->trans('tour.short', ['%game%' => $this->event->getGame()->getName()], 'notifications');
    }
}
