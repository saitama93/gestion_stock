<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Service\CsvService;
use App\Service\MailerService;
use App\Repository\UserRepository;
use App\Service\GeneratePdfService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InterventionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="User.index",methods={"GET"})
     * Permet d'afficher tous les utilisateurs
     */
    public function index(EntityManagerInterface $em, UserRepository $userRepo)
    {
        $users = $userRepo->findBy(array('present' => 1));

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/add",name="User.add",methods={"GET","POST"})
     * Permet de créer un utilisateur
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, EntityManagerInterface $em, GeneratePdfService $pdfService, MailerService $mailerService)
    {
        $message = '';
        $user = new User();

        $form =  $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user, $plainPasswd);
                    $user->setPassword($password);
                    switch ($_POST['roles']) {
                        case 0:
                            $user->setRoles(array());
                            break;
                        case 1:
                            $user->setRoles(array('ROLE_ADMIN'));
                            break;
                        case 2:
                            $user->setRoles(array('ROLE_PUBLIC'));
                            break;
                        default:
                            $user->setRoles(array());
                            break;
                    }
                    $user->setPresent(1);
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Compte créé et un mail vous a été envoyé avec vos identifiants');
                    
                    //Création et envoie de mail    

                    // sendMail prend en parametres:
                    // Le message du mail
                    // L'expéditeur du mail
                    // Le destinataire du mail
                    // L'objet du mail
                    // Le nom de l'expéditeur
                    $mailerService->sendMail(
                        'Voici vos informations utilisateurs afin d\'accéder à l\'application',
                        'rononoa.zoro@mugiwara.fr',
                        'igal.ilmiamir@doubs.fr',
                        'Création de compte',
                        'Zoro'
                    );

                    // GénérationPdfService

                    // On renseigne l'entité relié au PDF
                    $pdfService->setEntityClass(User::class);

                    //download prend en parametre :
                    // le nom du fichier à télécharger
                    // le chemin du template twig
                    // et les paramettre de ce template
                    $pdfService->download('infos_' . $user->getNom(), 'pdf/userInfo.html.twig', [
            
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'pseudo' =>  $user->getUsername(),
                       'plainPassword' => $user->getPlainPassword(),
                        'mail' => $user->getMail()
                    ]);

                    return $this->redirectToRoute('User.index');
            }
            if ($form->isValid() && $form->isSubmitted()) {

                $this->addFlash('success', 'Utilisateur créé.');
            }
        }
        return $this->render('user/addUser.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/edit/{id}",name="User.edit",methods={"GET","POST"})
     * Permet de modifier les utilisateurs
     */
    public function edit(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, UserRepository $userRepo, GeneratePdfService $pdfService, MailerService $mailerService)
    {
        $user = $userRepo->find($id);
        $check = '';
        $message = 'Test MSG';
        $form =  $this->createForm(UserType::class, $user);
        $plainPasswd = $user->getPassword();
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                    $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user, $plainPasswd);
                    $user->setPassword($password);

                    if ($user->getIduser() != 1 && $user->getIduser() != 2) {
                        switch ($_POST['roles']) {
                            case 0:
                                $user->setRoles(array());
                                break;
                            case 1:
                                $user->setRoles(array('ROLE_ADMIN'));
                                break;
                            case 2:
                                $user->setRoles(array('ROLE_PUBLIC'));
                                break;
                            default:
                                $user->setRoles(array());
                                break;
                        }
                    }

                    $user->setPresent(1);
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Informations modifiées.');

                     //Création et envoie de mail    

                    // sendMail prend en parametres:
                    // Le message du mail
                    // L'expéditeur du mail
                    // Le destinataire du mail
                    // L'objet du mail
                    // Le nom de l'expéditeur
                    $mailerService->sendMail(
                        'Voici vos informations utilisateurs afin d\'accéder à l\'application',
                        'rononoa.zoro@mugiwara.fr',
                        'igal.ilmiamir@doubs.fr',
                        'Création de compte',
                        'Zoro'
                    );
                    
                    return $this->redirectToRoute('User.index');
            }
        }
        return $this->render('user/editUser.html.twig', [
            'id' => $id,
            'user' => $user,
            'check' => $check,
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("user/delete/{id}",name="User.delete",methods={"GET","POST"})
     * Permet de supprimer un utilisateur avec page de confirmation
     */
    public function delete(Request $request, $id, UserRepository $userRepo, EntityManagerInterface $em)
    {
    
        $user = $userRepo->find($id);
        $username = $user->getUsername();

        if ($request->isMethod('POST')) {
            $user->setPresent(0);
            $em->remove($user);
            $em->flush();

            $this->addFlash(
                'danger',
                "L'utilisateur {$username} a bien été supprimé."
            );

            return $this->redirectToRoute('User.index');
          
        }
        return $this->render(
            'user/deleteUser.html.twig',
            [
                'user' => $user
            ]
        );
    }

    /**
     * @Route("user/exportCsv",name="User.exportCsv",methods={"GET","POST"})
     * Permet d'exporter tous les utilisateurs sous format CSV
     */
    public function exportCsv(CsvService $csvService)
    {
        $csvService->exportUsers('./csv/user.csv', [
            'ID',
            'Nom',
            'Prenom',
            'Mail',
            'Roles'
        ]);
    }

    /**
     * @Route("/user/downloadModel",name="User.downloadModel",methods={"GET"})
     * Permet de télécharger le modèle du CSV
     */
    public function downloadModel(CsvService $csvService)
    {
        $csvService->importModel();
    }

    /**
     * @Route("user/importCSV",name="User.importCSV",methods={"GET","POST"})
     * Permet de rajouter de nouveau User via un import d'un CSV
     */
    public function importCsv(Request $request, EntityManagerInterface $em)
    {
        if ($request->isMethod('POST')) {
            // @codeCoverageIgnoreStart
            if (isset($_FILES['csvFile'])) {
                $extension = array('.file');
                $file = strrchr($_FILES['csvFile']['name'], '.');
                if (in_array($file, $extension)) {
                    $csv = fopen($_FILES['csvFile']['tmp_name'], 'r');
                    while (!feof($csv)) {
                        $line[] = fgetcsv($csv, 1024, ';');
                    }
                    fclose($csv);
                    $verif = false;
                    foreach ($line as $data) {
                        if ($verif) {
                            $user = $em->getRepository(User::class)->findOneBy(array('iduser' => $data[0]));
                            if ($user == null) {
                                $roles = [];
                                if ($data[4] == 'Admin') $roles = array('ROLE_ADMIN');
                                $user = new User();
                                $user->setNom($data[1]);
                                $user->setPrenom($data[2]);
                                $user->setMail($data[3]);
                                $user->setRoles($roles);
                                $user->setPresent(1);
                                $em->persist($user);
                                $em->flush();
                            }
                        }
                        $verif = true;
                    }
                    return $this->redirectToRoute('User.index');
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $this->render('user/importCSV.html.twig');
    }
}