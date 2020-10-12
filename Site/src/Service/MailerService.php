<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService  {

  
    /**
     * Permet d'envoyer un mail
     * 
     * @param string $message
     * @param string $sender
     * @param string $recipient
     * @param string $object
     */
    public function sendMail(string $message, string $sender, string $recipient, string $object, string $senderName){

        try {
            $transport = (new \Swift_SmtpTransport('relais-exchange.doubs.fr', 25));
            
            // Création du mail
            $mailer = new \Swift_Mailer($transport);
            
            $mail = (new \Swift_Message($object))
            ->setFrom([$sender => $senderName])
            ->setTo([$recipient])
            ->setBody($message);

            // Envoie du mail
            $mailer->send($mail);

        } catch (\Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }
    }
}