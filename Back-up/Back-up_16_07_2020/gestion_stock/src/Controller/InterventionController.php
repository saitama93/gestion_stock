<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Lieu;
use App\Entity\Materiel;
use App\Entity\Referent;
use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class InterventionController extends AbstractController
{

    /**
     * @Route("/intervention",name="Intervention.index",methods={"GET"})
     */
    public function index(){
        return $this->render('intervention/intervention.html.twig');
    }

    /**
     * @Route("/intervention/depart",name="Intervention.depart",methods={"GET","POST"})
     */
    public function depart(Request $request){
        if ($request->isMethod('POST')){
            $idRef = explode("-",$_POST['referent'])[0];
            return $this->redirectToRoute('Intervention.validateReferent',array(
                'retour' => 0,
                'id' => $idRef,
            ));
        }
        return $this->render('intervention/depart.html.twig',
        [
            'retour' => 0,
        ]);

    }

    /**
     * @Route("/intervention/validateReferent/{id}/{retour}",name="Intervention.validateReferent",methods={"GET"})
     */
    public function validateReferent(Request $request,$id,$retour){
        $em = $this->getDoctrine()->getManager();
        $intervention = new Intervention();
        $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>'Fort Griffon'));
        $referent = $em->getRepository(Referent::class)->findOneBy(array('idreferent' => $id));
        $intervention->setDateintervention(date('d/m/Y H:i:s',strtotime('+2 hours')));
        $intervention->setIdreferent($referent);
        $intervention->setIdlieu($lieu);
        if ($retour == 0){
            $intervention->setStatutinter("En cours");
        } else {
            $intervention->setStatutinter("Finis");
        }
        $em->persist($intervention);
        $em->flush();
        return $this->redirectToRoute('Intervention.add',array('id' => $intervention->getIdintervention(),
            'retour' => $retour
        ));

    }

    /**
     * @Route("/intervention/annuler/{id}",name="Intervention.annuler",methods={"GET"})
     */
    public function annuler(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)->find($id);
        while ($em->getRepository(Intervient::class)->findBy(array('idintervention' => $id))!= null){
            $intervient = $em->getRepository(Intervient::class)
                ->findOneBy(array('idintervention' => $id));
            $em->remove($intervient);
            $em->flush();
        }
        $em->remove($intervention);
        $em->flush();
        return $this->redirectToRoute('Intervention.index');
    }

    /**
     * @Route("/intervention/add/{id}/{retour}",name="Intervention.add",methods={"GET","POST"})
     */
    public function add(Request $request,$id,$retour){
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime();
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        if ($request->isMethod('POST')){
            if (isset($_POST['materiel'])){
                $materiel = $em->getRepository(Materiel::class)
                    ->findOneBy(array('numeroserie'=>$_POST['materiel']));
                if ($materiel != null){
                    $temp = $em->getRepository(Intervient::class)
                        ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$id));
                    if ($temp == null){
                        $intervient = new Intervient();
                        $intervient->setIdintervention($intervention);
                        $intervient->setIdmateriel($materiel);
                        if ($retour==1){
                            $intervient->setIdlieu($materiel->getIdlieu());
                            $intervient->setDateaffectation($date->format('d/m/Y'));
                        }
                        $em->persist($intervient);
                        $em->flush();
                    }
                }
            } else {
                $materiels = $em->getRepository(Materiel::class)->findAll();
                foreach ($materiels as $matos){
                    if (isset($_POST['materiel'.$matos->getIdmateriel()])){
                        $temp = $em->getRepository(Intervient::class)
                            ->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$id));
                        if ($temp == null){
                            $intervient = new Intervient();
                            $intervient->setIdintervention($intervention);
                            $intervient->setIdmateriel($matos);
                            if ($retour==1){
                                $intervient->setIdlieu($matos->getIdlieu());
                                $intervient->setDateaffectation($date->format('d/m/Y'));
                            }
                            $em->persist($intervient);
                            $em->flush();
                        }
                    }
                }
            }

        }
        $list = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention' => $id));
        return $this->render('intervention/addIntervention.html.twig',
        [
            'id' => $id,
            'intervient'=> $list,
            'retour' => $retour,
            'intervention' => $intervention,
        ]);
    }

    /**
     * @Route("intervention/listMateriel/{id}",name="Intervention.listMateriel",methods={"GET","POST"})
     */
    public function listMateriel($id){
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Materiel::class)->findAll();
        $intervient = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        return $this->render('intervention/listMateriel.html.twig',[
            'materiels' => $materiels,
            'intervient' => $intervient,
        ]);
    }
    /**
     * @Route("intervention/changeUser/{id}/{retour}", name="Intervention.changeUser", methods={"GET","POST"})
     */
    public function changeUser(Request $request,$id,$retour){
        if ($request->isMethod('POST')){
            $em = $this->getDoctrine()->getManager();
            $idRef = explode("-",$_POST['referent'])[0];
            $intervention = $em->getRepository(Intervention::class)->find($id);
            $referent = $em->getRepository(Referent::class)->find($idRef);
            $intervention->setIdreferent($referent);
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('Intervention.add',array(
                'id' => $id,
                'retour' => $retour
            ));
        }
        return $this->render('intervention/changeUser.html.twig',[
            'id' => $id,
        ]);
    }
    /**
     * @Route("intervention/edit/{id}",name="Intervention.edit",methods={"GET","POST"})
     */
    public function edit(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        if ($request->isMethod('POST')){
            if (isset($_POST['materiel'])){
                $materiel = $em->getRepository(Materiel::class)
                    ->findOneBy(array('numeroserie'=>$_POST['materiel']));

                if ($materiel != null){
                    $temp = $em->getRepository(Intervient::class)
                        ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$id));
                    if ($temp == null){
                        $intervient = new Intervient();
                        $intervient->setIdintervention($intervention);
                        $intervient->setIdmateriel($materiel);
                        $em->persist($intervient);
                        $em->flush();
                    }
                }
            } else {
                $materiels = $em->getRepository(Materiel::class)->findAll();
                foreach ($materiels as $matos){
                    if (isset($_POST['materiel'.$matos->getIdmateriel()])){
                        $temp = $em->getRepository(Intervient::class)
                            ->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$id));
                        if ($temp == null){
                            $intervient = new Intervient();
                            $intervient->setIdintervention($intervention);
                            $intervient->setIdmateriel($matos);
                            $em->persist($intervient);
                            $em->flush();
                        }
                    }
                }
            }

        }
        $list = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention' => $id));
        return $this->render('intervention/editIntervention.html.twig',
            [
                'id' => $id,
                'intervient'=> $list,
            ]);
    }

    /**
     * @Route("/intervention/retour",name="Intervention.retour",methods={"GET"})
     */
    public function retour(){
        return $this->render('intervention/retour.html.twig');
    }

    /**
     * @Route("/intervention/nouveauRetour",name="Intervention.nouveauRetour",methods={"GET","POST"})
     */
    public function nouveauRetour(Request $request){
        if ($request->isMethod('POST')){
            $idRef = explode("-",$_POST['referent'])[0];
            return $this->redirectToRoute('Intervention.validateReferent',array(
                'retour' => 1,
                'id' => $idRef,
            ));
        }
        return $this->render('intervention/depart.html.twig',
            [
                'retour' => 1,
            ]);
    }

    /**
     * @Route("/intervention/validateRetour/{id}",name="Intervention.validateRetour",methods={"GET","POST"})
     */
    public function validateRetour(Request $request,$id){
        ini_set("SMTP","relais-exchange.doubs.fr");
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention'=>$id));
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        foreach ($materiels as $materiel){
            $matos = $em->getRepository(Materiel::class)
                ->findOneBy(array('idmateriel'=>$materiel->getIdmateriel()->getIdmateriel()));
            $materiel->setIdLieu($matos->getIdlieu());
            $materiel->setIdreferent($intervention->getIdreferent());
            $em->persist($materiel);
            $em->flush();
            $matos->setIdlieu($materiel->getIdintervention()->getIdLieu());
            $em->persist($matos);
            $em->flush();
        }
        $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé. "
            ."\nVoici un rapport concernant l'intervention en pièce jointe.";
        $mpdf = new Mpdf();
        $contenus = '<table class="table">'
            .'<thead class="thead-dark">'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Statut</th><th>Date d\'affectation</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdintervention()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getStatutLibelle().'</td>'
                .'<td>'.$materiel->getDateaffectation().'</td></tr>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','S');

        $email = new PHPMailer();
        $email->setFrom('lucas.laine@doubs.fr');
        $email->Subject=utf8_decode('Intervention terminé');
        $email->Body=utf8_decode($message);
        $email->addAddress('lucas.laine@doubs.fr');
        $email->addStringAttachment($pdf,'Recapitulatif.pdf');

        $email->send();
        return $this->redirectToRoute('Intervention.index');
    }

    /**
     * @Route("/intervention/retour/encours",name="Intervention.encours",methods={"GET"})
     */
    public function encours(){
        $em = $this->getDoctrine();
        $interventions = $em->getRepository(Intervention::class)->findBy(array(),array('dateintervention'=>'DESC'));
        return $this->render('intervention/encours.html.twig',
            [
                'interventions' => $interventions,
            ]);
    }

    /**
     * @Route("/intervention/retour/details/{id}",name="Intervention.details",methods={"GET"})
     */
    public function details($id){
        $em = $this->getDoctrine();
        $intervention = $em->getRepository(Intervention::class)->findOneBy(array('idintervention'=>$id));
        $materiels = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        return $this->render('intervention/details.html.twig',
            [
                'intervention' => $intervention,
                'materiels' => $materiels,
            ]
        );
    }

    /**
     * @Route("/intervention/retour/terminer/{id}",name="Intervention.terminer",methods={"GET"})
     */
    public function terminerIntervention($id){
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)->find($id);
        $intervient = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));

        $intervention->setStatutinter("En attente");
        $em->persist($intervention);
        $em->flush();

        // Test de mail
        //@toDo Voir pour mettre le bon message et les bonnes adresses
        ini_set("SMTP","relais-exchange.doubs.fr");

        $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé mais "
        ."certains matériaux n'ont pas été affecté \n Veuillez vous rendre à cette page pour affecté les matériels : blalbla.fr";
        mail('lucas.laine@doubs.fr',utf8_decode('Intervention terminé'),utf8_decode($message));


        return $this->redirectToRoute('Intervention.encours');
    }

    /**
     * @Route("/intervention/rendre/{id}",name="Intervention.rendre",methods={"GET","POST"})
     */
    public function rendre(Request $request,$id){
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $intervention = $doctrine->getRepository(Intervention::class)->findOneBy(array('idintervention'=>$id));
        $materiels = $doctrine->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        if ($request->isMethod('POST')){
            $materiel = $em->getRepository(Materiel::class)
                ->findOneBy(array('numeroserie'=>$_POST['materiel']));
            $intervient = $em->getRepository(Intervient::class)
                ->findOneBy(array('idintervention'=>$id,'idmateriel'=>$materiel));
            if ($intervient != null){
                $lieu = $em->getRepository(Lieu::class)
                    ->findOneBy(array('libellelieu'=>'Fort Griffon'));
                $date = new \DateTime();
                $intervient->setDateaffectation($date->format('d/m/Y'));
                $intervient->setIdlieu($lieu);
                $materiel->setDate($date->format('d/m/Y'));
                $materiel->setIdLieu($lieu);
                $materiel->setStatut(0);
                $materiel->setIdreferent($intervention->getIdreferent());
                $em->persist($intervient);
                $em->flush();
                $em->persist($materiel);
                $em->flush();
            }
        }
        return $this->render('intervention/rendre.html.twig',
            [
                'intervention' => $intervention,
                'materiels' => $materiels,
                'id' => $id,
            ]
        );
    }

    /**
     * @Route("/intervention/finisRendre/{id}",name="Intervention.finisRendre",methods={"GET"})
     */
    public function finisRendre($id){
        ini_set("SMTP","relais-exchange.doubs.fr");
        $verif = true ;
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention'=>$id));
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));

        foreach ($materiels as $materiel) {
            if ($materiel->getIdlieu() == null){
                $verif = false ;
            }
        }
        if (!$verif){
            //@toDo Voir pour mettre le bon message et les bonnes adresses
            $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé mais "
                ."certains matériaux n'ont pas été affecté \n Veuillez vous rendre à cette page pour affecté les matériels : blalbla.fr";
            mail('lucas.laine@doubs.fr',utf8_decode('Intervention terminé'),utf8_decode($message));
            return $this->redirectToRoute('Intervention.encours');
        } else {
            $intervention->setStatutinter("Terminé");
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('Intervention.sendMailRecap',array('id'=>$id));
        }


    }

    /**
     * @Route("/intervention/affectation/{id}",name="Intervention.affectation",methods={"GET","POST"})
     */
    public function affectation(Request $request, $id){
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $intervention = $doctrine->getRepository(Intervention::class)->findOneBy(array('idintervention'=>$id));
        $materiels = $doctrine->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        $lieux = $doctrine->getRepository(Lieu::class)->findAll();
        if ($request->isMethod('POST')){
            if (isset($_POST['lieu'])){
                $lieu = $doctrine->getRepository(Lieu::class)
                    ->findOneBy(array('libellelieu'=>$_POST['lieu']));
                if ($lieu != null){
                    $date = new \DateTime();
                    foreach ($materiels as $materiel) {
                        if (isset($_POST['idMateriel'.$materiel->getIdmateriel()->getIdmateriel()])){
                            $idMateriel = $materiel->getIdmateriel()->getIdmateriel();
                            $inter = $doctrine->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$idMateriel,'idintervention'=>$id));
                            $matos = $doctrine->getRepository(Materiel::class)
                                ->findOneBy(array('idmateriel'=>$idMateriel));
                            $inter->setIdLieu($lieu);
                            $inter->setDateaffectation($date->format('d/m/Y'));
                            $matos->setStatut(intval($_POST['statut']));
                            $matos->setDate($date->format('d/m/Y'));
                            $matos->setIdreferent($intervention->getIdreferent());
                            $em->persist($inter);
                            $em->flush();
                            $em->persist($matos);
                            $em->flush();
                        }
                    }
                }
            }
        }
        return $this->render('intervention/affectation.html.twig',
            [
                'intervention' => $intervention,
                'materiels' => $materiels,
                'lieux' => $lieux,
            ]
        );
    }

    /**
     * @Route("/intervention/terminerAffectation/{id}",name="Intervention.terminerAffectation",methods={"GET"})
     */
    public function terminerAffectation($id){
        $verif = true ;
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention'=>$id));
        foreach ($materiels as $materiel) {
            if ($materiel->getIdlieu() == null){
                $verif = false ;
            }
        }
        if ($verif){
            $intervention = $em->getRepository(Intervention::class)->find($id);
            $intervention->setStatutinter("Terminé");
            $em->persist($intervention);
            $em->flush();
            return $this->redirectToRoute('Intervention.sendMailRecap',array('id'=>$id));
        }
        return $this->redirectToRoute('Intervention.encours');
    }

    /**
     * @Route("intervention/sendMailRecap/{id}",name="Intervention.sendMailRecap",methods={"GET"})
     */
    public function sendMailRecap($id){
        ini_set("SMTP","relais-exchange.doubs.fr");
        $em = $this->getDoctrine()->getManager();
        $materiels = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention'=>$id));
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));


        $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé. "
            ."\nVoici un rapport concernant l'intervention en pièce jointe.";
        $mpdf = new Mpdf();
        $contenus = '<table class="table">'
            .'<thead class="thead-dark">'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Statut</th><th>Date d\'affectation</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdintervention()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getStatutLibelle().'</td>'
                .'<td>'.$materiel->getDateaffectation().'</td></tr>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','S');

        $email = new PHPMailer();
        $email->setFrom('lucas.laine@doubs.fr');
        $email->Subject=utf8_decode('Intervention terminé');
        $email->Body=utf8_decode($message);
        $email->addAddress('lucas.laine@doubs.fr');
        $email->addStringAttachment($pdf,'Recapitulatif.pdf');

        $email->send();
        foreach ($materiels as $materiel){
            $matos = $em->getRepository(Materiel::class)
                ->findOneBy(array('idmateriel'=>$materiel->getIdmateriel()->getIdmateriel()));
            $matos->setIdlieu($materiel->getIdlieu());
            $em->persist($matos);
            $em->flush();
        }
        return $this->redirectToRoute('Intervention.encours');
    }
}