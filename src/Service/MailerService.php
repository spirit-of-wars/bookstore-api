<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Class MailerService
 * @package App\Service
 */
class MailerService
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $receiver
     * @param string $sender
     * @param string $subject
     * @param string $templateName
     */
    public function sendText($receiver, $sender, $subject, $text)
    {
        $email = (new Email())
            ->from($sender)
            ->to($receiver)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html($text);

        $this->mailer->send($email);
    }

    // public function sendTemplate
}
