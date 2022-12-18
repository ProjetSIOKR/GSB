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
        Utilitaires::supprimerIdVisiteur();
        $pdo=PdoGsb::getPdoGsb();
        $idVisiteur = filter_input(INPUT_POST,'idVisiteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        Utilitaires::ajouterIdVisiteur($idVisiteur);
        $mois= filter_input(INPUT_POST,'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lesInfos=$pdo->getLesInfosFicheFrais($idVisiteur,$mois);
        if ($lesInfos===false) {
            Utilitaires::ajouterErreur('Pas de Fiche de frais pour ce visiteur pour le mois sélectionné');
            $lesErreurs = $_REQUEST['erreurs'];
            MyTwig::afficheVue('FraisView/Valider/frais.html.twig', array(
                'erreurs'=>$lesErreurs
            ));
        }else{
            if ($lesInfos['idEtat'] !== 'CL') {
                $pdo->majEtatFicheFrais($idVisiteur,$mois,'CL');
            }
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
            $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
            $nbJustificatifs = $lesInfos['nbJustificatifs'];
            MyTwig::afficheVue('FraisView/Valider/frais.html.twig', array(
                'lesFrais'=>$lesFraisForfait,
                'LesFraisHorsForfait'=>$lesFraisHorsForfait,
                'nbJustificatifs'=>$nbJustificatifs
            ));
        }
    }

    #[Route('/corrigerfraisforfait', methods: ['POST'],name: 'app_corriger_frais_forfait')]
    public function corrigerFraisForfait(): void {
        $pdo=PdoGsb::getPdoGsb();
        $lesFrais = json_decode(stripslashes($_POST['tabLesFrais']),true);
        $idVisiteur = Utilitaires::getIdVisiteur();
        $mois = Utilitaires::getMois(date('d/m/Y'));
        if(Utilitaires::lesQteFraisValides($lesFrais)){
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFrais);
        }

    }

}