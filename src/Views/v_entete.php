<?php

/**
 * Vue Entête
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

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="UTF-8">
        <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title> 
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./styles/bootstrap/bootstrap.css" rel="stylesheet">
        <link href="./styles/style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <?php
            $uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($estConnecte) {
                ?>
            <div class="header">
                <div class="row vertical-align">
                    <div class="col-md-4">
                        <h1>
                            <img src="./images/logo.jpg" class="img-responsive" 
                                 alt="Laboratoire Galaxy-Swiss Bourdin" 
                                 title="Laboratoire Galaxy-Swiss Bourdin">
                        </h1>
                    </div>
                     <style>
                         .nav-pills > li.comptableColor > a{
                              color: orange!important;
                              text-decoration: none;
                            }
                        .nav-pills > li.comptableColor > a:hover, .nav-pills > li.comptableColor > a:focus{
                              color: orange!important;
                              background-color: #eee;     
                            }
                       .comptableActive>a,.comptableActive>a:hover,.comptableActive>a:focus{
                              color: white!important;
                              background-color: orange!important;
                            }
                       .orange{
                            color: orange;
                       }
                    </style>
                    <div class="col-md-8">
                        <ul class="nav nav-pills pull-right" role="tablist">
                            <?php if($_SESSION['role'] == 1){
                                ?>
                                <li <?php if (!$uc || $uc == 'accueil') { ?>class="comptableActive"<?php }else{ ?>class="comptableColor" <?php }?>>
                                    <a href="index.php">
                                        Accueil
                                    </a>
                                </li>
                                <li <?php if ($uc == 'gererFrais') { ?>class="comptableActive"<?php }else{ ?>class="comptableColor" <?php }?>>
                                    <a href="index.php?uc=gererFrais&action=validerFrais">
                                        <span class="glyphicon glyphicon-ok"></span>
                                       Valider fiche de frais
                                    </a>
                                </li>
                                <li <?php if ($uc == 'etatFrais') { ?>class="comptableActive"<?php }else{ ?>class="comptableColor" <?php }?>>
                                    <a href="index.php?uc=etatFrais&action=selectionnerMois">
                                        <span class="glyphicon glyphicon-eur"></span>
                                        Suivre le paiement 
                                    </a>
                                </li>
                                <li <?php if ($uc == 'deconnexion') { ?>class="comptableActive"<?php }else{ ?>class="comptableColor" <?php }?>>
                                    <a href="index.php?uc=deconnexion&action=demandeDeconnexion">
                                        Déconnexion
                                    </a>
                                </li>
                            <?php } else {
                                ?>
                                <li <?php if (!$uc || $uc == 'accueil') { ?>class="active" <?php } ?>>
                                    <a href="index.php">
                                        <span class="glyphicon glyphicon-home"></span>
                                        Accueil
                                    </a>
                                </li>
                                <li <?php if ($uc == 'gererFrais') { ?>class="active"<?php } ?>>
                                    <a href="index.php?uc=gererFrais&action=saisirFrais">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                        Renseigner la fiche de frais
                                    </a>
                                </li>
                                <li <?php if($uc == 'etatFrais') { ?>class="active"<?php } ?>>
                                    <a href="index.php?uc=etatFrais&action=selectionnerMois">
                                        <span class="glyphicon glyphicon-list-alt"></span>
                                        Afficher mes fiches de frais
                                    </a>
                                </li>
                                <li 
                                <?php if ($uc == 'deconnexion') { ?>class="active"<?php } ?>>
                                    <a href="index.php?uc=deconnexion&action=demandeDeconnexion">
                                        <span class="glyphicon glyphicon-log-out"></span>
                                        Déconnexion
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
            } else {
                ?>   
                <h1>
                    <img src="./images/logo.jpg"
                         class="img-responsive center-block"
                         alt="Laboratoire Galaxy-Swiss Bourdin"
                         title="Laboratoire Galaxy-Swiss Bourdin">
                </h1>
                <?php
            }
