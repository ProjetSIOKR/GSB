<?php
namespace App\Controller\Frais;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class SuiviFraisController
{
    #[Route('/suivipaiement', name: 'app_suivi_paiement')]
    public function suivrePaiement() : void
    {
        $uri= Utilitaires::getUri();
        $pdo=PdoGsb::getPdoGsb();
        $role= Utilitaires::getRole();
        $fichesValides = $pdo->getFichesValidees();
        MyTwig::afficheVue('FraisView/suivipaiement.html.twig', array(
            "fichesValidees"=>$fichesValides,
            'connecte'=>Utilitaires::estConnecte(),
            'role'=>$role,
            'uri'=>$uri,
        ));
    }

    #[Route('/suivipaiement/ajax', methods: ['POST'], name: 'app_suivi_paiement_ajax')]
    public function suivrePaiementAjax(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pdo=PdoGsb::getPdoGsb();
        $totalFraisForfait = $pdo->getTotauxFicheFraisForfait($id, $mois);
        $totalFraisHorsForfait = $pdo->getTotauxFicheFraisHorsForfait($id, $mois);
        $utilisateur = $pdo->getUtilisateurFicheFraisValidee($id);
        MyTwig::afficheVue('FraisView/suivipaiementajax.html.twig',array(
            'totalFraisForfait'=>$totalFraisForfait,
            'totalFraisHorsForfait'=>$totalFraisHorsForfait,
            'utilisateur'=>$utilisateur
        ));
    }

    #[Route('/misenpaiement', methods: ['POST'], name: 'app_mise_en_paiement')]
    public function miseEnPaiement() : void
    {
        $idVisiteurMois = json_decode(stripslashes($_POST['idVisiteurMois']));
        $pdo=PdoGsb::getPdoGsb();
        for ($i=0; count($idVisiteurMois) ; $i++) {
            $pdo->setFicheFraisMiseEnPaiement($idVisiteurMois[$i]);
        }
        $uri= Utilitaires::getUri();
        $role= Utilitaires::getRole();
        $fichesValides = $pdo->getFichesValidees();
        MyTwig::afficheVue('FraisView/suivipaiement.html.twig', array(
            "fichesValidees"=>$fichesValides,
            'connecte'=>Utilitaires::estConnecte(),
            'role'=>$role,
            'uri'=>$uri,
        ));
    }
}