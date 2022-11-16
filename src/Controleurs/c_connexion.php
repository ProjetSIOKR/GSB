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
        if($securisationUtilisateur['bloque'] == 1){
            Utilitaires::ajouterErreur("Votre compte est bloqué, veuillez attendre 1 minute avant de réessayer");
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS . 'v_connexion.php';
        }else{
             if (!password_verify($mdp, $pdo->getMdpUtilisateur($login))) {
                $utilisateur = $pdo->getInfosUtilisateur($login);
                $id = $utilisateur['id'];
               
                $securisationUtilisateur = $pdo->getInfosSecurisationConnexion($id);

                    if(!empty($securisationUtilisateur)){
                    $pdo->updateTentativeMDP($id);
                      if($securisationUtilisateur['tentative_mdp_id']>=5){
                          $pdo->updateTentativeBloque($id);
                    }else{
                        Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
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
                Utilitaires::connecter($id, $nom, $prenom);
                $email = $utilisateur['email'];
                $code = rand(1000, 9999);
                $pdo->setCodeA2f($id, $code);
                mail($email, '[GSB-AppliFrais] Code de vérification', "Code : $code");
                include  PATH_VIEWS . 'v_code2facteurs.php';
            }
        }
            
        
        
        
        
        
        
        
        /*
        for ($tentativeConnexion = 0; $tentativeConnexion <= 5; $tentativeConnexion++) {
             if (!password_verify($mdp, $pdo->getMdpUtilisateur($login))) {
                Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                echo $tentativeConnexion;
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_connexion.php';
            } else {
                $utilisateur = $pdo->getInfosUtilisateur($login);
                $id = $utilisateur['id'];
                $nom = $utilisateur['nom'];
                $prenom = $utilisateur['prenom'];
                $role = $utilisateur['id_role'];
                Utilitaires::connecter($id, $nom, $prenom);
                $email = $utilisateur['email'];
                $code = rand(1000, 9999);
                $pdo->setCodeA2f($id, $code);
                mail($email, '[GSB-AppliFrais] Code de vérification', "Code : $code");
                include  PATH_VIEWS . 'v_code2facteurs.php';
            }
        }*/
         /*
        $tentativeConnexion = 0;
         if ($tentativeConnexion <= 5) {
             if (!password_verify($mdp, $pdo->getMdpUtilisateur($login))) {
                Utilitaires::ajouterErreur('Login ou mot de passe incorrect');
                $tentativeConnexion++;
                include PATH_VIEWS . 'v_erreurs.php';
                include PATH_VIEWS . 'v_connexion.php';
               echo $tentativeConnexion;
            } else {
                $utilisateur = $pdo->getInfosUtilisateur($login);
                $id = $utilisateur['id'];
                $nom = $utilisateur['nom'];
                $prenom = $utilisateur['prenom'];
                $role = $utilisateur['id_role'];
                Utilitaires::connecter($id, $nom, $prenom);
                $email = $utilisateur['email'];
                $code = rand(1000, 9999);
                $pdo->setCodeA2f($id, $code);
                mail($email, '[GSB-AppliFrais] Code de vérification', "Code : $code");
                include  PATH_VIEWS . 'v_code2facteurs.php';
            }
            echo $tentativeConnexion;
        }else{
            Utilitaires::deconnecter($id, $nom, $prenom);
            mail($email, '[GSB-AppliFrais] Multiples tentatives de connexion', " Attention il y a eu 5 tentatives de connexion à votre compte, si ce n'est pas vous veuillez changer votre mot de passe");
        }*/
     
        break;
        //TEST SECURISATION CONNEXION      
    case 'valideA2fConnexion':
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($pdo->getCodeUtilisateur($_SESSION['idutilisateur']) !== $code) {
            Utilitaires::ajouterErreur('Code de vérification incorrect');
            include PATH_VIEWS . 'v_erreurs.php';
            include PATH_VIEWS .'v_code2facteurs.php';
        } else {
            Utilitaires::connecterA2f($code);
            header('Location: index.php');
        }
        break;
    default:
        include PATH_VIEWS . 'v_connexion.php';
        break;
}


