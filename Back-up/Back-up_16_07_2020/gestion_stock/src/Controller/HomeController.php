<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="Home.index",methods={"GET"})
     */
    public function index(){
        $nombre = random_int(1,5);
        return $this->render('home/home.html.twig',[
            'nombre' => $nombre
        ]);
    }
}