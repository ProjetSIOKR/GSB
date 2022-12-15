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
        $pdo=PdoGsb::getPdoGsb();
        $role= Utilitaires::getRole();
        $fichesValides = $pdo->getFichesValidees();
        MyTwig::afficheVue('FraisView/suivipaiement.html.twig', array(
            "fichesValidees"=>$fichesValides,
            'connecte'=>Utilitaires::estConnecte(),
            'role'=>$role,
            'uri'=>$_SERVER['REQUEST_URI'],
        ));
    }
}