<?php

/**
 * Vue Liste des frais hors forfait
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


<hr>
<div class="row">
    
    <?php if($etat['idetat'] == 'CR'){
     ?>
<?php

}
else
{
    ?> 
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>  
                    <th class="montant">Montant</th>  
                    <th class="action">&nbsp;</th> 
                </tr>
            </thead>  
            <tbody>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
               // $mois = $unFraisHorsForfait['mois'];
                $montant = $unFraisHorsForfait['montant'];
                $idFrais = $unFraisHorsForfait['id']; ?>  
              <form action="index.php?uc=gererFrais&action=updateFrais&idFrais=<?php echo $idFrais ?>" 
              method="post" role="form">
                <tr>
                    <!--<td><input type="text" name="date"/> <?php/* echo $date */?></td>-->
                    <td><input type="text" name="idFrais" value="<?php echo $idFrais?>" hidden/><input type="text" name="dateFrais" value="<?php echo  $date ?>"/> </td>
                    <td><input type="text" name="libelle" value="<?php echo $libelle ?>"/></td>
                    <td><input type="text" name="montant" value="<?php echo $montant?>"/></td>
                    <td>
                        <a href="index.php?uc=gererFrais&action=supprimerFrais&idFrais=<?php echo $idFrais ?>" 
                           onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');">
                            Supprimer ce frais
                        </a>
                    </td>
                    <td>
                        <button type='submit' onclick="return confirm('Voulez-vous vraiment appliquer les modifications faite sur ce frais?');"> modifier</button>                        
                    </td>
                </tr>
                 </form>
                <?php
            }
            ?>
            </tbody>  
        </table>
    </div>
</div>
<div class="row">
    <h3>Nouvel élément hors forfait</h3>
    <div class="col-md-4">
        <form action="index.php?uc=gererFrais&action=validerCreationFrais" 
              method="post" role="form">
            <div class="form-group">
                <label for="txtDateHF">Date (jj/mm/aaaa): </label>
                <input type="date" id="txtDateHF" name="dateFrais" 
                       class="form-control" id="text">
            </div>
            <div class="form-group">
                <label for="txtLibelleHF">Libellé</label>             
                <input type="text" id="txtLibelleHF" name="libelle" class="form-control" id="text">
            </div> 
            <div class="form-group">
                <label for="txtMontantHF">Montant : </label>
                <div class="input-group">
                    <span class="input-group-addon">€</span>
                    <input type="text" id="txtMontantHF" name="montant" class="form-control" value="">
                </div>
            </div>
            <button class="btn btn-success" type="submit">Ajouter</button>
            <button class="btn btn-danger" type="reset">Effacer</button>
        </form>
    </div>
</div>
<?php 

                } 

                ?>