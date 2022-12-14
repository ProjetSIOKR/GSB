<?php
namespace App\Controller\Frais;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;

class GestionFraisController{
    #[Route('/gererfrais', name: 'app_saisir_frais')]
    public function sasirFrais() : void
    {
        if(!(Utilitaires::estConnecte())){
            header('Location: /');
        }
        $idutilisateur = Utilitaires::getId();
        if(isset($_SESSION['erreurs'])){
            $erreurs = $_SESSION['erreurs'];
        }
        $mois = Utilitaires::getMois(date('d/m/Y'));
        $numAnnee = substr($mois, 0, 4);
        $numMois = substr($mois, 4, 2);
        if (PdoGsb::getPdoGsb()->estPremierFraisMois($idutilisateur, $mois)) {
            PdoGsb::getPdoGsb()->creeNouvellesLignesFrais($idutilisateur, $mois);
        }
        $lesFraisHorsForfait = PdoGsb::getPdoGsb()->getLesFraisHorsForfait($idutilisateur, $mois);
        $lesFraisForfait = PdoGsb::getPdoGsb()->getLesFraisForfait($idutilisateur, $mois);
        if(isset($erreurs)){
            MyTwig::afficheVue('FraisView/frais.html.twig', array('annee' => $numAnnee,'mois' => $numMois,
                'erreurs'=>$erreurs,
                'role'=>$_SESSION['role'],
                'uri'=>$_SERVER['REQUEST_URI'],
                'lesFrais'=>$lesFraisForfait,
                'LesFraisHorsForfait'=>$lesFraisHorsForfait,
                'connecte'=>Utilitaires::estConnecte()));
            Utilitaires::supprimerErreurs();
        }else{
            MyTwig::afficheVue('FraisView/frais.html.twig', array('annee' => $numAnnee,'mois' => $numMois,
                'role'=>$_SESSION['role'],
                'uri'=>$_SERVER['REQUEST_URI'],
                'lesFrais'=>$lesFraisForfait,
                'LesFraisHorsForfait'=>$lesFraisHorsForfait,
                'connecte'=>Utilitaires::estConnecte()));
        }
    }
    #[Route('/gererfrais/validermajfraisforfait', methods: ['POST'], name: 'app_valider_maj_frais_forfait')]
    public function validerMajFraisForfait() : void
    {
        $lesFrais = filter_input(INPUT_POST, 'LesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        $idutilisateur = Utilitaires::getId();
        $mois = Utilitaires::getMois(date('d/m/Y'));
        if (Utilitaires::lesQteFraisValides($lesFrais)) {
            PdoGsb::getPdoGsb()->majFraisForfait($idutilisateur, $mois, $lesFrais);
        } else {
            Utilitaires::ajouterErreurSession('Les valeurs des frais doivent être numériques');
        }
        header('Location: /gererfrais');
    }
    
    #[Route('/gererfrais/validercreationfrais', methods: ['POST'],name: 'app_valider_creation_frais')]
    public function validerCreationFrais() : void
    {
        $idutilisateur = Utilitaires::getId();
        $mois = Utilitaires::getMois(date('d/m/Y'));
        $dateFrais = Utilitaires::dateAnglaisVersFrancais(
            filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        );
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        Utilitaires::valideInfosFrais($dateFrais, $libelle, $montant);
        if (Utilitaires::nbErreurs() != 0) {
            header('Location: /gererfrais');
        } else {
            PdoGsb::getPdoGsb()->creeNouveauFraisHorsForfait($idutilisateur, $mois, $libelle, $dateFrais, $montant);
        }
    }

}