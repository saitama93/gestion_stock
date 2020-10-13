<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="AdminDasboard.index")
     */
    public function index()
    {
        return $this->render('admin/dashboard/index.html.twig');
    }
}
