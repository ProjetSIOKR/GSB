<?php

/**
 * Gestion de la connexion
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
use Outils\Utilitaires;

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (!$uc) {
    $uc = 'demandeconnexion';
}

switch ($action) {
    case 'demandeConnexion':
        include PATH_VIEWS . 'v_connexion.php';
        break;
    case 'valideConnexion':
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $utilisateur = $pdo->getInfosUtilisateur($login);
        $id = $utilisateur['id'];
        $securisationUtilisateur = $pdo->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = $pdo->getInfosSecurisationConnexionBloque($id);
        if($securisationUtilisateurBloque['bloque'] == 1){
            sleep(10);
            $pdo->updateTentativeMDP_A2F($id);
            Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !"); //Votre compte est bloqué, veuillez attendre 2 minute avant de réessayer
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS . 'v_connexion.php';
        }else{
             if (!password_verify($mdp, $pdo->getMdpUtilisateur($login))) {
                    if(!empty($securisationUtilisateur)){
                        error_log("Une tentative de connexion sur l'utilisateur : " . $login . " d'une machine aillant l'adresse IP : " . 
                                filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
                      if($securisationUtilisateur['tentative_mdp_id'] == 2){
                          $pdo->updateTentativeBloque($id);
                                Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant 2 minutes");
                                 
                                //Votre compte est bloqué, veuillez attendre 1 minute avant de réessayer
                    }else{
                        Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                         $pdo->updateTentativeMDP($id);
                    }
                   // Utilitaires::ajouterErreur('erreur');
                }else{
                    $pdo->insertTentativeMDP($id);
                     Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                }
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_connexion.php';
               
            } else {
                $utilisateur = $pdo->getInfosUtilisateur($login);
                $id = $utilisateur['id'];
                $nom = $utilisateur['nom'];
                $prenom = $utilisateur['prenom'];
                $role = $utilisateur['id_role'];
                Utilitaires::connecter($id, $nom, $prenom, $role);
                $email = $utilisateur['email'];
                $code = rand(1000, 9999);
                $pdo->setCodeA2f($id, $code);
                mail($email, '[GSB-AppliFrais] Code de vérification', "Code : $code");
              
                include  PATH_VIEWS . 'v_code2facteurs.php';
            }
        }
        //réinitialiser toutes les heures pour enlever les essais (car si l'utilisateur à 2 echecs et qu'il réessaye 2j après il sera bloqué lors de la première tentative)
        //DATE_ADD(date, INTERVAL expr type) //DATEDIFF(date1,date2)
        //DATEDIFF(Now(),DATE_ADD(Now(), INTERVAL 60 seconds)
        //str_totime
        // Tache chrone en base de donnée
        //Mettre un timer sur la page et dès que le timeur arrive a 0 faire une modifier la base et mettre a 0
        // sleep() 
        break; 
        
    case 'valideA2fConnexion':
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = $_SESSION['idutilisateur'];
        
        $securisationUtilisateur = $pdo->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = $pdo->getInfosSecurisationConnexionBloque($id);
        if ($pdo->getCodeUtilisateur($_SESSION['idutilisateur']) !== $code) {
         if($securisationUtilisateurBloque['bloque'] == 1){
            sleep(10);
            $pdo->updateTentativeMDP_A2F($id);
            Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !"); //Votre compte est bloqué, veuillez attendre 2 minute avant de réessayer
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS .'v_code2facteurs.php';
        }else{
                    if(!empty($securisationUtilisateur)){
                      if($securisationUtilisateur['tentative_a2f'] == 2){
                          $pdo->updateTentativeBloque($id);
                                Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant 2 minutes");
                                 
                                //Votre compte est bloqué, veuillez attendre 1 minute avant de réessayer
                    }else{
                      Utilitaires::ajouterErreur('Code de vérification incorrect');
                         $pdo->updateTentativeCodeA2f($id);
                    }
                   // Utilitaires::ajouterErreur('erreur');
                }else{
                    $pdo->insertTentativeMDP($id);
                     Utilitaires::ajouterErreur('Code a2f non valide');
                }
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS .'v_code2facteurs.php';
               
        }
        }else {
            Utilitaires::connecterA2f($code);
            header('Location: index.php');
        }
        /*
        else {
            Utilitaires::connecterA2f($code);
            header('Location: index.php');
        }*/
        break;
    default:
        include PATH_VIEWS . 'v_connexion.php';
        break;
}


