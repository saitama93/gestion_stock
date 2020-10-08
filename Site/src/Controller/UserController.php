<?php

namespace App\Controller;

use Mpdf\Mpdf;
use App\Entity\User;
use App\Form\UserType;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="User.index",methods={"GET"})
     * Permet d'afficher tous les utilisateurs
     */
    public function index(Request $request){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findBy(array('present' => 1));
        return $this->render('user/index.html.twig',[
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/add",name="User.add",methods={"GET","POST"})
     * Permet de créer un utilisateur
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $message = '';
        $user = new User();
       
       $form =  $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()){
                ini_set("SMTP","relais-exchange.doubs.fr");
                try {
                    $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user,$plainPasswd);
                    $user->setPassword($password);
                    switch ($_POST['roles']){
                        case 0:
                            $user->setRoles(array());
                            break;
                        case 1 :
                            $user->setRoles(array('ROLE_ADMIN'));
                            break;
                        case 2 :
                            $user->setRoles(array('ROLE_PUBLIC'));
                            break;
                        default:
                            $user->setRoles(array());
                            break;
                    }
                    $user->setPresent(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Compte créé.');
                    $message = 'Voici vos informations utilisateurs afin d\'accéder à l\'application';
                    $mpdf = new Mpdf();
                    $contenus = '<h1>Informations de l\'utilisateur</h1>'
                        . '<p>Nom : '.$user->getNom().'</p>'
                        . '<p>Prenom : '.$user->getPrenom().'</p>'
                        . '<p>Login : '.$user->getUsername().'</p>'
                        . '<p>Mot de passe : '.$user->getPlainPassword().'</p>';
                    $mpdf->WriteHTML(utf8_encode($contenus));
                    $pdf = $mpdf->Output('User_info.pdf','S');
                    $email = new PHPMailer();
                    $email->setFrom('stockA2SR@doubs.fr'); //@todo modifier mail départ
                    $email->Subject=utf8_decode('User créé');
                    $email->Body=utf8_decode($message);
                    if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test'){
                        $email->addAddress('igalilmi32-148be1@inbox.mailtrap.io');
                    } else {
                        $email->addAddress($user->getMail());
                    }
                    $email->addStringAttachment($pdf,'User_info.pdf');

                    $email->send();
                    return $this->redirectToRoute('User.index');
                } catch (UniqueConstraintViolationException $exception){
                    $message = 'Cet utilisateur existe déjà !';
                }
            }

            if($form->isSubmitted()){
                $this->addFlash('success', 'Utilisateur créé.');
            }

        }
        return $this->render('user/addUser.html.twig',[
            'message'=>$message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/edit/{id}",name="User.edit",methods={"GET","POST"})
     * Permet de modifier les utilisateurs
     */
    public function edit(Request $request, $id,UserPasswordEncoderInterface $passwordEncoder){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $check = '';
        $message = '';
        $form =  $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()){
                ini_set("SMTP","relais-exchange.doubs.fr");
                try {
                    $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user,$plainPasswd);
                    $user->setPassword($password);
                    if ($user->getIduser() != 1 && $user->getIduser() != 2){
                        switch ($_POST['roles']){
                            case 0:
                                $user->setRoles(array());
                                break;
                            case 1 :
                                $user->setRoles(array('ROLE_ADMIN'));
                                break;
                            case 2 :
                                $user->setRoles(array('ROLE_PUBLIC'));
                                break;
                            default:
                                $user->setRoles(array());
                                break;
                        }
                    }
                    $user->setPresent(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Informations modifiées.');

                    $message = 'Voici vos informations utilisateurs afin d\'accéder à l\'application';
                    $mpdf = new Mpdf();
                    $contenus = '<h1>Informations de l\'utilisateur</h1>'
                        . '<p>Nom : '.$user->getNom().'</p>'
                        . '<p>Prenom : '.$user->getPrenom().'</p>'
                        . '<p>Login : '.$user->getUsername().'</p>'
                        . '<p>Mot de passe : '.$user->getPlainPassword().'</p>';
                    $mpdf->WriteHTML(utf8_encode($contenus));
                    $pdf = $mpdf->Output('User_info.pdf','S');
                    $email = new PHPMailer();
                    $email->setFrom('stockA2SR@doubs.fr'); //@todo modifier mail départ
                    $email->Subject=utf8_decode('User modifié');
                    $email->Body=utf8_decode($message);
                    if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test'){
                        $email->addAddress('igalilmi32-148be1@inbox.mailtrap.io');
                    } else {
                        $email->addAddress($user->getMail());
                    }
                    $email->addStringAttachment($pdf,'User_info.pdf');

                    $email->send();
                    return $this->redirectToRoute('User.index');
                } catch (UniqueConstraintViolationException $exception){
                    $message = 'Cet utilisateur existe déjà !';
                }
            }
        }
        return $this->render('user/editUser.html.twig',[
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
    public function delete(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($id == 1 || $id == 2){
            return $this->redirectToRoute('User.index');
        }
        if ($request->isMethod('POST')){
            $user->setPresent(0);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('User.index');
        }
        return $this->render('user/deleteUser.html.twig',
            [
                'user' => $user
            ]);
    }

    /**
     * @Route("user/exportCsv",name="User.exportCsv",methods={"GET","POST"})
     * Permet d'exporter tous les utilisateurs sous format CSV
     */
    public function exportCsv(){
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        $chemin='./csv/user.csv';
        $fp = fopen($chemin,'w');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp,array(
            'ID',
            'Nom',
            'Prenom',
            'Mail',
            'Roles'
        ),';','"');
        foreach ($users as $user){
            if (sizeof($user->getRoles())==2) $roles = 'Admin';
            else $roles = 'User';
            $data = array(
                $user->getIduser(),
                $user->getNom(),
                $user->getPrenom(),
                $user->getMail(),
                $roles,
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
     * @Route("/user/downloadModel",name="User.downloadModel",methods={"GET"})
     * Permet de télécharger le modèle du CSV
     */
    public function downloadModel(){
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        $chemin='./csv/user.csv';

        $fp = fopen($chemin,'w');
        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp,array(
            'ID',
            'Nom',
            'Prenom',
            'Mail',
            'Roles'
        ),';','"');

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
     * @Route("user/importCSV",name="User.importCSV",methods={"GET","POST"})
     * Permet de rajouter de nouveau User via un import d'un CSV
     */
    public function importCsv(Request $request){
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
                            $user = $em->getRepository(User::class)->findOneBy(array('iduser'=>$data[0]));
                            if ($user==null){
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
                        $verif = true ;
                    }
                    return $this->redirectToRoute('User.index');
                }
            }
            // @codeCoverageIgnoreEnd
        }
        return $this->render('user/importCSV.html.twig');
    }
}