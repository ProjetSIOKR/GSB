<?php
namespace App\Controller\Frais;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;

class EtatFraisController{

    #[Route('/etatfrais', name: 'app_etat_frais')]
    public function etatfrais():void {
        if(!(Utilitaires::estConnecte())){
            header('Location: /');
        }
        $idVisiteur = $_SESSION['idutilisateur'];
        $lesMois = PdoGsb::getPdoGsb()->getLesMoisDisponibles($idVisiteur);
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];
        MyTwig::afficheVue('EtatFraisView/etatfrais.html.twig',array(
            'role'=>$_SESSION['role'],
            'uri'=>$_SERVER['REQUEST_URI'],
            'connecte'=>Utilitaires::estConnecte(),
            'lesMois'=>$lesMois,
            'moisASelectionner'=>$moisASelectionner,
        ));
    }
    
    #[Route('/voiretatfrais', methods: ['POST'], name: 'app_voir_frais')]
    public function voirEtatFrais(): void {
        $idVisiteur = $_SESSION['idutilisateur'];
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lesMois = PdoGsb::getPdoGsb()->getLesMoisDisponibles($idVisiteur);
        $moisASelectionner = $leMois;
        $lesFraisHorsForfait = PdoGsb::getPdoGsb()->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = PdoGsb::getPdoGsb()->getLesFraisForfait($idVisiteur, $leMois);
        $lesInfosFicheFrais = PdoGsb::getPdoGsb()->getLesInfosFicheFrais($idVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = Utilitaires::dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        MyTwig::afficheVue('EtatFraisView/voiretatfrais.html.twig',array(
            'role'=>$_SESSION['role'],
            'uri'=>$_SERVER['REQUEST_URI'],
            'connecte'=>Utilitaires::estConnecte(),
            'lesMois'=>$lesMois,
            'moisASelectionner'=>$moisASelectionner,
            'lesFraisHorsForfaits'=>$lesFraisHorsForfait,
            'lesFraisForfait'=>$lesFraisForfait,
            'lesInfosFicheFrais'=>$lesInfosFicheFrais,
            'numAnnee'=>$numAnnee,
            'numMois'=>$numMois,
            'libEtat'=>$libEtat,
            'montantValide'=>$montantValide,
            'nbJustificatifs'=>$nbJustificatifs,
            'dateModif'=>$dateModif,
        ));
    }
}