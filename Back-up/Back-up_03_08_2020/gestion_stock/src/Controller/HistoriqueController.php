<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Materiel;
use Mpdf\Mpdf;
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

    /**
     * @Route("/historique/downloadRecap/{id}",name="Historique.downloadRecap",methods={"GET"})
     * Permet de télécharger un récapitulatif de l'intervention
     */
    public function downloadRecap($id){
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention'=>$id));
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        $mpdf = new Mpdf();
        $contenus = '<h1>Recapitulatif de l\'intervention</h1>'
            . '<p>Date : '.$intervention->getDateintervention() .'</p>'
            . '<p>Referent : '.$intervention->getIduser()->getNom().' '.$intervention->getIduser()->getPrenom().'</p>'
            .'<table class="table">'
            .'<thead class="thead-dark">'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $lieu = $materiel->getIdlieu();
            if ($lieu!=null){
                $lieu = $lieu->getLibellelieu();
            }
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdintervention()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$lieu.'</td>'
                .'</tr>'
                .'<style>table, th, td {border: 1px solid black}</style>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','D');
        exit;
    }
}