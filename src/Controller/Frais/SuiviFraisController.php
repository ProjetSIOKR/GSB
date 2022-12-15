<?php
namespace App\Controller\Frais;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;

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
        $uri= Utilitaires::getUri();
        $pdo=PdoGsb::getPdoGsb();
        $role= Utilitaires::getRole();
        $totalFraisForfait = $pdo->getTotauxFicheFraisForfait($id, $mois);
        $totalFraisHorsForfait = $pdo->getTotauxFicheFraisHorsForfait($id, $mois);
        $utilisateur = $pdo->getUtilisateurFicheFrais($id);
        MyTwig::afficheVue('FraisView/suivipaiementajax.html.twig',array(
            'role'=>$role,
            'uri'=>$uri,
            'connecte'=>Utilitaires::estConnecte(),
            'totalFraisForfait'=>$totalFraisForfait,
            'totalFraisHorsForfait'=>$totalFraisHorsForfait,
            'utilisateur'=>$utilisateur
        ));
    }
}