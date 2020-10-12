<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService  {

  
    /**
     * Permet d'envoyer un mail
     * 
     * @param string $message => Message du mail
     * @param string $sender => L'expéditeur du mail
     * @param string $recipient => Le destinataire du mail
     * @param string $object =>  Le nom de l'expéditeur
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