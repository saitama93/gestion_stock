<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Service\PaginationService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminArchiveController extends AbstractController
{
    /**
     * Permet l'accès aux archives
     * 
     * @Route("/admin/archives", name="AdminArchive.index")
     */
    public function index()
    {
        return $this->render('admin/archive/index.html.twig');
    }

    /**
     * Permet d'afficher les matériels archivé
     * 
     * @Route("/admin/archive/materiels/{page<\d+>?1}", name="AdminArchive.materiels")
     */
    public function materiels($page, PaginationService $paginator)
    {

        $paginator->setEntityClass(Materiel::class)
            ->setCurrentPage($page)
            ->setLimit(10);

        return $this->render('admin/materiel/index.html.twig', [
            'paginator' => $paginator,
            'archive' => true
        ]);
    }

    // public function
}
