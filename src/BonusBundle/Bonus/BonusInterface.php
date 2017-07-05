<?php

namespace BonusBundle\Bonus;

/**
 * Interface BonusInterface
 * @package BonusBundle\Bonus
 */
interface BonusInterface
{
    /**
     * Get the unique name of the bonus
     * @return string
     */
    public function getName();

    /**
     * Get the bonus description
     * @return string
     */
    public function getDescription();
}
