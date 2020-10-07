<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Intervient;
use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Materiel;
use App\Entity\Statut;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
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
        $users = $em->getRepository(User::class)->findBy(array('present'=>1));
        return $this->render('intervention/depart.html.twig',
        [
            'retour' => 0,
            'users'=>$users,
        ]);

    }

    /**
     * @Route("/intervention/validateReferent/{id}/{retour}",name="Intervention.validateReferent",methods={"GET"})
     * Créer une intervention pour la suite tout en verifiant que le référent existe
     */
    public function validateReferent(Request $request,$id,$retour){
        // @codeCoverageIgnoreStart
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('iduser' => $id,'present'=>1));
        if ($user == null){
            return $this->redirectToRoute('Intervention.depart');
        }
        $intervention = new Intervention();
        $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>'besançon.fort-griffon'));
        $intervention->setDateintervention(date('d/m/Y H:i:s',strtotime('+2 hours')));
        $intervention->setIduser($user);
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
                        if ($em->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$data))!=null){
                            $verif = false;
                        }
                    }
                    if ($retour == 0){
                        $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>'besançon.fort-griffon'));
                        if ($materiel->getIdlieu() != $lieu){
                            $verif = false ;
                        }
                    }
                    if ($verif){
                        $temp = $em->getRepository(Intervient::class)
                            ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$id));
                        if ($temp == null){
                            $intervient = new Intervient();
                            $intervient->setIdintervention($intervention);
                            $intervient->setIdmateriel($materiel);
                            $intervient->setIdlieudepart($materiel->getIdlieu());
                            if ($retour==1){
                                $intervient->setDateaffectation($date->format('d/m/Y'));
                                $statut= $em->getRepository(Statut::class)->find(1);
                                $intervient->setIdstatut($statut);
                            }
                            $em->persist($intervient);
                            $em->flush();
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
    public function listMateriel(Request $request,$id,$retour){
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Materiel::class);
        $intervention = $em->getRepository(Intervention::class)->find($id);
        $qb = new QueryBuilder($em);
        $query = $repository->createQueryBuilder('m');
        $search = 0 ;
        if ($retour == 1){
            $query->innerJoin(Lieu::class,'l','WITH','m.idlieu=l.idlieu')
                ->where('m.supprimer = :sup OR m.supprimer IS NULL')
                ->setParameter('sup','')
                ->andwhere('l.libellelieu != :lieu')
                ->setParameter('lieu','besançon.fort-griffon');
        } else if ($retour == 0) {
            $query->innerJoin(Lieu::class,'l','WITH','m.idlieu=l.idlieu');
            $query->where($qb->expr()->isNull('m.supprimer'));
            $query->orwhere('m.supprimer = :data')
                ->setParameter('data',"")
                ->andwhere('l.libellelieu = :lieu')
                ->setParameter('lieu','besançon.fort-griffon');
        } else {
            $query->where($qb->expr()->isNull('m.supprimer'));
            $query->orwhere('m.supprimer = :data')
                ->setParameter('data',"");
        }
        if (isset($_POST['search_materiel'])){ // Si l'on recherche sur le nom
            $query->andwhere($qb->expr()->like('m.nommateriel',':nom'))
                ->setParameter('nom','%'.$_POST['search_materiel'].'%');
            $search = 1;
        }
        if (isset($_POST['search_num'])){ // Si l'on recherche sur le numero de série
            $query->andwhere($qb->expr()->like('m.numeroserie',':num'))
                ->setParameter('num','%'.$_POST['search_num'].'%');
            $search = 1;
        }
        if (isset($_POST['statut'])){
            if ($_POST['statut']!=-1){
                $query->andwhere('m.idstatut = :statut')
                    ->setParameter('statut',$_POST['statut']);
                $search = 1 ;
            }
        }
        if (isset($_POST['type'])){ // Si l'on recherche sur le type de matériel
            if ($_POST['type']!=0){ //Verifie si il y a pas la valeur par défaut
                $query->andwhere('m.idtype = :type')
                    ->setParameter('type',$_POST['type']);
                $search = 1;
            }
        }
        if (isset($_POST['marque'])){
            if ($_POST['marque']!=0){
                $query->andwhere('m.idmarque = :marque')
                    ->setParameter('marque',$_POST['marque']);
                $search = 1;
            }
        }
        if (isset($_POST['lieu'])){
            if ($_POST['lieu']!=0){
                $query->andwhere('m.idlieu = :lieu')
                    ->setParameter('lieu',$_POST['lieu']);
                $search = 1;
            }
        }
        if (isset($_POST['date'])){
            if ($_POST['date']!=0){
                $query->andwhere('str_to_date(m.date,\'%d/%m/%Y\') > str_to_date(:date,\'%d/%m/%Y\')')
                    ->setParameter('date',$_POST['date']);
                $search = 1 ;
            }
        }
        if (isset($_POST['motscles'])){
            $temp = 0 ; //Pour mettre plusieurs conditions
            foreach ($_POST['motscles'] as $data){
                $query->andwhere($qb->expr()->like('m.motscles',':mot'.$temp))
                    ->setParameter('mot'.$temp,'%'.$data.'%');
                $temp++;
            }
            $search = 1;
        }

        $materiels = $query->getQuery()->getResult();

        if ($request->isMethod('POST') && $search != 1){
            $date = new \DateTime();
            $keys = array_keys($_POST);
            foreach ($keys as $key){
                if ($key != 'checkAll'){
                    $key = trim($key,'materiel');
                    $materiel = $em->getRepository(Materiel::class)->find($key);
                    $interEncours = $em->getRepository(Intervention::class)->findBy(array('statutinter'=>'En cours'));
                    $verif = true ;
                    foreach ($interEncours as $data){
                        if ($em->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$data))!=null){
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
                            $intervient->setIdlieudepart($materiel->getIdlieu());
                            if ($retour==1){
                                $intervient->setDateaffectation($date->format('d/m/Y'));
                                $statut= $em->getRepository(Statut::class)->find(1);
                                $intervient->setIdstatut($statut);
                            }
                            $em->persist($intervient);
                            $em->flush();
                        }
                    }
                }

            }
            if ($retour == 2){
                return $this->redirectToRoute('Intervention.edit',array(
                    'id'=>$id,
                ));
            } else {
                return $this->redirectToRoute('Intervention.add',array(
                    'id'=>$id,
                    'retour'=>$retour,
                ));
            }
        }

        $types = $em->getRepository(Type::class)->findAll();
        $marques = $em->getRepository(Marque::class)->findAll();
        $lieux = $em->getRepository(Lieu::class)->findAll();
        $matos = $em->getRepository(Materiel::class)->findAll();
        $statut = $em->getRepository(Statut::class)->findAll();
        $motscles = [];
        foreach ($matos as $materiel){
            $mot = $materiel->getMotscles();
            $mots = explode("\n",$mot);
            foreach ($mots as $data){
                if (!in_array($data,$motscles)){
                    array_push($motscles,$data);
                }
            }
        }
        return $this->render('intervention/listMateriel.html.twig',[
            'id' => $id,
            'materiels' => $materiels,
            'retour'=>$retour,
            'types' => $types,
            'lieux' => $lieux,
            'marques'=> $marques,
            'statuts'=>$statut,
            'motscles'=>$motscles,
            'search'=>$search,
        ]);
    }

    /**
     * @Route("intervention/addCsv/{id}/{edit}",name="Intervention.addCsv",methods={"GET","POST"})
     * Permet de rajouter du matériel à une intervention
     */
    public function addCsv($edit,$id,Request $request){
        $em = $this->getDoctrine()->getManager();
        $date = new \DateTime();
        // @codeCoverageIgnoreStart
        if ($request->isMethod('POST')) {
            if (isset($_FILES['csvFile'])) {
                $extension = array('.file');
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
                                    $intervient->setIdlieudepart($materiel->getIdlieu());
                                    if ($edit == 1){
                                        $intervient->setDateaffectation($date->format('d/m/Y'));
                                        $statut= $em->getRepository(Statut::class)->find(1);
                                        $intervient->setIdstatut($statut);
                                    }
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
            $user = $em->getRepository(User::class)->findOneBy(array('iduser' => $idRef,'present'=>1));
            if ($user != null){
                $intervention = $em->getRepository(Intervention::class)->find($id);
                $intervention->setIduser($user);
                $em->persist($intervention);
                $em->flush();
                return $this->redirectToRoute('Intervention.add',array(
                    'id' => $id,
                    'retour' => $retour
                ));
            }

        }
        $users = $em->getRepository(User::class)->findBy(array('present'=>1));
        return $this->render('intervention/changeUser.html.twig',[
            'id' => $id,
            'users'=>$users,
            'retour'=>$retour
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
                        if ($em->getRepository(Intervient::class)
                                ->findOneBy(array('idmateriel'=>$materiel,'idintervention'=>$data))!=null){
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
                            $intervient->setIdlieudepart($materiel->getIdlieu());
                            $em->persist($intervient);
                            $em->flush();
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
     * @Route("/intervention/partirInter/{id}",name="Intervention.partirInter",methods={"GET"})
     * Permet d'afficher la page de redirection
     */
    public function partirInter($id){
        return $this->render('intervention/downloadRecap.html.twig',[
            'id' => $id,
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
            . '<p>Referent : '.$intervention->getIduser()->getNom().' '.$intervention->getIduser()->getPrenom().'</p>'
            .'<table class="table">'
            .'<thead class="thead-dark">'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){

            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdlieudepart()->getLibellelieu().'</td>'
                .'</tr>'
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
        $referents = $em->getRepository(User::class)->findBy(array('present'=>1));
        return $this->render('intervention/depart.html.twig',
            [
                'retour' => 1,
                'users' => $referents
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
            $materiel->setIdlieuarrive($materiel->getIdintervention()->getIdLieu());
            $matos->setIduser($intervention->getIduser());
            $matos->setIdstatut($materiel->getIdstatut());
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
            . '<p>Referent : '.$intervention->getIduser()->getNom().' '.$intervention->getIduser()->getPrenom().'</p>'            .'<table>'
            .'<thead>'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Statut</th><th>Date d\'affectation</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdlieudepart()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdintervention()->getIdlieu()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdstatut()->getLibellestatut().'</td>'
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
    public function terminerIntervention($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $intervention = $em->getRepository(Intervention::class)->find($id);
        $intervient = $em->getRepository(Intervient::class)->findBy(array('idintervention'=>$id));
        $verif = true ;
        $em = $this->getDoctrine()->getManager();
        foreach ($intervient as $materiel) {
            if ($materiel->getIdlieuarrive() == null){
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
                ."certains matériaux n'ont pas été affecté \n"
                ."Veuillez vous rendre à cette page pour affecté les matériels : ".$request->getSchemeAndHttpHost()
                . "/intervention/affectation/".$id;
            mail($user,utf8_decode('Intervention terminé'),utf8_decode($message));
        }

        return $this->redirectToRoute('Intervention.encours');
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
                            $statut = $doctrine->getRepository(Statut::class)->find($_POST['statut']);
                            $inter->setIdLieuarrive($lieu);
                            $inter->setDateaffectation($date->format('d/m/Y'));
                            $inter->setIdStatut($statut);
                            $matos->setDate($date->format('d/m/Y'));
                            $matos->setIduser($intervention->getIduser());
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
        $statuts = $em->getRepository(Statut::class)->findAll();
        return $this->render('intervention/affectation.html.twig',
            [
                'intervention' => $intervention,
                'materiels' => $materiels,
                'lieux' => $lieux,
                'statuts' => $statuts
            ]
        );
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
            . '<p>Referent : '.$intervention->getIduser()->getNom().' '.$intervention->getIduser()->getPrenom().'</p>'            .'<table class="table">'
            .'<thead>'
            .'<tr><th>Numero de Serie</th><th>Nom du materiel</th><th>Marque</th><th>Type</th><th>Ancien Lieu</th>'
            .'<th>Nouveau Lieu</th><th>Nouveau statut</th><th>Date d\'affectation</th>'
            .'</tr></thead><tbody>';
        foreach ($materiels as $materiel){
            $contenus .= '<tr><td>'.$materiel->getIdmateriel()->getNumeroserie().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getNommateriel().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdmarque()->getLibellemarque().'</td>'
                .'<td>'.$materiel->getIdmateriel()->getIdtype()->getLibelletype().'</td>'
                .'<td>'.$materiel->getIdlieudepart()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdlieuarrive()->getLibellelieu().'</td>'
                .'<td>'.$materiel->getIdStatut()->getLibellestatut().'</td>'
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
            $matos->setIdlieu($materiel->getIdlieuarrive());
            $matos->setIdstatut($materiel->getIdstatut());
            $matos->setDate($materiel->getDateaffectation());
            $em->persist($matos);
            $em->flush();
        }
        return $this->redirectToRoute('Intervention.encours');
    }

}