<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminArchiveController extends AbstractController
{
    /**
     * Permet l'accès aux archives
     * 
     * @Route("/admin/archive", name="AdminArchive.index")
     */
    public function index()
    {
        return $this->render('admin/archive/index.html.twig');
    }
}
