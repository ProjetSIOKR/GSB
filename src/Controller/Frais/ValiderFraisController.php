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
        $lesVisiteurs = PdoGsb::getPdoGsb()->getInfosAllVisiteur();
        $lesMois = PdoGsb::getPdoGsb()->getLesMoisDisponibles($idVisiteur);
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];
        if($role != 1 || !(Utilitaires::estConnecte())){
            header('Location : /');
        }
        MyTwig::afficheVue('FraisView/validerfichefrais.html.twig', array(
                'role'=>$role,
                'uri'=>$uri,
                'lesVisiteurs'=>$lesVisiteurs,
                'connecte'=>Utilitaires::estConnecte()));
    }

}