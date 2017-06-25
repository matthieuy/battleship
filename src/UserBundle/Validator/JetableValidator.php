<?php

namespace UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class JetableValidator
 *
 * @package UserBundle\Validator
 */
class JetableValidator extends ConstraintValidator
{
    private $jetables = [
        // [0-9]
        "@0815.", "5ymail.com",
        // A
        "@a45.in", "@armyspy.com", "@anonymbox.com",
        // B
        "@bundes-li.ga", "@bund.us",
        // C
        "@cachedot.net", "@cuvox.de",
        // D
        "@discard.email", "@discardmail.", "@dingbone.com", "@dayrep.com", "@dispostable.com", "@dodsi.com",
        "@deadaddress.com",
        // E
        "@emailtemporanea.net", "@einrot.com", "@email-jetable.", "@easy-trash-mail.com",
        "@easytrashmail.com", "e4ward.com",
        // F
        "@fakeinbox.com", "@freundin.ru", "@fiifke.de", "@fudgerub.com", "@fleckens.hu", "@filzmail.com",
        // G
        "@guerrillamail.", "@guerrillamailblock.com", "@getairmail.com", "@get2mail.", "@gustr.com",
        // H
        "@hulapla.de", "@hartbot.de", "@hmamail.com",
        // I
        "@incognitomail.org",
        // J
        "@jobbikszimpatizans.hu", "@jetable.", "@jourrapide.com",
        // K
        "kurzepost.de",
        // L
        "@loadby.us", "@labetteraverouge.at", "@lookugly.com",
        // M
        "@mailinator.com", "@meltmail.com", "@mailnesia.com", "@mt2015.com", "@mt2014", "@mt2009", "@mytrashmail",
        "@mail-temporaire.", "@mailtemporaire.", "@mailcatch.com", "mintemail.com", "@mytempemail.com",
        // N
        "@notmailinator.com", "@notsharingmy.info",
        // O
        "@opayq.com", "@objectmail.com",
        // P
        "@pfui.ru", "@proxymail.eu",
        // R
        "@rmqkr.", "@rhyta.com", "@rcpt.at",
        // S
        "@sharklasers.com", "@spambog.", "@s0ny.net", "@spamstack.net", "@sweetxxx.de", "@showslow.de",
        "@smellfear.com", "spam4.me", "@superrito.com", "@spamgourmet.com", "@spamfree24.org", "@spamobox.com",
        // T
        "@tempinbox.com", "@teewars.org", "thankyou2010.com", "trash2009.com", "@trashymail", "@teleworm.us",
        "tempomail.fr", "@trbvm.com", "@trash-mail.at", "@trashmail.", "@tempemail.net",
        // W
        "@webuser.in", "@wegwerfmail.",
        // Y
        "@yopmail.", "@yapped.net",
    ];

    /**
     * Validate email temp
     * @param string     $value      E-mail
     * @param Constraint $constraint Constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $value = strtolower($value);

        foreach ($this->jetables as $jetable) {
            if (strpos($value, $jetable) !== false) {
                $this->context->addViolation($constraint->message);
                break;
            }
        }
    }
}
