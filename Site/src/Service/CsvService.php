<?php

namespace App\Service;

use App\Repository\UserRepository;

class CsvService
{

    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function importModel(){
        $chemin = './csv/user.csv';

        $fp = fopen($chemin, 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($fp, array(
            'ID',
            'Nom',
            'Prenom',
            'Mail',
            'Roles'
        ), ';', '"');

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($chemin) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($chemin));
        readfile($chemin);
        exit;
    }

    public function exportUsers(string $templatePath, array $items)
    {

        $users = $this->userRepo->findAll();
        $templatePath;

        $fp = fopen($templatePath, 'w');
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($fp, $items, ';', '"');

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

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($templatePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($templatePath));
        readfile($templatePath);
        exit;
    }
}
