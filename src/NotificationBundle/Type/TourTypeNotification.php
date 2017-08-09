<?php

namespace NotificationBundle\Type;

/**
 * Class TourTypeNotification
 * @package NotificationBundle\Type
 */
class TourTypeNotification extends AbstractTypeNotification
{
    const NAME = 'TOUR';

    /**
     * Get ShortMessage
     * @return string
     */
    public function getShortMessage()
    {
        $context = [
            '%game%' => $this->event->getGame()->getName(),
        ];

        return $this->translator->trans('tour.short', $context, 'notifications');
    }

    /**
     * Get no personal shortMessage
     * @return string
     */
    public function getGlobalShortMessage()
    {
        $names = [];
        $players = $this->event->getGame()->getPlayersTour();
        foreach ($players as $player) {
            $names[] = $player->getName();
        }
        $text = $this->translator->trans('waiting_list', ['%list%' => implode(', ', $names)]);
        $text .= ' - '.$this->event->getGame()->getName();

        return $text;
    }


    /**
     * Get LongMessage
     * @return string
     */
    public function getLongMessage()
    {
        return $this->getShortMessage();
    }

    /**
     * Get type name
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Check if allowed to use this type with transporter
     * @return array
     */
    public function getAllowedTransporters()
    {
        return [
            'mail',
            'discord_hook',
        ];
    }
}
