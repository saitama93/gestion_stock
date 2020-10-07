<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Materiel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    /**
     * @Route("/historique",name="Historique.index",methods={"GET","POST"})
     * Affiche l'historique des interventions
     */
    public function index(){
        $em = $this->getDoctrine();
        $interventions = $em->getRepository(Intervention::class)->findAll();
        $search = false ;
        if (isset($_POST['search_num'])){
            if ($_POST['search_num']!=""){
                $materiel = $em->getRepository(Materiel::class)->findOneBy(array('numeroserie'=>$_POST['search_num']));
                $interventions = $em->getRepository(Intervient::class)->findBy(array('idmateriel'=>$materiel));
                $search = true;
            }
        }
        return $this->render('historique/index.html.twig',
            [
                'interventions' => $interventions,
                'search' => $search,
            ]);
    }

    /**
     * @Route("/historique/details/{id}",name="Historique.details",methods={"GET"})
     * Affiche les détails d'une intervention terminés
     */
    public function details($id){
        $em = $this->getDoctrine();
        $intervention = $em->getRepository(Intervention::class)->findOneBy(array('idintervention'=>$id));
        $materiels = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        return $this->render('historique/details.html.twig',
            [
                'intervention' => $intervention,
                'materiels' => $materiels,
                'id' => $id,
            ]
        );
    }
}