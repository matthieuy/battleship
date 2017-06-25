<?php

namespace UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Jetable
 *
 * @Annotation
 * @package UserBundle\Validator
 */
class Jetable extends Constraint
{
    public $message = "Don't use a jetable email";
}
