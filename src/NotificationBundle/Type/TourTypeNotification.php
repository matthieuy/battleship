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
        ];
    }
}
