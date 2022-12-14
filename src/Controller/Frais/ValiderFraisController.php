<?php
namespace App\Controller\Frais;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;

class ValiderFraisController{
    
    #[Route('/validerfichefrais',name: 'app_valider_fiche_frais')]
    public function validerFicheFrais() : void {
        $role= Utilitaires::getRole();
        $lesVisiteurs = PdoGsb::getPdoGsb()->getInfosAllVisiteur();
        if($role != 1 || !(Utilitaires::estConnecte())){
            header('Location : /');
        }
        MyTwig::afficheVue('FraisView/validerfichefrais.html.twig', array(
                'role'=>$role,
                'uri'=>$_SERVER['REQUEST_URI'],
                'lesVisiteurs'=>$lesVisiteurs,
                'connecte'=>Utilitaires::estConnecte()));
    }
    
}