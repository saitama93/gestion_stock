<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="Home.index",methods={"GET"})
     * Accueil du site
     */
    public function index(){
        $nombre = random_int(1,5);
        return $this->render('home/home.html.twig',[
            'nombre' => $nombre
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