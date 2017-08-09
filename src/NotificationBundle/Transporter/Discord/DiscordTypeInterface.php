<?php

namespace NotificationBundle\Transporter\Discord;

/**
 * Interface DiscordTypeInterface
 * @package NotificationBundle\Transporter\Discord
 */
interface DiscordTypeInterface
{
    /**
     * Get the hook text
     * @return string
     */
    public function getDiscordHookText();

    /**
     * Get the text for PM discord bot
     * @return string
     */
    public function getDiscordBotText();
}
