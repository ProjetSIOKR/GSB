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
        $uri = Utilitaires::getUri();
        $dateJour = date('d/m/Y');
        $dateFormatter= Utilitaires::getTableauDate($dateJour);
        $dates =Utilitaires::getTableauDateAffichage($dateFormatter);
        $lesVisiteurs = PdoGsb::getPdoGsb()->getInfosAllVisiteur();
        if($role != 1 || !(Utilitaires::estConnecte())){
            header('Location : /');
        }
        MyTwig::afficheVue('FraisView/validerfichefrais.html.twig', array(
                'role'=>$role,
                'uri'=>$uri,
                'lesMois'=>$dates,
                'lesVisiteurs'=>$lesVisiteurs,
                'connecte'=>Utilitaires::estConnecte()));
    }

}