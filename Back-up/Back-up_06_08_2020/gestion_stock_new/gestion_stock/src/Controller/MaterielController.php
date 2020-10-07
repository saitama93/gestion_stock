<?php

namespace App\Controller;

use App\Entity\Intervient;
use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Materiel;
use App\Entity\Referent;
use App\Entity\Specificite;
use App\Entity\Statut;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\RegistryInterface;   // ORM Doctrine
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MaterielController extends AbstractController
{
    /**
     * @Route("/stock",name="Materiel.index",methods={"GET","POST"})
     * Affichage des matériels (soit tous les materiels soit en fonctione d'une recherche
     */
    public function index(){
        $repository = $this->getDoctrine()->getRepository(Materiel::class);
        $em = $this->getDoctrine()->getManager();
        $search = 0 ;
        $qb = new QueryBuilder($em);
        $query = $repository->createQueryBuilder('m');
        $query->where($qb->expr()->isNull('m.supprimer'));
        $query->orwhere('m.supprimer = :data')
            ->setParameter('data',"");
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
        $materiels = $query->getQuery()->getResult(); //Recupère le résultat de la requête
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
        return $this->render('stock/materiel.html.twig',
            [
                'materiels' => $materiels,
                'types' => $types,
                'lieux' => $lieux,
                'marques'=> $marques,
                'statuts'=>$statut,
                'motscles'=>$motscles,
                'search'=>$search,
            ]
        );
    }

    /**
     * @Route("/stock/add",name="Materiel.add",methods={"GET","POST"})
     * Permet d'ajouter des matériels
     */
    public function add(Request $request){
        $em = $this->getDoctrine()->getManager();
        $materiel = new Materiel();
        $today = new \DateTime(); //Récupère la date d'aujourd'hui
        $materiel->setDate($today->format('Y-m-d'));
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$materiel);

        //Création du formulaire
        $formBuilder
            ->add('numeroSerie', TextType::class)
            ->add('nomMateriel', TextType::class)
            ->add('idStatut', EntityType::class,[
                'class' => Statut::class,
                'choice_label' => 'libelleStatut',
            ])
            ->add('motsCles',TextareaType::class,[
                'data' => "@",
                'required' => false,
            ])
            ->add('idMarque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'libelleMarque',
            ])
            ->add('idLieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'libelleLieu',
            ])
            ->add('idType',EntityType::class,[
                'class' => Type::class,
                'choice_label' => 'libelleType',
            ])
            ->add('date',DateType::class,[
                'input' => "string",
                'format' => 'dd/MM/yyyy'
            ])
            ->add('idSpecificite',EntityType::class,[
                'class' => Specificite::class,
                'choice_label' => 'libelleSpe',
            ])
            ->add('ajouter', SubmitType::class);

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')){
            $form->handleRequest($request);

            //Verifie les données
            if ($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $temp = date_create_from_format('Y-m-d',$materiel->getDate());
                $materiel->setDate($temp->format('d/m/Y'));
                $em->persist($materiel);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice','Materiel bien créer');
                return $this->redirectToRoute('Materiel.index');
            }
        }
        $materiels = $em->getRepository(Materiel::class)->findAll();
        return $this->render('stock/addMateriel.html.twig',
            [
                'form' => $form->createView(),
                'materiels' => $materiels,
            ]
        );
    }

    /**
     * @Route("stock/edit/{id}",name="Materiel.edit",methods={"GET","POST"})
     * Permet de modifier un materiel
     */
    public function edit(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $materiel = $em->getRepository(Materiel::class)->find($id);
        if ($materiel->getDate() == null){
            $temp = new \DateTime();
        } else {
            $temp = date_create_from_format('d/m/Y',$materiel->getDate());
            if ($temp == false){
                $temp = new \DateTime();
            }
        }

        $materiel->setDate($temp->format('Y-m-d'));

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$materiel);

        $formBuilder
            ->add('numeroSerie', TextType::class)
            ->add('nomMateriel', TextType::class)
            ->add('idStatut', EntityType::class,[
                'class' => Statut::class,
                'choice_label' => 'libelleStatut',
            ])
            ->add('motsCles',TextareaType::class,[
                'required'=>false,
            ])
            ->add('idMarque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'libelleMarque',
            ])
            ->add('idLieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'libelleLieu',
            ])
            ->add('idType',EntityType::class,[
                'class' => Type::class,
                'choice_label' => 'libelleType',
            ])
            ->add('date',DateType::class,[
                'input' => "string",
                'format' => 'dd/MM/yyyy'
            ])
            ->add('idSpecificite',EntityType::class,[
                'class' => Specificite::class,
                'choice_label' => 'libelleSpe',
            ])
            ->add('modifier', SubmitType::class);

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')){
            $form->handleRequest($request);

            if ($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $temp = date_create_from_format('Y-m-d',$materiel->getDate());
                $materiel->setDate($temp->format('d/m/Y'));
                $em->persist($materiel);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice','Materiel bien créer');
                return $this->redirectToRoute('Materiel.index');
            }
        }
        $materiels = $em->getRepository(Materiel::class)->findAll();
        return $this->render('stock/editMateriel.html.twig',
            [
                'form' => $form->createView(),
                'materiels'=> $materiels
            ]
        );
    }

    /**
     * @Route("stock/delete/{id}",name="Materiel.delete",methods={"GET","POST"})
     * Affiche la page de confirmation pour supprimer un materiel
     */
    public function delete(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $materiel = $em->getRepository(Materiel::class)->find($id);
        return $this->render('stock/deleteMateriel.html.twig',
        [
           'materiel' => $materiel
        ]);
    }

    /**
     * @Route("stock/validateDelete",name="Materiel.validateDelete",methods={"DELETE"})
     * "Supprime un materiel"
     */
    public function validateDelete(Request $request){
        $em = $this->getDoctrine()->getManager();
        $materiel = $em->getRepository(Materiel::class)->find($_POST['id']);
        $date = new \DateTime();
        $materiel->setSupprimer($date->format('d/m/Y'));
        $em->persist($materiel);
        $em->flush();
        return $this->redirectToRoute('Materiel.index');
    }

    /**
     * @Route("stock/file",name="Materiel.downloadCsv",methods={"GET"})
     * Telecharge un CSV de la liste des materiels
     */
    public function downloadCsv(){
        $repository = $this->getDoctrine()->getRepository(Materiel::class);
        $materiels = $repository->findAll();
        $chemin='./file/materiel.csv';

        $fp = fopen($chemin,'w');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp,array(
            'ID',
            'Numéro de série',
            'Statut',
            'Nom du matériel',
            'Marque',
            'Lieu',
            'Type',
            'Specificite',
            'Dernier Referent',
            'Date de suppression'
        ),';','"');
        foreach ($materiels as $materiel){
            $marque = null != $materiel->getIdMarque() ? $materiel->getIdmarque()->getLibellemarque() : 'NULL';
            $lieu = null != $materiel->getIdlieu() ? $materiel->getIdlieu()->getLibellelieu() : 'NULL' ;
            $type = null != $materiel->getIdtype() ? $materiel->getIdtype()->getLibelletype() : 'NULL';
            $speci = null != $materiel->getIdspecificite() ? $materiel->getIdspecificite()->getLibellespe() : 'NULL';
            $user = null != $materiel->getIduser() ?
                $materiel->getIduser()->getIduser().'-'.$materiel->getIduser()->getNom() : 'NULL';
            $statut = null != $materiel->getIdstatut() ? $materiel->getIdstatut()->getLibellestatut() : 'NULL';
            $data = array($materiel->getIdmateriel(),
                $materiel->getNumeroserie(),
                $statut,
                $materiel->getNommateriel(),
                $marque,
                $lieu,
                $type,
                $speci,
                $user,
                $materiel->getSupprimer(),
            );
            fputcsv($fp,$data,';','"');
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
     * @Route("/stock/downloadModel",name="Materiel.downloadModel",methods={"GET"})
     */
    public function downloadModel(){
        $chemin='./csv/materiel.csv';

        $fp = fopen($chemin,'w');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp,array(
            'ID',
            'Numéro de série',
            'Statut',
            'Nom du matériel',
            'Marque',
            'Lieu',
            'Type',
            'Specificite',
            'Dernier Referent',
            'Date de suppression'
        ),';','"');
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
     * @Route("stock/cleanBase",name="Materiel.cleanBase",methods={"GET","POST"})
     * Importer un CSV tout en écrasant l'ancienne ainsi que de créer une back up
     */
    public function cleanBase(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')){
            // @codeCoverageIgnoreStart
            if (isset($_FILES['csvFile'])){
                $extension = array('.file');
                $file = strrchr($_FILES['csvFile']['name'],'.');
                if (in_array($file,$extension)){
                    /*
                     * Enregistrement d'une backup de la base
                     */
                    $materiels = $em->getRepository(Materiel::class)->findAll();
                    $chemin='./backup/'.date('d_m_Y_H_i_s',strtotime('+2 hours')).'_backup.csv';

                    $fp = fopen($chemin,'w');
                    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
                    fputcsv($fp,array(
                        'ID',
                        'Numéro de série',
                        'Statut',
                        'Nom du matériel',
                        'Marque',
                        'Lieu',
                        'Type',
                        'Specificite',
                        'Dernier Referent',
                        'Date de suppression'
                    ),';','"');
                    foreach ($materiels as $materiel){
                        $marque = null != $materiel->getIdMarque() ? $materiel->getIdmarque()->getLibellemarque() : 'NULL';
                        $lieu = null != $materiel->getIdlieu() ? $materiel->getIdlieu()->getLibellelieu() : 'NULL' ;
                        $type = null != $materiel->getIdtype() ? $materiel->getIdtype()->getLibelletype() : 'NULL';
                        $speci = null != $materiel->getIdspecificite() ? $materiel->getIdspecificite()->getLibellespe() : 'NULL';
                        $user = null != $materiel->getIduser() ?
                            $materiel->getIduser()->getIduser().'-'.$materiel->getIduser()->getNom() : 'NULL';
                        $statut = null != $materiel->getIdstatut() ? $materiel->getIdstatut()->getLibellestatut() : 'NULL';
                        $data = array($materiel->getIdmateriel(),
                            $materiel->getNumeroserie(),
                            $statut,
                            $materiel->getNommateriel(),
                            $marque,
                            $lieu,
                            $type,
                            $speci,
                            $user,
                            $materiel->getSupprimer(),
                        );
                        fputcsv($fp,$data,';','"');
                    }
                    fclose($fp);

                    /*
                     * Modification de la base
                     */
                    $csv = fopen($_FILES['csvFile']['tmp_name'],'r');
                    while (!feof($csv)){
                        $line[]= fgetcsv($csv,1024,';');
                    }
                    fclose($csv);
                    $verif = false ;
                    foreach ($line as $data){
                        if ($verif){
                            $marque = $em->getRepository(Marque::class)->findOneBy(array('libellemarque'=>$data[4]));
                            $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>$data[5]));
                            $type = $em->getRepository(Type::class)->findOneBy(array('libelletype'=>$data[6]));
                            $specificite = $em->getRepository(Specificite::class)->findOneBy(array('libellespe'=>$data[7]));
                            $statut = $em->getRepository(Statut::class)->findOneBy(array('libellestatut'=>$data[2]));
                            $idRef= explode("-",$data[8])[0];
                            $user = $em->getRepository(User::class)->findOneBy(array('iduser'=>$idRef));
                            $materiel = $em->getRepository(Materiel::class)->findOneBy(array('numeroserie'=>$data[1]));
                            if ($materiel==null){
                                $materiel = new Materiel();
                            }
                            $materiel->setNumeroserie($data[1]);
                            $materiel->setIdstatut($statut);
                            $materiel->setNommateriel($data[3]);
                            $materiel->setIdmarque($marque);
                            $materiel->setIdlieu($lieu);
                            $materiel->setIdtype($type);
                            $materiel->setIdspecificite($specificite);
                            $materiel->setIduser($user);
                            $materiel->setSupprimer($data[9]);
                            $em->persist($materiel);
                            $em->flush();
                        }
                        $verif = true ;
                    }
                    return $this->redirectToRoute('Materiel.index');
                }
            }
            // @codeCoverageIgnoreEnd
        }
        $contenus = '<h1>COUCOU</h1>';
        return $this->render('stock/lirecsv.html.twig',[
            'contenus' => $contenus,
        ]);
    }

    /**
     * @Route("stock/importCSV",name="Materiel.importCSV",methods={"GET","POST"})
     * Rajoute du materiel via un CSV sans modifier ceux déjà existant
     */
    public function importCSV(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {
            // @codeCoverageIgnoreStart
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
                            $statut = $em->getRepository(Statut::class)->findOneBy(array('libellestatut'=>$data[2]));
                            $marque = $em->getRepository(Marque::class)->findOneBy(array('libellemarque'=>$data[4]));
                            $lieu = $em->getRepository(Lieu::class)->findOneBy(array('libellelieu'=>$data[5]));
                            $type = $em->getRepository(Type::class)->findOneBy(array('libelletype'=>$data[6]));
                            $specificite = $em->getRepository(Specificite::class)->findOneBy(array('libellespe'=>$data[7]));
                            $idRef= explode("-",$data[8])[0];
                            $user = $em->getRepository(User::class)->findOneBy(array('iduser'=>$idRef));
                            $materiel = $em->getRepository(Materiel::class)->findOneBy(array('numeroserie'=>$data[1]));
                            if ($materiel==null){
                                $materiel = new Materiel();
                                $materiel->setNumeroserie($data[1]);
                                $materiel->setIdstatut($statut);
                                $materiel->setNommateriel($data[3]);
                                $materiel->setIdmarque($marque);
                                $materiel->setIdlieu($lieu);
                                $materiel->setIdtype($type);
                                $materiel->setIdspecificite($specificite);
                                $materiel->setIduser($user);
                                $materiel->setSupprimer($data[9]);
                                $em->persist($materiel);
                                $em->flush();
                            }
                        }
                        $verif = true ;
                    }
                    return $this->redirectToRoute('Materiel.index');
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $this->render('stock/importCSV.html.twig');
    }

    /**
     * @Route("stock/deleteMultiple",name="Materiel.deleteMultiple",methods={"GET","POST"})
     */
    public function deleteMultiple(){
        $keys = array_keys($_POST);
        $materiels = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($keys as $key){
            $key = trim($key,'materiel');
            $materiel = $em->getRepository(Materiel::class)->find($key);
            array_push($materiels,$materiel);
        }
        return $this->render('stock/deleteMultiple.html.twig',[
            'materiels' => $materiels
        ]);
    }

    /**
     * @Route("stock/validateDeleteMultiple",name="Materiel.validateDeleteMultiple",methods={"GET","POST"})
     */
    public function validateDeleteMultiple(){
        $em = $this->getDoctrine()->getManager();
        foreach ($_POST as $id){
            $materiel = $em->getRepository(Materiel::class)->find($id);
            $date = new \DateTime();
            $materiel->setSupprimer($date->format('d/m/Y'));
            $em->persist($materiel);
            $em->flush();
        }
        return $this->redirectToRoute('Materiel.index');
    }
}