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
        $lesCles = array_keys($dates);
        $moisASelectionner = $lesCles[0];
        if($role != 1 || !(Utilitaires::estConnecte())){
            header('Location : /');
        }
        MyTwig::afficheVue('FraisView/Valider/validerfichefrais.html.twig', array(
                'role'=>$role,
                'uri'=>$uri,
                'lesMois'=>$dates,
                'lesVisiteurs'=>$lesVisiteurs,
                'moisASelectionner'=>$moisASelectionner,
                'connecte'=>Utilitaires::estConnecte()));
    }

    #[Route('/validerfichefrais/recupererinfos', methods: ['POST'],name: 'app_recuperer_infos_fiche')]
    public function updateFrais() : void
    {
        $pdo=PdoGsb::getPdoGsb();
        $idVisiteur = filter_input(INPUT_POST,'idVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mois= filter_input(INPUT_POST,'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        MyTwig::afficheVue('FraisView/Valider/frais.html.twig', array(
            'lesFrais'=>$lesFraisForfait,
            'LesFraisHorsForfait'=>$lesFraisHorsForfait
        ));
    }

}