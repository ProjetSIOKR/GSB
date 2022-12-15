<?php
date_default_timezone_set('Europe/Paris');
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
        
        $tempsCompteBloque = 60;
        if($securisationUtilisateurBloque['bloque'] == 1){
            $tempsEnBDD = strtotime($securisationUtilisateur['date']);
            $tempsEnBDDFormatTime = date('H:i:s',$tempsEnBDD+$tempsCompteBloque);
            $tempsMachine = date('H:i:s',time());

            if($tempsMachine>=$tempsEnBDDFormatTime){
                $pdo->updateTentativeMDP_A2F($id);
                Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_connexion.php';
            }else{  
                $tempsRestant =  $tempsEnBDD - time();
                Utilitaires::ajouterErreur("Le temps restant avant de débloquer le compte est de " . getdate($tempsRestant)['seconds'] . " secondes");
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_connexion.php';
            }
           
        }else{
             if (!password_verify($mdp, $pdo->getMdpUtilisateur($login))) {
                    if(!empty($securisationUtilisateur)){
                        
                       /* var_dump("Une tentative de connexion sur l'utilisateur : " . $login . " d'une machine aillant l'adresse IP : " . 
                                filter_input(INPUT_SERVER, 'REMOTE_ADDR'));*/
                        
                      if($securisationUtilisateur['tentative_mdp_id'] == 2){
                          $pdo->updateTentativeBloque($id);
                                Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant " . $tempsCompteBloque . " secondes");
                    }else{
                        Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                         $pdo->updateTentativeMDP($id);
                    }
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
        break; 
        
    case 'valideA2fConnexion':
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = $_SESSION['idutilisateur'];
        $securisationUtilisateur = $pdo->getInfosSecurisationConnexion($id);
        $securisationUtilisateurBloque = $pdo->getInfosSecurisationConnexionBloque($id);
        $tempsCompteBloque = 60;
        
        if($securisationUtilisateurBloque['bloque'] == 1){
            $tempsEnBDD = strtotime($securisationUtilisateur['date']);
            $tempsEnBDDFormatTime = date('H:i:s',$tempsEnBDD+$tempsCompteBloque);
            $tempsMachine = date('H:i:s',time());

            if($tempsMachine>=$tempsEnBDDFormatTime){
                $pdo->updateTentativeMDP_A2F($id);
                Utilitaires::ajouterErreur("Votre compte est débloqué, vous pouvez réessayer !");
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS .'v_code2facteurs.php';
            }else{  
                $tempsRestant =  $tempsEnBDD - time();
                Utilitaires::ajouterErreur("Le temps restant avant de débloquer le compte est de " . getdate($tempsRestant)['seconds'] . " secondes");
                include PATH_VIEWS . 'v_erreurs.php';
                 include PATH_VIEWS .'v_code2facteurs.php';
            }
        }else{
            if ($pdo->getCodeUtilisateur($_SESSION['idutilisateur']) !== $code) {
                    if(!empty($securisationUtilisateur)){
                      if($securisationUtilisateur['tentative_a2f'] == 2){
                          $pdo->updateTentativeBloque($id);
                                Utilitaires::ajouterErreur("Derniere tentative avant que votre compte soit bloqué pendant " . $tempsCompteBloque . " secondes");
                                 
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
               
        }else {
            Utilitaires::connecterA2f($code);
            header('Location: index.php');
         }
        }
        break;
    default:
        include PATH_VIEWS . 'v_connexion.php';
        break;
}


