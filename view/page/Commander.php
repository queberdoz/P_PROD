<?php

include_once "model/Database.php";
include_once "controller/HomeController.php";

$database = new Database();
$controller = new HomeController();

//todo choix de tables mis en commentaires pour le moment, à rétablir si besoin est
if (!array_key_exists('username', $_SESSION)) {
    header("Location: index.php?controller=home&action=Connexion");
    exit();
} else {

    echo '
    

<form action="#" method="post">
    <div class="container">

    <h3>Commander un plat</h3>
    <div class="ligne"></div>';

        if (isset($commandErrors)) {
            if (count($commandErrors) > 0) {
        ?>
                <div class="alert alert-danger mt-5">
                    Oups ... Nous avons rencontré quelques erreurs :<br>
                    <ul class="mb-0">
        <?php
                    foreach ($commandErrors as $error) {
                        echo "<li>$error</li>";
                    }
        ?>
                    </ul>
                </div>
        <?php
            }
        }

        ?>
        <div class="form-group mt-4">
            <label>Date de la réservation : </label>
            <input class="form-control" type="date" name="resDate" value="<?php if (isset($_POST['resDate'])) { echo $_POST['resDate']; } else { echo date('Y-m-d', strtotime("+1 days")); } ?>">
        </div>
            <div class="form-group">
            <label>Heure de la réservation</label>
            <select class="form-control" name="resHour">
                <option value="Choose">Choisir</option>
                <option value="11" <?php if (isset($_POST['resHour'])) { if ($_POST['resHour'] == "11") { echo "selected"; } }?>>11h20-12h</option>
                <option value="12" <?php if (isset($_POST['resHour'])) { if ($_POST['resHour'] == "12") { echo "selected"; } }?>>12h10-12h50</option>
            </select>
        </div>
        <div class="form-group">
            <label>Plat choisi : </label>
            <select class="form-control" name="resMeal">
                <option value="0">Choisir</option>
                <?php
                $meals = $database->getCurrentMeals();
                foreach ($meals as $meal)
                {
                    if ($meal['meaName'] != "-") {
                ?>
                        <option value="<?= $meal["idMeal"] ?>" <?php if (isset($_POST['resMeal'])) { if ($_POST['resMeal'] == $meal['idMeal']) { echo "selected"; } }?>><?= $meal["meaName"] ?></option>
                <?php
                    }
                }
                ?>
            </select>
        </div>
        <input class="btn btn-primary mt-4 mb-4" name="submitBtn" type="submit">
        </div>
        <?php
            //Recherche des reservation de l'utilisateur.
            $result=$database->readReservationUser($_SESSION['username']);
        ?>

        <!-- tableau de plats commandé -->
        <div class="container">
            <h3>Plats commandé</h3>
            <div class="ligne"></div>
            <?php
                if(count($result) != 0){
            ?>
            <table class="table">
            <thead>
                <tr>
                <th scope="col">Date</th>
                <th scope="col">Heure de la réservation</th>
                <th scope="col">Plat choisi</th>
                <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php

            
                for($y=0; $y < count($result); $y++){
                    $newDate = date("d.m.Y", strtotime($result[$y]['resDate']));
                    $meaname=$result[$y]["meaName"];
                    echo("<tr>");
                        
                    //date
                    echo"<td>$newDate</td>";
    
                    //Heur réservée
                    if($result[$y]["resHour"] == 11){
                        echo("<td>11h20 - 12h</td>");
                    }
                    elseif($result[$y]["resHour"] == 12){
                        echo("<td>12h10 - 12h50</td>");
                    }
                    else{
                        echo("<td>... - ...</td>");
                    }
                    
                    //Nom du plat
                    echo("<td>$meaname</td>");
    
                    //Action
                    echo("<td>");
    
    
    
                    echo("<form method='post' action='#'>");
                    //<!-- <a onclick="modifieorder($result[$y]['idReservation']);"><i class="fas fa-edit"></i></a>' -->
                    if($newDate != date("d.m.Y")){
                        echo('<a href="?controller=home&action=Commander&Delete=' .$result[$y]["idReservation"]. '"><i class="fas fa-trash-alt"></i></a>');
                    }
                    
                    ?>
                    <?php
                    echo("</form>");
                    ?>
                    <div id='hello'></div>
                    <?php
                    echo("</td>");
                    echo("</tr>");
                }
            }
            else{
                echo("<h3 style='height: 100px;'>Aucun plat commandé</h3>");
            }
            
            ?>
            </tbody>
            </table>

    </div>
</form>
    <?php
    //Permet de 
    if(isset($_GET['Delete'])){
        $controller->VerifieDeleteOrder($_GET['Delete']);
        header('Location: index.php?controller=home&action=Commander');
    }
}


if (array_key_exists('CommandDone', $_SESSION) && $_SESSION['CommandDone']) {
?>
    <!-- Modal -->
    <div class="modal" tabindex="-1" role="dialog" id="myModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Commande réalisée</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="mb-2"> Commande suivante bien effectuée :<br>
            <?php
            echo 'Date : ' . date('d.m.Y', strtotime($_SESSION['CommandTemp']['resDate'])) . '<br>Heure : ';
            switch ($_SESSION['CommandTemp']['resHour']) {
                case 11:
                    echo "11h20 à 12h00";
                    break;
                case 12:
                    echo '12h10 à 12h50';
                    break;
                default:
                    echo 'Heure non reconnue';
                    break;
            }
            echo '<br>Plat : ';
            echo $database->getMeal($_SESSION['CommandTemp']['resMeal'])['meaName'];
            ?></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>
    
    <script>
        $('#myModal').modal('show');
    </script>

    <?php
    unset($_SESSION['CommandDone']);
    unset($_SESSION['CommandTemp']);

}
?>