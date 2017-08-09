<?php

namespace NotificationBundle\Transporter\Discord;

/**
 * Interface DiscordWebhookTypeInterface
 * @package NotificationBundle\Transporter\Discord
 */
interface DiscordWebhookTypeInterface
{
    /**
     * Get the hook text
     * @return string
     */
    public function getDiscordHookText();
}
