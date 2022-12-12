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
        MyTwig::afficheVue('AppView/accueil.html.twig', array('prenom' => $_SESSION['prenom'],
            'nom' => $_SESSION['nom'],
            'role'=>$_SESSION['role'],
            'uri'=>$_SERVER['REQUEST_URI'],
            'connecte'=>Utilitaires::estConnecte()));
    }
}