<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * Page d'accueil de l'adminstrateur
     * 
     * @Route("/admin/dashboard", name="AdminDasboard.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
