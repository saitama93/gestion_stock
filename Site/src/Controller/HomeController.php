<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\Materiel;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * Accueil du site
     * 
     * @Route("/",name="Home.index",methods={"GET","POST"})
     * 
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {

        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();

            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render('admin/home.html.twig', [
                    'role' => $roles
                ]);
            } else {
                return $this->render('home/home.html.twig', [
                    'role' => $roles
                ]);
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/home.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/goBack/{lib}/{id}",name="Home.goBack",methods={"GET","POST"})
     * Permet le retour en arrière sur toute les pages
     */
    public function goBack($lib, $id)
    {
        return $this->render('goBack.html.twig', [
            'lib' => $lib,
            'id' => $id,
        ]);
    }

    /**
     * @Route("/exportAll",name="Home.exportAll",methods={"GET","POST"})
     * Permet d'exporter tous les CSV
     */
    public function exportAll()
    {
        $repository = $this->getDoctrine()->getRepository(Materiel::class);
        $materiels = $repository->findAll();
        $chemin1 = './csv/materiel.csv';

        $fp = fopen($chemin1, 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, array(
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
        ), ';', '"');
        foreach ($materiels as $materiel) {
            $marque = null != $materiel->getIdMarque() ? $materiel->getIdmarque()->getLibellemarque() : 'NULL';
            $lieu = null != $materiel->getIdlieu() ? $materiel->getIdlieu()->getLibellelieu() : 'NULL';
            $type = null != $materiel->getIdtype() ? $materiel->getIdtype()->getLibelletype() : 'NULL';
            $speci = null != $materiel->getIdspecificite() ? $materiel->getIdspecificite()->getLibellespe() : 'NULL';
            $user = null != $materiel->getIduser() ?
                $materiel->getIduser()->getIduser() . '-' . $materiel->getIduser()->getNom() : 'NULL';
            $statut = null != $materiel->getIdstatut() ? $materiel->getIdstatut()->getLibellestatut() : 'NULL';
            $data = array(
                $materiel->getIdmateriel(),
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
            fputcsv($fp, $data, ';', '"');
        }
        fclose($fp);


        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        $chemin2 = './csv/user.csv';

        $fp = fopen($chemin2, 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, array(
            'ID',
            'Nom',
            'Prenom',
            'Mail',
            'Roles'
        ), ';', '"');
        foreach ($users as $user) {
            if (sizeof($user->getRoles()) == 2) $roles = 'Admin';
            else $roles = 'User';
            $data = array(
                $user->getIduser(),
                $user->getNom(),
                $user->getPrenom(),
                $user->getMail(),
                $roles,
            );
            fputcsv($fp, $data, ';', '"');
        }
        fclose($fp);

        $repository = $this->getDoctrine()->getRepository(Intervention::class);
        $interventions = $repository->findAll();
        $chemin3 = './csv/historique.csv';

        $fp = fopen($chemin3, 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, array(
            'ID',
            'Intervenant',
            'Date d\'intervention',
            'Type d\'intervention'
        ), ';', '"');
        foreach ($interventions as $intervention) {
            if ($intervention->getStatutinter() != 'En cours') {
                if ($intervention->getStatutinter() == 'Terminé') $statut = 'Intervention classique';
                else if ($intervention->getStatutinter() == 'Finis') $statut = 'Retour de matériel';
                else $statut = 'Inconnus';
                $data = array(
                    $intervention->getIdintervention(),
                    $intervention->getIduser()->getNom() . ' ' . $intervention->getIduser()->getPrenom(),
                    $intervention->getDateintervention(),
                    $statut,
                );
                fputcsv($fp, $data, ';', '"');
            }
        }
        fclose($fp);

        $zip = new \ZipArchive();
        $nom = 'all_data.zip';
        if ($zip->open($nom, \ZipArchive::OVERWRITE)) {
            $zip->addFile($chemin1);
            $zip->addFile($chemin2);
            $zip->addFile($chemin3);
            $zip->close();
        }


        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($nom) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($nom));
        readfile($nom);
        exit;
    }
}
