<?php

/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 8
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

namespace Outils;

use Outils\MyTwig;

abstract class Utilitaires {

    /**
     * Teste si un quelconque visiteur est connecté
     *
     * @return vrai ou faux
     */
    public static function estConnecte(): bool {
        return isset($_SESSION['idutilisateur']) && isset($_SESSION['code']);
    }

    /**
     * Ajoute en variable de session le code de l'authentification A2F de l'utilisateur connecté
     * @param $code
     * @return void
     */
    public static function connecterA2f($code) {
        $_SESSION['code'] = $code;
    }

    /**
     * Méthode retournant l'id de l'utilisateur connecté
     * @return int
     */
    public static function getId(): int
    {
        return $_SESSION['idutilisateur'];
    }

    /**
     * Ajoute l'id du visiteur selectionné dans la variable de session (concerne le comptable)
     * @param $id id du visiteur
     * @return void
     */
    public static function ajouterIdVisiteur($id): void
    {
        $_SESSION['idselect']= intval($id);
    }

    /**
     * Retourne l'id du visiteur enregistré dans la variable de session (concerne le comptable)
     * @return int id du visiteur enregistré dans la variable de session
     */
    public static function getIdVisiteur() : int
    {
        return $_SESSION['idselect'];
    }

    /**
     * Supprime l'id du visiteur enregistré dans la variable de session
     * @return void
     */
    public static function supprimerIdVisiteur() : void
    {
        if(isset($_SESSION['idselect'])){
            $_SESSION['idselect']=[];
        }
    }

    /**
     * Méthode retournant l'uri de la page courante
     * @return string
     */
    public static function getUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Méthoide retournant le role de l'utilisateur connecté
     * @return int
     */
    public static function getRole(): int
    {
        return $_SESSION['role'];
    }

    /**
     * Méthode retournant le nom de l'utilisateur connecté
     * @return string
     */
    public static function getNom(): string
    {
        return $_SESSION['nom'];
    }

    /**
     * Méthode retournant le prénom de l'utilisateur connecté
     * @return string
     */
    public static function getPrenom(): string
    {
        return $_SESSION['nom'];
    }

    /**
     * Enregistre dans une variable session les infos d'un visiteur
     *
     * @param String $idUtilisateur ID du visiteur
     * @param String $nom        Nom du visiteur
     * @param String $prenom     Prénom du visiteur
     *
     * @return null
     */
    public static function connecter($idUtilisateur, $nom, $prenom, $role): void {
        $_SESSION['idutilisateur'] = $idUtilisateur;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['role'] = $role;
    }

    /**
     * Détruit la session active
     *
     * @return null
     */
    public static function deconnecter(): void {
        session_destroy();
    }

    /**
     * Transforme une date au format français jj/mm/aaaa vers le format anglais
     * aaaa-mm-jj
     *
     * @param String $maDate au format  jj/mm/aaaa
     *
     * @return Date au format anglais aaaa-mm-jj
     */
    public static function dateFrancaisVersAnglais($maDate): string {
        @list($jour, $mois, $annee) = explode('/', $maDate);
        return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
    }

    /**
     * Transforme une date au format format anglais aaaa-mm-jj vers le format
     * français jj/mm/aaaa
     *
     * @param String $maDate au format  aaaa-mm-jj
     *
     * @return Date au format format français jj/mm/aaaa
     */
    public static function dateAnglaisVersFrancais($maDate): string {
        @list($annee, $mois, $jour) = explode('-', $maDate);
        $date = $jour . '/' . $mois . '/' . $annee;
        return $date;
    }

    /**
     * Retourne le mois au format aaaamm selon le jour dans le mois
     *
     * @param String $date au format  jj/mm/aaaa
     *
     * @return String Mois au format aaaamm
     */
    public static function getMois($date): string {
        @list($jour, $mois, $annee) = explode('/', $date);
        unset($jour);
        if (strlen($mois) == 1) {
            $mois = '0' . $mois;
        }
        return $annee . $mois;
    }

    /**
     *Retourne les mois sur 1 an a partir de la date du jour au format aaaamm
     *
     *
     * @return array
     */
    public static function getTableauDate() : array {
        $tabDateFormat = [];
        for ($i=0; $i<= 12 ; $i++){
           $nouvelleDate = date('d/m/Y',strtotime('- '.$i.' months'));
           $tabDateFormat[] = self::getMois($nouvelleDate);
        }
        return $tabDateFormat;
    }

    /**
     *Formatte les dates du tableau en paramètre de aaaamm -> mm/aaaa
     * @param array $date
     *
     * @return array
     */
    public static function getTableauDateAffichage($tabDate) : array {
        $tailleTableau = (count($tabDate)) -1;
        $tabDateAffichage = [];
        for ($i=0; $i<=$tailleTableau ; $i++){
           $anneemois = $tabDate[$i];
           $annee = substr($anneemois,0,4);
           $mois = substr($anneemois,4);
           $date = $mois.'/'.$annee;
           $tabDateAffichage[] = array('date' => $anneemois, 'dateaffichage' => $date);
        }
        return $tabDateAffichage;
    }

    /* gestion des erreurs */

    /**
     * Indique si une valeur est un entier positif ou nul
     *
     * @param Integer $valeur Valeur
     *
     * @return Boolean vrai ou faux
     */
    public static function estEntierPositif($valeur): bool {
        return preg_match('/[^0-9]/', $valeur) == 0;
    }

    /**
     * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
     *
     * @param Array $tabEntiers Un tableau d'entier
     *
     * @return Boolean vrai ou faux
     */
    public static function estTableauEntiers($tabEntiers): bool {
        $boolReturn = true;
        foreach ($tabEntiers as $unEntier) {
            if (!self::estEntierPositif($unEntier)) {
                $boolReturn = false;
            }
        }
        return $boolReturn;
    }

    /**
     * Vérifie si une date est inférieure d'un an à la date actuelle
     *
     * @param String $dateTestee Date à tester
     *
     * @return Boolean vrai ou faux
     */
    public static function estDateDepassee($dateTestee): bool {
        $dateActuelle = date('d/m/Y');
        @list($jour, $mois, $annee) = explode('/', $dateActuelle);
        $annee--;
        $anPasse = $annee . $mois . $jour;
        @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
        return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
    }

    /**
     * Vérifie la validité du format d'une date française jj/mm/aaaa
     *
     * @param String $date Date à tester
     *
     * @return Boolean vrai ou faux
     */
    public static function estDateValide($date): bool {
        $tabDate = explode('/', $date);
        $dateOK = true;
        if (count($tabDate) != 3) {
            $dateOK = false;
        } else {
            if (!self::estTableauEntiers($tabDate)) {
                $dateOK = false;
            } else {
                if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                    $dateOK = false;
                }
            }
        }
        return $dateOK;
    }

    /**
     * Vérifie que le tableau de frais ne contient que des valeurs numériques
     *
     * @param Array $lesFrais Tableau d'entier
     *
     * @return Boolean vrai ou faux
     */
    public static function lesQteFraisValides($lesFrais): bool {
        return self::estTableauEntiers($lesFrais);
    }

    /**
     * Vérifie la validité des trois arguments : la date, le libellé du frais
     * et le montant
     *
     * Des message d'erreurs sont ajoutés au tableau des erreurs
     *
     * @param String $dateFrais Date des frais
     * @param String $libelle   Libellé des frais
     * @param Float  $montant   Montant des frais
     *
     * @return null
     */
    public static function valideInfosFrais($dateFrais, $libelle, $montant): void {
        if ($dateFrais == '') {
            self::ajouterErreurSession('Le champ date ne doit pas être vide');
        } else {
            if (!self::estDatevalide($dateFrais)) {
                self::ajouterErreurSession('Date invalide');
            } else {
                if (self::estDateDepassee($dateFrais)) {
                    self::ajouterErreurSession("date d'enregistrement du frais dépassé, plus de 1 an");
                }
            }
        }
        if ($libelle == '') {
            self::ajouterErreurSession('Le champ description ne peut pas être vide');
        }
        if ($montant == '') {
            self::ajouterErreurSession('Le champ montant ne peut pas être vide');
        } elseif (!is_numeric($montant)) {
            self::ajouterErreurSession('Le champ montant doit être numérique');
        }
    }
    
    /**
     * Ajoute le libellé d'une erreur au tableau des erreurs dans la variable de session
     *
     * @param String $msg Libellé de l'erreur
     *
     * @return null
     */
    public static function ajouterErreur($msg): void {
        if (!isset($_REQUEST['erreurs'])) {
            $_REQUEST['erreurs'] = array();
        }
        $_REQUEST['erreurs'][] = $msg;
    }

    /**
     * Ajoute le libellé d'une erreur au tableau des erreurs dans la variable de session
     *
     * @param String $msg Libellé de l'erreur
     *
     * @return null
     */
    public static function ajouterErreurSession($msg): void {
        if (!isset($_SESSION['erreurs'])) {
            $_SESSION['erreurs'] = array();
        }
        $_SESSION['erreurs'][] = $msg;
    }

    /**
     * Retoune le nombre de lignes du tableau des erreurs
     *
     * @return Integer le nombre d'erreurs
     */
    public static function nbErreurs(): int {
        if (!isset($_SESSION['erreurs'])) {
            return 0;
        } else {
            return count($_SESSION['erreurs']);
        }
    }

    /**
     * Méthode permettant de supprimer les erreurs dans la variable de Session
     *
     */
    public static function supprimerErreurs(): void
    {
        if (isset($_SESSION['erreurs'])) {
            $_SESSION['erreurs'] = [];
        }
    }

    /**
     * Retourne les erreurs enregistrées dans la variable de session
     * @return array
     */
    public static function getErreursSession(): array {
        return ($_SESSION['erreurs']);
    }

    /**
     * Méthode permettant de faire une redirection sois en php si possible sois en JavaScript
     * @param $url
     * @return void
     */
    public static function redirectTo($url): void
    {
        if(!headers_sent()){
            header('Location '.$url);
        }else{
            MyTwig::afficheVue('redirection.html.twig', array('url' => $url));
        }
    }

}
