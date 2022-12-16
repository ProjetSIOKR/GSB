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
        date_default_timezone_set('Europe/Paris');
        if(Utilitaires::estConnecte()){
            header('Location: /accueil');
        }
        $pdo= PdoGsb::getPdoGsb();
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $utilisateur = PdoGsb::getPdoGsb()->getInfosUtilisateur($login);
        $id = $utilisateur['id'];
        $securisationUtilisateur = PdoGsb::getPdoGsb()->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = PdoGsb::getPdoGsb()->getInfosSecurisationConnexionBloque($id);
        $tempsCompteBloque = 60;
        if(!isset($utilisateur) || !(isset($id))){
            Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
            MyTwig::afficheVue('ConnexionView/connexion.html.twig',array('erreurs'=> $_REQUEST['erreurs']));
        }
        if ($securisationUtilisateurBloque['bloque'] == 1) {
            $tempsEnBDD = strtotime($securisationUtilisateur['date']);
            $tempsEnBDDFormatTime = date('H:i:s',$tempsEnBDD+$tempsCompteBloque);
            $tempsMachine = date('H:i:s',time());
            if($tempsMachine>=$tempsEnBDDFormatTime){
                $pdo->updateTentativeMDP_A2F($id);
                Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
                MyTwig::afficheVue('ConnexionView/connexion.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
            }else{
                $tempsRestant= $tempsEnBDD - time();
                Utilitaires::ajouterErreur("Le temps restant avant de débloquer le compte est de " . getdate($tempsRestant)['seconds'] . " secondes");
                MyTwig::afficheVue('ConnexionView/connexion.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
            }
        } else {
            if (!password_verify($mdp, PdoGsb::getPdoGsb()->getMdpUtilisateur($login))) {
                if (!empty($securisationUtilisateur)) {
                    if ($securisationUtilisateur['tentative_mdp_id'] == 2) {
                        $pdo->updateTentativeBloque($id);
                        Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant " . $tempsCompteBloque . " secondes");
                        //Votre compte est bloqué, veuillez attendre 1 minute avant de réessayer
                    } else {
                        Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                        $pdo->updateTentativeMDP($id);
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
        date_default_timezone_set('Europe/Paris');
        $pdo=PdoGsb::getPdoGsb();
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = Utilitaires::getId();
        $securisationUtilisateur = $pdo->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = $pdo->getInfosSecurisationConnexionBloque($id);
        $tempsCompteBloque = 60;
            if($securisationUtilisateurBloque['bloque'] == 1){
                $tempsEnBDD = strtotime($securisationUtilisateur['date']);
                $tempsEnBDDFormatTime = date('H:i:s',$tempsEnBDD+$tempsCompteBloque);
                $tempsMachine = date('H:i:s',time());
//                PdoGsb::getPdoGsb()->updateTentativeMDP_A2F($id);
//                Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
//                MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs'=> $_REQUEST['erreurs']));
                if($tempsMachine>=$tempsEnBDDFormatTime){
                    $pdo->updateTentativeMDP_A2F($id);
                    Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
                    MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs' => $_REQUEST['erreurs']));
                }else{
                    $tempsRestant= $tempsEnBDD - time();
                    Utilitaires::ajouterErreur("Le temps restant avant de débloquer le compte est de " . getdate($tempsRestant)['seconds'] . " secondes");
                   MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs' => $_REQUEST['erreurs']));
                }
            }else {
                if ($pdo->getCodeUtilisateur($id) !== $code) {
                    if (!empty($securisationUtilisateur)) {
                        if ($securisationUtilisateur['tentative_a2f'] == 2) {
                            $pdo->updateTentativeBloque($id);
                            Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant " . $tempsCompteBloque . " secondes");
                        } else {
                            Utilitaires::ajouterErreur('Code de vérification incorrect');
                            $pdo->updateTentativeCodeA2f($id);
                        }
                    } else {
                        $pdo->insertTentativeMDP($id);
                        Utilitaires::ajouterErreur('Code A2F non valide');
                    }
                    MyTwig::afficheVue('ConnexionView/verification.html.twig', array('erreurs' => $_REQUEST['erreurs']));
                } else {
                    Utilitaires::connecterA2f($code);
                    header('Location: /accueil');
                }
            }
    }

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function deconnexion(): void
    {
        Utilitaires::deconnecter();
        MyTwig::afficheVue('AppView/deconnexion.html.twig', array('url'=>'/'));
    }
}