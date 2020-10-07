<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Lieu;
use App\Entity\Materiel;
use App\Entity\Type;
use App\Entity\User;
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
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Intervient::class);
        //$interventions = $em->getRepository(Intervention::class)->findAll();
        $search = 0 ;
        $query = $repository->createQueryBuilder('i');
        $query->select('ref.prenom','ref.nom','inter.idintervention','inter.dateintervention','inter.statutinter')
            ->innerJoin(Intervention::class,'inter','WITH','i.idintervention=inter.idintervention')
            ->innerJoin(Materiel::class,'m','WITH','i.idmateriel=m.idmateriel')
            ->innerJoin(User::class,'ref','WITH','inter.iduser=ref.iduser')
            ->distinct()
            ->where('inter.statutinter != :statut')
            ->setParameter('statut','En cours');
        if (isset($_POST['type'])){
            if ($_POST['type']!=0){
                $query->andwhere('m.idtype = :type')
                    ->setParameter('type',$_POST['type']);
                $search = 1;
            }
        }
        if (isset($_POST['referent'])){
            if ($_POST['referent']!=0){
                $query->andwhere('inter.iduser = :user')
                    ->setParameter('user',$_POST['referent']);
                $search = 1;
            }
        }
        if (isset($_POST['lieuDep'])){
            if ($_POST['lieuDep']!=0){
                $query->andwhere('i.idlieudepart = :lieu')
                    ->setParameter('lieu',$_POST['lieuDep']);
                $search = 1;
            }
        }
        if (isset($_POST['lieuArri'])){
            if ($_POST['lieuArri']!=0){
                $query->andwhere('i.idlieuarrive = :lieu')
                    ->setParameter('lieu',$_POST['lieuArri']);
                $search = 1;
            }
        }
        if (isset($_POST['date'])){
            if ($_POST['date']!=0){
                $query->andwhere('str_to_date(inter.dateintervention,\'%d/%m/%Y %H:%i:%s\') > str_to_date(:date,\'%d/%m/%Y\')')
                    ->setParameter('date',$_POST['date']);
                $search = 1 ;
            }
        }
        $interventions = $query->getQuery()->getResult();

        $types = $em->getRepository(Type::class)->findAll();
        $referents = $em->getRepository(User::class)->findAll();
        $lieux = $em->getRepository(Lieu::class)->findAll();
        return $this->render('historique/index.html.twig',
            [
                'interventions' => $interventions,
                'search' => $search,
                'types'=>$types,
                'referents' => $referents,
                'lieux' => $lieux,
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
     * @Route("historique/exportCsv",name="Historique.exportCsv",methods={"GET","POST"})
     */
    public function exportCsv(){
        $repository = $this->getDoctrine()->getRepository(Intervention::class);
        $interventions = $repository->findAll();
        $chemin='./csv/historique.csv';

        $fp = fopen($chemin,'w');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp,array(
            'ID',
            'Intervenant',
            'Date d\'intervention',
            'Type d\'intervention'
        ),';','"');
        foreach ($interventions as $intervention){
            if ($intervention->getStatutinter() != 'En cours'){
                if ($intervention->getStatutinter() == 'Terminé') $statut = 'Intervention classique';
                else if ($intervention->getStatutinter() == 'Finis') $statut = 'Retour de matériel';
                else $statut = 'Inconnus';
                $data = array(
                    $intervention->getIdintervention(),
                    $intervention->getIduser()->getNom().' '.$intervention->getIduser()->getPrenom(),
                    $intervention->getDateintervention(),
                    $statut,
                );
                fputcsv($fp,$data,';','"');
            }

        }
        fclose($fp);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($chemin).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($chemin));
        readfile($chemin);
        exit;
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
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdlieudepart()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdlieuarrive()->getLibellelieu().'</td>'
                .'</tr>'
                .'<style>table, th, td {border: 1px solid black}</style>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','D');
        exit;
    }
}