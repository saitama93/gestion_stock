<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    /**
     * Permet de visualiser le profil de l'utilisateur connecté
     * 
     * @Route("/account/{id}", name="Account.myAccount")
     */
    public function myAccount(User $user)
    {
        // dd($user->getRoles());
        return $this->render('account/index.html.twig', [
            'user' => $user
        ]);
    }
}
