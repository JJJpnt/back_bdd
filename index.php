<?php 
if(isset($_GET["setbgcolor"]) && !empty($_POST["newbgcolor"])  ) // && isset($_POST['submit']))
{
    // echo "<!--setcookie...-->";
    setcookie('bgcolor', $_POST["newbgcolor"], time() + (365 * 24 * 3600));
    // echo "<!--...setcookie-->";
    header('Location: index.php'); 
}

$cookie_color = (isset($_COOKIE['bgcolor'])) ? $_COOKIE['bgcolor'] : 'slategray';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exo DBB Cinémas</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="background-color:<?php echo $cookie_color ?>">


<?php include("include/connect.php"); ?>

<?php

// echo "BgColor cookie : ".$_COOKIE['bgcolor'];

$action = "index.php";
$editing = false;

if(isset($_GET["edit"]) && !empty($_GET["id"]) && !isset($_POST['submit']))
{
    $editing = true;
    $action="index.php?edit&id=".$_GET["id"];

    $sql = 'SELECT * FROM cinema WHERE id_cinema='.$_GET["id"];
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $cinema_editing = $stmt->fetch(PDO::FETCH_OBJ);
    $stmt->closeCursor(); // Termine le traitement de la requête
}

if(isset($_GET["edit"]) && !empty($_GET["id"]) && isset($_POST['submit'])) {

    $cine_name = trim($_POST['cine_name']);
    $cine_adress = trim($_POST['cine_adress']);
    $cine_town = trim($_POST['cine_town']);
    $cine_mail = trim($_POST['cine_mail']);
    $cine_phone = trim($_POST['cine_phone']);


    $sql = $dbh->prepare ("UPDATE cinema 
                            SET nom_cinema=:nom_cinema, adresse_cinema=:adresse_cinema, ville_cinema=:ville_cinema, mail_cinema=:mail_cinema, telephone_cinema=:telephone_cinema
                            WHERE id_cinema=".$_GET["id"]);

                    $sql->execute(array(
                        'nom_cinema' => $cine_name,
                        'adresse_cinema' => $cine_adress,
                        'ville_cinema' => $cine_town,
                        'mail_cinema' => $cine_mail,
                        'telephone_cinema' => $cine_phone
                    ));
                    $sql-> closeCursor();
                    // header('location:../test.php');
}

if(isset($_GET["add"]) && isset($_POST['submit'])) {

    $cine_name = trim($_POST['cine_name']);
    $cine_adress = trim($_POST['cine_adress']);
    $cine_town = trim($_POST['cine_town']);
    $cine_mail = trim($_POST['cine_mail']);
    $cine_phone = trim($_POST['cine_phone']);


    $sql = $dbh->prepare ("INSERT INTO cinema (nom_cinema,adresse_cinema,ville_cinema,mail_cinema,telephone_cinema)
                    
                        VALUES (:nom_cinema,:adresse_cinema,:ville_cinema,:mail_cinema,:telephone_cinema)");

                    $sql->execute(array(
                        'nom_cinema' => $cine_name,
                        'adresse_cinema' => $cine_adress,
                        'ville_cinema' => $cine_town,
                        'mail_cinema' => $cine_mail,
                        'telephone_cinema' => $cine_phone
                    ));
                    $sql-> closeCursor();
                    // header('location:../test.php');
}

$adding_salle = false;
$editing_salle = false;
$form_salle = "hidden";
if( isset($_GET["add_salle"]) && !empty($_GET["id"]) ) {

    if(!isset($_POST['submit'])) {

        $form_salle = "visible";
        $add_to_id = $_GET["id"];

    } else {

    $adding_salle = true;
    $salle_capa = trim($_POST['salle_capa']);
    $salle_numero = trim($_POST['salle_numero']);

    $sql = $dbh->prepare ("INSERT INTO salle (nom_cinema,adresse_cinema,ville_cinema,mail_cinema,telephone_cinema)
                    
                        VALUES (:nom_cinema,:adresse_cinema,:ville_cinema,:mail_cinema,:telephone_cinema)");

                    $sql->execute(array(
                        'numero_salle' => $numero_salle,
                        'adresse_cinema' => $cine_adress
                    ));
                    $sql-> closeCursor();
                    // header('location:../test.php');
    }
}
?>



<table>
    <tr>
        <th>Id</th><th>Cinéma</th><th>Ville</th><th>Addresse</th><th>Mail</th><th>Téléphone</th><th></th><th></th>
    </tr>
    <?php
    $sql = 'SELECT * FROM cinema';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    while ($donnees = $stmt->fetch(PDO::FETCH_OBJ))
    {
    echo '
        <tr>
            <td>'.$donnees->id_cinema.'</td>
            <td>'.$donnees->nom_cinema.'</td>
            <td>'.$donnees->adresse_cinema.'</td>
            <td>'.$donnees->ville_cinema.'</td>
            <td>'.$donnees->mail_cinema.'</td>
            <td>'.$donnees->telephone_cinema.'</td>
            <td><a href="index.php?edit&id='.$donnees->id_cinema.'">EDIT</a></td>
            <td><a href="index.php?add_salle&id='.$donnees->id_cinema.'">Ajouter Salle</a></td>
        </tr>
    ';
    }
    $stmt->closeCursor(); // Termine le traitement de la requête
    ?>
</table>





<div class="tables_wrapper">
<?php
            $sql_cinema = "SELECT * FROM cinema";
            $stmt_cinema = $dbh->prepare($sql_cinema);
            $stmt_cinema->execute();

            while($cinema = $stmt_cinema->fetch(PDO::FETCH_OBJ)) {
                echo '<div>' . $cinema->nom_cinema . ' :';
                $sql_salle = 'SELECT * FROM salle WHERE id_cinema = ' . $cinema->id_cinema;
                //echo $sql_salle;
                $stmt_salle = $dbh->prepare($sql_salle);
                $stmt_salle->execute();

                echo '
                    <table>
                    <tr>
                        <th>id</th><th>Numéro</th><th>Capacité</th><th>Equipements</th>
                    </tr>
                ';                    
                while($row = $stmt_salle->fetch(PDO::FETCH_OBJ)) {
                    echo '
                    <tr>
                        <td>'.$row->id_salle.'</td>
                        <td>'.$row->numero_salle.'</td>
                        <td>'.$row->capacite_salle.'</td>
                        <td>';

                        $equipement =   $dbh->query("SELECT id_equipement FROM avoir WHERE id_salle=$row->id_salle");
                        while( $y = $equipement->fetchObject() ) {
                            $nom_equipement=$dbh->query("SELECT nom_equipement FROM equipement WHERE id_equipement=".$y->id_equipement);
                            while( $l=$nom_equipement->fetchObject() ) {
                                    echo "$l->nom_equipement<br>";
                            }
                        }                    


                    echo '</td></tr>
                    ';
                }            
                echo '</table></div>';
            }
?>            
</div>


<div class="form-add-cinema">

    Ajouter un cinéma :
    <form action="<?php echo $action?>" method="POST">

        <div>
            <label>Nom :</label>
            <input type="text" name="cine_name" id="cine_name" placeholder="Entrer le nom du cinéma" <?php echo $editing?'value="'.$cinema_editing->nom_cinema.'"':"" ?> maxlength="50" required>
        </div>
        <div>
            <label>Ville :</label>
            <input type="text" name="cine_town" id="cine_town" placeholder="Entrer la ville du cinéma" <?php echo $editing?'value="'.$cinema_editing->ville_cinema.'"':"" ?> maxlength="50" required>
        </div>
        <div>
            <label>Adresse :</label>
            <input type="text" name="cine_adress" id="cine_adress" placeholder="Entrer l'adresse' du cinéma" <?php echo $editing?'value="'.$cinema_editing->adresse_cinema.'"':"" ?> maxlength="100" required>
        </div>
        <div>
            <label>Mail :</label>
            <input type="text" name="cine_mail" id="cine_mail" placeholder="Entrer l'email du cinéma" <?php echo $editing?'value="'.$cinema_editing->mail_cinema.'"':"" ?> maxlength="100" required>
        </div>
        <div>
            <label>Telephone :</label>
            <input type="text" name="cine_phone" id="cine_phone" placeholder="Entrer le numéro du cinéma" <?php echo $editing?'value="'.$cinema_editing->telephone_cinema.'"':"" ?> maxlength="25" required>
        </div>

        <button type="submit" name="submit"><?php echo $editing?"Editer Cinéma":"Ajouter Cinéma"?></button>

    </form>

</div>


<div class="form-add-salle" style="visibility:<?php echo $form_salle; ?>">
    <?php
        $stmt_nom_cinema = $dbh->prepare("SELECT nom_cinema FROM cinema WHERE id_cinema=".$_GET["id"]);
        $stmt_cinema->execute();
        $cinema = $stmt_cinema->fetch(PDO::FETCH_OBJ);
        echo '<h4>Ajouter une salle à ' . $cinema->nom_cinema . ' :</h4><br>';
    ?>

    <form action="<?php echo $action;?>" method="POST">

        <div>
            <label>Numéro :</label>
            <input type="text" name="salle_numero" id="salle_numero" placeholder="Numéro" <?php echo $editing_salle?'value="'."".'"':"" ?> maxlength="5" required>
        </div>
        <div>
            <label>Capacité :</label>
            <input type="text" name="salle_capa" id="salle_capa" placeholder="Capacité" <?php echo $editing_salle?'value="'."".'"':"" ?> maxlength="5" required>
        </div>

        <button type="submit" name="submit"><?php echo $editing_salle?"Editer Salle":"Ajouter Salle"?></button>

    </form>

</div>



<div class="form-bgcolor">
    <form action="index.php?setbgcolor" method="POST">

        <div>
            <label>bgcolor :</label>
            <select name="newbgcolor" id="newbgcolor">
                <option value="slategray">Ardoise</option>
                <option value="red">Rouge</option>
                <option value="orange">Orange</option>
            </select>
        </div>

        <button type="submit" name="submit">Changer</button>

    </form>

<div>


















</body>
</html>