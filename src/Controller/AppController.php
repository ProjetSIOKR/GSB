<?php
namespace App\Controller;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;


class AppController
{
    #[Route('/accueil', name: 'accueil')]
    public function index() : void
    {
        $role = Utilitaires::getRole();
        $nom = Utilitaires::getNom();
        $prenom = Utilitaires::getPrenom();
        $uri = Utilitaires::getUri();
        MyTwig::afficheVue('AppView/accueil.html.twig', array('prenom' => $prenom,
            'nom' => $nom,
            'role'=>$role,
            'uri'=>$uri,
            'connecte'=>Utilitaires::estConnecte()));
    }
}