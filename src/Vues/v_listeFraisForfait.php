<?php

/**
 * Vue Liste des frais au forfait
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
<div class="row">
    <?php if ($_SESSION['role']==1){
        ?>
            <div class ="form-group row">
                <label for="choixVisiteur" class="col-sm-2 col-form-label">Choisir le visiteur:</label>
                <div class = "col-5">
                    <select>
                        <?php
                        foreach ($lesVisiteurs as $visiteur){
                            $idVisiteur = $visiteur['id'];
                            $nomVisiteur = $visiteur['nom'];
                            $prenomVisiteur = $visiteur['prenom'];
                            $nomCompletVisiteur = $nomVisiteur ." ". $prenomVisiteur;?>
                        <option value="<?php echo $idVisiteur ?>"><?php echo $nomCompletVisiteur ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <select>
                    <?php
                        if(!isset($moisFicheVisiteur)){
                            ?>
                            <option disabled></option>
                    <?php
                        }else{
                            ?>
                            <option value ="<?php echo ?>"
                        <?php
                        }
                    ?>
                    </select>

                    </select>
                </div>
            </div>
        <h2 class="orange">Valider la fiche de frais</h2>
        <h3>Eléments forfaitisés</h3>
        <div class="col-md-4">
            <form method="post"
                  action="index.php?uc=gererFrais&action=validerMajFraisForfait"
                  role="form">
                <fieldset>
                    <?php
                    foreach ($lesFraisForfait as $unFrais) {
                        $idFrais = $unFrais['idfrais'];
                        $libelle = htmlspecialchars($unFrais['libelle']);
                        $quantite = $unFrais['quantite']; ?>
                        <div class="form-group">
                            <label for="idFrais"><?php echo $libelle ?></label>
                            <input type="text" id="idFrais"
                                   name="lesFrais[<?php echo $idFrais ?>]"
                                   size="10" maxlength="5"
                                   value="<?php echo $quantite ?>"
                                   class="form-control">
                        </div>
                        <?php
                    }
                    ?>
                    <button class="btn btn-success" type="submit">Ajouter</button>
                    <button class="btn btn-danger" type="reset">Effacer</button>
                </fieldset>
            </form>
        </div>
    <?php
    }else{
        ?>
    <h2>Renseigner ma fiche de frais du mois
        <?php echo $numMois . '-' . $numAnnee ?>
    </h2>
    <h3>Eléments forfaitisés</h3>
    <div class="col-md-4">
        <form method="post"
              action="index.php?uc=gererFrais&action=validerMajFraisForfait"
              role="form">
            <fieldset>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle ?></label>
                        <input type="text" id="idFrais"
                               name="lesFrais[<?php echo $idFrais ?>]"
                               size="10" maxlength="5"
                               value="<?php echo $quantite ?>"
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <button class="btn btn-success" type="submit">Ajouter</button>
                <button class="btn btn-danger" type="reset">Effacer</button>
            </fieldset>
        </form>
    </div>
    <?php
    }
    ?>
</div>
