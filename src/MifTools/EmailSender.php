<?php


namespace App\MifTools;

use App\Constants;
use App\Mif;
use App\Util\RabbitMq\RabbitMq;

class EmailSender
{

    private string $docRoot;

    /**
     * @param string $docRoot
     */
    public function setDocRoot(string $docRoot) : void
    {
        $this->docRoot = $docRoot;
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $email, string $subject, string $message) : bool
    {
        switch (Mif::getEnvConfig(Constants::CK_EMAIL_SEND_MODE)) {
            case Constants::EMAIL_SEND_RABBIT:
                return $this->setAmqpMessage($message);
                break;
            case Constants::EMAIL_SEND_FILE:
                $this->saveMessageAsFile($message);
                return true;
                break;
            case Constants::EMAIL_SEND_BOTH:
                $this->saveMessageAsFile($message);
                return $this->setAmqpMessage($message);
                break;
            case Constants::EMAIL_SEND_STRAIGHT:
                Mif::getServiceProvider()->MailerService->sendText(
                    $email,
                    Mif::getEnvConfig(Constants::CK_MIF_SENDER_EMAIL),
                    $subject,
                    $message
                );
                return true;
        }

        return false;
    }

    /**
     * @param string $message
     * @return bool
     */
    private function setAmqpMessage(string $message) : bool
    {
        $amqp = RabbitMq::getInstances();
        return $amqp->setMessage('authentication', $message);
    }

    /**
     * @param string $message
     */
    private function saveMessageAsFile(string $message) : void
    {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] . $this->docRoot;
        if (!is_dir($docRoot)) {
            mkdir($docRoot, 0777);
        }
        file_put_contents($docRoot . '/' . date('YmdHis').'.txt', $message);
    }
}