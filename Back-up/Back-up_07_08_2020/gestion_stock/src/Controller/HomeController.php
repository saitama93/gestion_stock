<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="Home.index",methods={"GET","POST"})
     * Accueil du site
     */
    public function index(AuthenticationUtils $authenticationUtils){
        $nombre = random_int(1,5);
        if ($this->getUser()) {
            return $this->render('home/home.html.twig',[
                'nombre' => $nombre
            ]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/home.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'nombre' => $nombre,
        ]);
    }

    /**
     * @Route("/goBack/{lib}/{id}",name="Home.goBack",methods={"GET","POST"})
     * Permet le retour en arrière sur toute les pages
     */
    public function goBack($lib,$id){
        return $this->render('goBack.html.twig',[
            'lib' => $lib,
            'id' => $id,
        ]);
    }
}