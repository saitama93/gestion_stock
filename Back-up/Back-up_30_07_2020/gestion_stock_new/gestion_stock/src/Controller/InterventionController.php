<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Lieu;
use App\Entity\Materiel;
use App\Entity\Referent;
use App\Repository\UserRepository;
use Doctrine\ORM\Query\Expr\Join;
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
     * Index des interventions
     */
    public function index(){
        return $this->render('intervention/intervention.html.twig');
    }

    /**
     * @Route("/intervention/depart",name="Intervention.depart",methods={"GET","POST"})
     * Choix de l'intervenant
     */
    public function depart(Request $request){
        $em= $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')){
            $idRef = explode("-",$_POST['referent'])[0];
            return $this->redirectToRoute('Intervention.validateReferent',array(
                'retour' => 0,
                'id' => $idRef,
            ));
        }
        $referents = $em->getRepository(Referent::class)->findBy(array('present'=>1));
        return $this->render('intervention/depart.html.twig',
        [
            'retour' => 0,
            'referents'=>$referents,
        ]);

    }

    /**
     * @Route("/intervention/validateReferent/{id}/{retour}",name="Intervention.validateReferent",methods={"GET"})
     * Créer une intervention pour la suite tout en verifiant que le référent existe
     */
    public function validateReferent(Request $request,$id,$retour){
        // @codeCoverageIgnoreStart
        $em = $this->getDoctrine()->getManager();
        $referent = $em->getRepository(Referent::class)->findOneBy(array('idreferent' => $id,'present'=>1));
        if ($referent == null){
            return $this->redirectToRoute('Intervention.depart');
        }
        $intervention = new Intervention();
        $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>'Fort Griffon'));
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
        // @codeCoverageIgnoreEnd
    }

    /**
     * @Route("/intervention/annuler/{id}",name="Intervention.annuler",methods={"GET"})
     * Supprime l'intervention pour revenir en arriere
     */
    public function annuler(Request $request,$id){
        // @codeCoverageIgnoreStart
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
        // @codeCoverageIgnoreEnd
    }

    /**
     * @Route("/intervention/add/{id}/{retour}",name="Intervention.add",methods={"GET","POST"})
     * Permet de rajouter du matériel
     */
    public function add(Request $request,$id,$retour){
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime();
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        // @codeCoverageIgnoreStart
        if ($request->isMethod('POST')){
            if (isset($_POST['materiel'])){
                $materiel = $em->getRepository(Materiel::class)
                    ->findOneBy(array('numeroserie'=>$_POST['materiel']));
                if ($materiel != null){
                    $interEncours = $em->getRepository(Intervention::class)->findBy(array('statutinter'=>'En cours'));
                    $verif = true ;
                    foreach ($interEncours as $data){
                        if ($em->getRepository(Intervient::class)->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$data))!=null){
                            $verif = false;
                        }
                    }
                    if ($verif){
                        $temp = $em->getRepository(Intervient::class)
                            ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$id));
                        if ($temp == null){
                            $intervient = new Intervient();
                            $intervient->setIdintervention($intervention);
                            $intervient->setIdmateriel($materiel);
                            if (isset($_POST['statut'])){
                                $intervient->setStatut($_POST['statut']);
                            }
                            if ($retour==1){
                                $intervient->setIdlieu($materiel->getIdlieu());
                                $intervient->setDateaffectation($date->format('d/m/Y'));
                            } else {
                                if (isset($_POST['lieu'])){
                                    $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                    $intervient->setIdlieu($lieu);
                                    $intervient->setDateaffectation($date->format('d/m/Y'));
                                }
                            }
                            $em->persist($intervient);
                            $em->flush();
                        } else {
                            if (isset($_POST['statut'])){
                                $temp->setStatut($_POST['statut']);
                            }
                            if ($retour==1){
                                $temp->setIdlieu($materiel->getIdlieu());
                                $temp->setDateaffectation($date->format('d/m/Y'));
                            } else {
                                if (isset($_POST['lieu'])){
                                    $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                    $temp->setIdlieu($lieu);
                                    $temp->setDateaffectation($date->format('d/m/Y'));
                                }
                            }
                            $em->persist($temp);
                            $em->flush();
                        }
                    }
                }
            } else {
                $materiels = $em->getRepository(Materiel::class)->findAll();
                foreach ($materiels as $matos){
                    if (isset($_POST['materiel'.$matos->getIdmateriel()])){
                        $interEncours = $em->getRepository(Intervention::class)->findBy(array('statutinter'=>'En cours'));
                        $verif = true ;
                        foreach ($interEncours as $data){
                            if ($em->getRepository(Intervient::class)->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$data))!=null){
                                $verif = false;
                            }
                        }
                        if ($verif){
                            $temp = $em->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$id));
                            if ($temp == null){
                                $intervient = new Intervient();
                                $intervient->setIdintervention($intervention);
                                $intervient->setIdmateriel($matos);
                                if (isset($_POST['statut'])){
                                    $intervient->setStatut($_POST['statut']);
                                }
                                if ($retour==1){
                                    $intervient->setIdlieu($matos->getIdlieu());
                                    $intervient->setDateaffectation($date->format('d/m/Y'));
                                } else {
                                    if (isset($_POST['lieu'])){
                                        $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                        $intervient->setIdlieu($lieu);
                                        $intervient->setDateaffectation($date->format('d/m/Y'));
                                    }
                                }
                                $em->persist($intervient);
                                $em->flush();
                            } else {
                                if (isset($_POST['statut'])){
                                    $temp->setStatut($_POST['statut']);
                                }
                                if ($retour==1){
                                    $temp->setIdlieu($matos->getIdlieu());
                                    $temp->setDateaffectation($date->format('d/m/Y'));
                                } else {
                                    if (isset($_POST['lieu'])){
                                        $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                        $temp->setIdlieu($lieu);
                                        $temp->setDateaffectation($date->format('d/m/Y'));
                                    }
                                }
                                $em->persist($temp);
                                $em->flush();
                            }
                        }
                    }
                }
            }

        }
        // @codeCoverageIgnoreEnd
        $list = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention' => $id));
        $lieux =  $em->getRepository(Lieu::class)->findAll();
        return $this->render('intervention/addIntervention.html.twig',
        [
            'id' => $id,
            'intervient'=> $list,
            'retour' => $retour,
            'intervention' => $intervention,
            'lieux' => $lieux,
        ]);
    }

    /**
     * @Route("intervention/listMateriel/{id}/{retour}",name="Intervention.listMateriel",methods={"GET","POST"})
     * Affiche la liste de tous les materiels disponible
     */
    public function listMateriel($id,$retour){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Materiel::class);
        $qb = $repository->createQueryBuilder('m');
        if ($retour == 1){
            $qb->innerJoin(Lieu::class,'l','WITH','m.idlieu=l.idlieu')
                ->where('m.supprimer = :sup OR m.supprimer IS NULL')
                ->setParameter('sup','')
                ->andwhere('l.libellelieu != :lieu')
                ->setParameter('lieu','Fort Griffon');
            $materiels = $qb->getQuery()->getResult();
        } else {
            $qb->where('m.supprimer = :sup')
                ->setParameter('sup','')
                ->orwhere('m.supprimer IS NULL');
            $materiels = $qb->getQuery()->getResult();
        }
        $intervient = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        $lieux = $em->getRepository(Lieu::class)->findAll();
        return $this->render('intervention/listMateriel.html.twig',[
            'materiels' => $materiels,
            'intervient' => $intervient,
            'lieux' => $lieux,
            'retour'=>$retour
        ]);
    }

    /**
     * @Route("intervention/addCsv/{id}/{edit}",name="Intervention.addCsv",methods={"GET","POST"})
     * Permet de rajouter du matériel à une intervention
     */
    public function addCsv($edit,$id,Request $request){
        $em = $this->getDoctrine()->getManager();
        // @codeCoverageIgnoreStart
        if ($request->isMethod('POST')) {
            if (isset($_FILES['csvFile'])) {
                $extension = array('.csv');
                $file = strrchr($_FILES['csvFile']['name'], '.');
                if (in_array($file, $extension)) {
                    $csv = fopen($_FILES['csvFile']['tmp_name'],'r');
                    while (!feof($csv)){
                        $line[]= fgetcsv($csv,1024,';');
                    }
                    fclose($csv);
                    $verif = false ;
                    foreach ($line as $data){
                        if ($verif){
                            $materiel = $em->getRepository(Materiel::class)->find($data[0]);
                            if ($materiel != null){
                                $intervention = $em->getRepository(Intervention::class)
                                    ->findOneBy(array('idintervention'=>$id));
                                $intervient = $em->getRepository(Intervient::class)->findOneBy(array(
                                    'idintervention'=>$id,'idmateriel'=>$data[0]));
                                if ($intervient==null){
                                    $intervient = new Intervient();
                                    $intervient->setIdintervention($intervention);
                                    $intervient->setIdmateriel($materiel);
                                    $em->persist($intervient);
                                    $em->flush();
                                }
                            }
                        }
                        $verif = true ;
                    }
                    if ($edit == 0 || $edit == 1){
                        return $this->redirectToRoute('Intervention.add',array(
                            'id'=>$id,
                            'retour' => $edit,
                        ));
                    } else {
                        return $this->redirectToRoute('Intervention.edit',array(
                            'id' => $id
                        ));
                    }

                }
            }
        }
        // @codeCoverageIgnoreEnd
        return $this->render('intervention/addCsv.html.twig',[
            'id'=>$id,
            'edit'=>$edit,
        ]);
    }

    /**
     * @Route("intervention/changeUser/{id}/{retour}", name="Intervention.changeUser", methods={"GET","POST"})
     * Permet de changer d'utilisateur
     */
    public function changeUser(Request $request,$id,$retour){
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')){
            $idRef = explode("-",$_POST['referent'])[0];
            $referent = $em->getRepository(Referent::class)->findOneBy(array('idreferent' => $idRef,'present'=>1));
            if ($referent != null){
                $intervention = $em->getRepository(Intervention::class)->find($id);
                $intervention->setIdreferent($referent);
                $em->persist($intervention);
                $em->flush();
                return $this->redirectToRoute('Intervention.add',array(
                    'id' => $id,
                    'retour' => $retour
                ));
            }

        }
        $referents = $em->getRepository(Referent::class)->findBy(array('present'=>1));
        return $this->render('intervention/changeUser.html.twig',[
            'id' => $id,
            'referents'=>$referents,
        ]);
    }
    /**
     * @Route("intervention/delete/{idMat}/{idInt}/{retour}",name="Intervention.delete",methods={"GET"})
     * Permet de supprimer un materiel d'une intervention
     */
    public function delete($idMat,$idInt,$retour){
        $em = $this->getDoctrine()->getManager();
        $intervient = $em->getRepository(Intervient::class)
            ->findOneBy(array('idintervention'=>$idInt,'idmateriel'=>$idMat));
        $em->remove($intervient);
        $em->flush();
        if ($retour == 0 || $retour == 1){
            return $this->redirectToRoute('Intervention.add',array(
                'id' => $idInt,
                'retour' => $retour,
            ));
        } else {
            return $this->redirectToRoute('Intervention.edit',array(
                'id' => $idInt,
            ));
        }
    }
    /**
     * @Route("intervention/edit/{id}",name="Intervention.edit",methods={"GET","POST"})
     * Permet de modifier les matériels d'une intervention
     */
    public function edit(Request $request,$id){
        $date = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)
            ->findOneBy(array('idintervention'=>$id));
        // @codeCoverageIgnoreStart
        if ($request->isMethod('POST')){
            if (isset($_POST['materiel'])){
                $materiel = $em->getRepository(Materiel::class)
                    ->findOneBy(array('numeroserie'=>$_POST['materiel']));

                if ($materiel != null){
                    $interEncours = $em->getRepository(Intervention::class)->findBy(array('statutinter'=>'En cours'));
                    $verif = true ;
                    foreach ($interEncours as $data){
                        if ($em->getRepository(Intervient::class)->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$data))!=null){
                            $verif = false;
                        }
                    }
                    if ($verif){
                        $temp = $em->getRepository(Intervient::class)
                            ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$id));
                        if ($temp == null){
                            $intervient = new Intervient();
                            $intervient->setIdintervention($intervention);
                            $intervient->setIdmateriel($materiel);
                            if (isset($_POST['statut'])){
                                $intervient->setStatut($_POST['statut']);
                            }
                            if (isset($_POST['lieu'])){
                                $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                $intervient->setIdlieu($lieu);
                                $intervient->setDateaffectation($date->format('d/m/Y'));
                            }
                            $em->persist($intervient);
                            $em->flush();
                        } else {
                            if (isset($_POST['statut'])){
                                $temp->setStatut($_POST['statut']);
                            }
                            if (isset($_POST['lieu'])){
                                $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                $temp->setIdlieu($lieu);
                                $temp->setDateaffectation($date->format('d/m/Y'));
                            }
                            $em->persist($temp);
                            $em->flush();
                        }
                    }
                }
            } else {
                $materiels = $em->getRepository(Materiel::class)->findAll();
                foreach ($materiels as $matos){
                    if (isset($_POST['materiel'.$matos->getIdmateriel()])){
                        $interEncours = $em->getRepository(Intervention::class)->findBy(array('statutinter'=>'En cours'));
                        $verif = true ;
                        foreach ($interEncours as $data){
                            if ($em->getRepository(Intervient::class)->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$data))!=null){
                                $verif = false;
                            }
                        }
                        if ($verif){
                            $temp = $em->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$matos,'idintervention'=>$id));
                            if ($temp == null){
                                $intervient = new Intervient();
                                $intervient->setIdintervention($intervention);
                                $intervient->setIdmateriel($matos);
                                if (isset($_POST['statut'])){
                                    $intervient->setStatut($_POST['statut']);
                                }
                                if (isset($_POST['lieu'])){
                                    $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                    $intervient->setIdlieu($lieu);
                                    $intervient->setDateaffectation($date->format('d/m/Y'));
                                }
                                $em->persist($intervient);
                                $em->flush();
                            } else {
                                if (isset($_POST['statut'])){
                                    $temp->setStatut($_POST['statut']);
                                }
                                if (isset($_POST['lieu'])){
                                    $lieu = $em->getRepository(Lieu::class)->find($_POST['lieu']);
                                    $temp->setIdlieu($lieu);
                                    $temp->setDateaffectation($date->format('d/m/Y'));
                                }
                                $em->persist($temp);
                                $em->flush();
                            }
                        }
                    }
                }
            }

        }
        // @codeCoverageIgnoreEnd
        $list = $em->getRepository(Intervient::class)
            ->findBy(array('idintervention' => $id));
        $lieux = $em->getRepository(Lieu::class)->findAll();
        return $this->render('intervention/editIntervention.html.twig',
            [
                'id' => $id,
                'intervient'=> $list,
                'lieux'=>$lieux,
            ]);
    }

    /**
     * @Route("/intervention/downloadRecap/{id}",name="Intervention.downloadRecap",methods={"GET"})
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
            . '<p>Referent : '.$intervention->getIdreferent()->getNom().' '.$intervention->getIdreferent()->getPrenom().'</p>'
            .'<table class="table">'
            .'<thead class="thead-dark">'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Nouveau statut</th><th>Date d\'affectation</th>'
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
                .'<td>'.$materiel->getIdmateriel()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$lieu.'</td>'
                .'<td>'.$materiel->getStatutLibelle().'</td>'
                .'<td>'.$materiel->getDateaffectation().'</td></tr>'
                .'<style>table, th, td {border: 1px solid black}</style>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','D');
        exit;
    }

    /**
     * @Route("/intervention/retour",name="Intervention.retour",methods={"GET"})
     */
    public function retour(){
        return $this->render('intervention/retour.html.twig');
    }

    /**
     * @Route("/intervention/nouveauRetour",name="Intervention.nouveauRetour",methods={"GET","POST"})
     * Créer une intervention afin de retourner du matériel
     */
    public function nouveauRetour(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')){
            $idRef = explode("-",$_POST['referent'])[0];
            return $this->redirectToRoute('Intervention.validateReferent',array(
                'retour' => 1,
                'id' => $idRef,
            ));
        }
        $referents = $em->getRepository(Referent::class)->findBy(array('present'=>1));
        return $this->render('intervention/depart.html.twig',
            [
                'retour' => 1,
                'referents' => $referents
            ]);
    }

    /**
     * @Route("/intervention/validateRetour/{id}",name="Intervention.validateRetour",methods={"GET","POST"})
     * Permet de finir un retour de matériel
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
            $matos->setIdreferent($intervention->getIdreferent());
            $matos->setStatut($materiel->getStatut());
            $em->persist($materiel);
            $em->flush();
            $matos->setIdlieu($materiel->getIdintervention()->getIdLieu());
            $em->persist($matos);
            $em->flush();
        }
        $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé. "
            ."\nVoici un rapport concernant l'intervention en pièce jointe.";
        $mpdf = new Mpdf();
        $contenus = '<h1>Recapitulatif de l\'intervention</h1>'
            . '<p>Date : '.$intervention->getDateintervention() .'</p>'
            . '<p>Referent : '.$intervention->getIdreferent()->getNom().' '.$intervention->getIdreferent()->getPrenom().'</p>'            .'<table>'
            .'<thead>'
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
                .'<td>'.$materiel->getDateaffectation().'</td></tr>'
                .'<style>table, th, td {border: 1px solid black}</style>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','S');

        $email = new PHPMailer();
        $email->setFrom('lucas.laine@doubs.fr');
        $email->Subject=utf8_decode('Intervention terminé');
        $email->Body=utf8_decode($message);
        if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test') {
            $email->addAddress('lucas.laine@doubs.fr');
        } else {
            $email->addAddress('lucas.laine@doubs.fr');
        }
        $email->addStringAttachment($pdf,'Recapitulatif.pdf');

        $email->send();
        return $this->redirectToRoute('Intervention.index');
    }

    /**
     * @Route("/intervention/retour/encours",name="Intervention.encours",methods={"GET"})
     * Affiche toutes les interventions en cours
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
     * Affiche les détails d'une intervention
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
     * Permet de clotûrer une intervention
     */
    public function terminerIntervention($id){
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)->find($id);
        $intervient = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        $verif = true ;
        $em = $this->getDoctrine()->getManager();
        foreach ($intervient as $materiel) {
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
        } else {
            $intervention->setStatutinter("En attente");
            $em->persist($intervention);
            $em->flush();
            //@toDo Voir pour mettre le bon message et les bonnes adresses
            ini_set("SMTP","relais-exchange.doubs.fr");
            if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test'){
                $user = 'lucas.laine@doubs.fr';
            } else {
                $user = 'florian.bailly.doubs.fr';
            }

            $message = "L'intervention datant du ". $intervention->getDateintervention() . " a été clotturé mais "
                ."certains matériaux n'ont pas été affecté \n Veuillez vous rendre à cette page pour affecté les matériels : blalbla.fr";
            mail($user,utf8_decode('Intervention terminé'),utf8_decode($message));
        }

        return $this->redirectToRoute('Intervention.encours');
    }

    /**
     * @Route("/intervention/rendre/{id}",name="Intervention.rendre",methods={"GET","POST"})
     * Permet de ramener du matériel non utilisé
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
     * Valide le retour de matériel non utilisé
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
            if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test'){
                $user = 'lucas.laine@doubs.fr';
            } else {
                $user = 'florian.bailly.doubs.fr';
            }
            mail($user,utf8_decode('Intervention terminé'),utf8_decode($message));
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
     * Permet d'affecter du matériel à un lieu
     */
    public function affectation(Request $request, $id){
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();
        $intervention = $doctrine->getRepository(Intervention::class)->findOneBy(array('idintervention'=>$id));
        $materiels = $doctrine->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        $lieux = $doctrine->getRepository(Lieu::class)->findAll();
        // @codeCoverageIgnoreStart
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
                            $inter->setStatut(intval($_POST['statut']));
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
        // @codeCoverageIgnoreEnd
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
     * Verifie que tous les matériels sont affectés
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
        return $this->redirectToRoute('Historique.index');
    }

    /**
     * @Route("intervention/sendMailRecap/{id}",name="Intervention.sendMailRecap",methods={"GET"})
     * Permet d'envoyer un mail si toute les informations sont correct
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
        $contenus = '<h1>Recapitulatif de l\'intervention</h1>'
            . '<p>Date : '.$intervention->getDateintervention() .'</p>'
            . '<p>Referent : '.$intervention->getIdreferent()->getNom().' '.$intervention->getIdreferent()->getPrenom().'</p>'            .'<table class="table">'
            .'<thead>'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Nouveau statut</th><th>Date d\'affectation</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getStatutLibelle().'</td>'
                .'<td>'.$materiel->getDateaffectation().'</td></tr>'
                .'<style>table, th, td {border: 1px solid black}</style>';
        }
        $contenus .= '</tbody></table>';
        $mpdf->WriteHTML(utf8_encode($contenus));
        $pdf = $mpdf->Output('Recapitulatif.pdf','S');

        $email = new PHPMailer();
        $email->setFrom('lucas.laine@doubs.fr');
        $email->Subject=utf8_decode('Intervention terminé');
        $email->Body=utf8_decode($message);
        if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test'){
            $email->addAddress('lucas.laine@doubs.fr');
        } else {
            $email->addAddress('lucas.laine@doubs.fr');
        }
        $email->addStringAttachment($pdf,'Recapitulatif.pdf');

        $email->send();
        foreach ($materiels as $materiel){
            $matos = $em->getRepository(Materiel::class)
                ->findOneBy(array('idmateriel'=>$materiel->getIdmateriel()->getIdmateriel()));
            $matos->setIdlieu($materiel->getIdlieu());
            $matos->setStatut($materiel->getStatut());
            $matos->setDate($materiel->getDateaffectation());
            $em->persist($matos);
            $em->flush();
        }
        return $this->redirectToRoute('Intervention.encours');
    }

}