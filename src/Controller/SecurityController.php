<?php
namespace App\Controller;

use Modeles\PdoGsb;
use Outils\MyTwig;
use Outils\Utilitaires;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController
{
    #[Route('/', name: 'index')]
    public function index() : void
    {
        if(Utilitaires::estConnecte()){
            header('Location: /accueil');
        }
        MyTwig::afficheVue('ConnexionView/connexion.html.twig');
    }

    #[Route('/connexion', methods: ['POST'], name: 'app_connexion')]
    public function connexion(): void
    {
        if(Utilitaires::estConnecte()){
            header('Location: /accueil');
        }
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $utilisateur = PdoGsb::getPdoGsb()->getInfosUtilisateur($login);
        $id = $utilisateur['id'];
        $securisationUtilisateur = PdoGsb::getPdoGsb()->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = PdoGsb::getPdoGsb()->getInfosSecurisationConnexionBloque($id);
        if(!isset($utilisateur) || !(isset($id))){
            Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
            MyTwig::afficheVue('ConnexionView/connexion.html.twig',array('erreurs'=> $_REQUEST['erreurs']));
        }
        if ($securisationUtilisateurBloque['bloque'] == 1) {
            //sleep(10);
            PdoGsb::getPdoGsb()->updateTentativeMDP_A2F($id);
            Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !"); //Votre compte est bloqué, veuillez attendre 2 minute avant de réessayer
        } else {
            if (!password_verify($mdp, PdoGsb::getPdoGsb()->getMdpUtilisateur($login))) {
                if (!empty($securisationUtilisateur)) {
                    if ($securisationUtilisateur['tentative_mdp_id'] == 2) {
                        PdoGsb::getPdoGsb()->updateTentativeBloque($id);
                        Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant 2 minutes");
                        //Votre compte est bloqué, veuillez attendre 1 minute avant de réessayer
                    } else {
                        Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                        PdoGsb::getPdoGsb()->updateTentativeMDP($id);
                    }
                } else {
                    PdoGsb::getPdoGsb()->insertTentativeMDP($id);
                    Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                }
                MyTwig::afficheVue('ConnexionView/connexion.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
            } else {
                $utilisateur = PdoGsb::getPdoGsb()->getInfosUtilisateur($login);
                $id = $utilisateur['id'];
                $nom = $utilisateur['nom'];
                $prenom = $utilisateur['prenom'];
                $role = $utilisateur['id_role'];
                Utilitaires::connecter($id, $nom, $prenom, $role);
                $email = $utilisateur['email'];
                $code = rand(1000, 9999);
                PdoGsb::getPdoGsb()->setCodeA2f($id, $code);
                mail($email, '[GSB-AppliFrais] Code de vérification', "Code : $code");
                MyTwig::afficheVue('ConnexionView/verification.html.twig');
                //Utilitaires::redirectTo('connexion/verfication');
            }
        }
    }
    #[Route('/connexion/verification', methods: ['POST'], name: 'app_verification')]
    public function verification(): void
    {
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = Utilitaires::getId();
        $securisationUtilisateur = PdoGsb::getPdoGsb()->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = PdoGsb::getPdoGsb()->getInfosSecurisationConnexionBloque($id);
        if (PdoGsb::getPdoGsb()->getCodeUtilisateur($id) !== $code) {
            if($securisationUtilisateurBloque['bloque'] == 1){
                PdoGsb::getPdoGsb()->updateTentativeMDP_A2F($id);
                Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
                MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
            }else{
                if(!empty($securisationUtilisateur)){
                    if($securisationUtilisateur['tentative_a2f'] == 2){
                        PdoGsb::getPdoGsb()->updateTentativeBloque($id);
                        Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant 2 minutes");
                    }else{
                        Utilitaires::ajouterErreur('Code de vérification incorrect');
                        PdoGsb::getPdoGsb()->updateTentativeCodeA2f($id);
                    }
                }else{
                    PdoGsb::getPdoGsb()->insertTentativeMDP($id);
                    Utilitaires::ajouterErreur('Code A2F non valide');
                }
                MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
            }
        }else {
            Utilitaires::connecterA2f($code);
            header('Location: /accueil');
        }
    }
    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function deconnexion(): void
    {
        Utilitaires::deconnecter();
        MyTwig::afficheVue('AppView/deconnexion.html.twig', array('url'=>'/'));
    }
}