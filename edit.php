<?php

function is_request()
{
    return isset($_POST['nummer']) && isset($_POST['autor']) && isset($_POST['titel']) && isset($_POST['erscheinungsjahr']) && isset($_POST['verlag']) && isset($_POST['beschreibung']) && isset($_POST['bereich']);
}

function is_update(){
	return isset($_GET['id']) || (isset($_POST['id']) && intval($_POST['id']) > 0);
}

function is_delete(){
    return isset($_POST['delete']);
}

function is_deleted(){
    return isset($_GET['deleted']) && !isset($_POST['save']);
}

$servername = "localhost";
$username   = "buch";
$password   = "BUCH";
$dbname     = "buch";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$stmt              = "";
$id               = "";
$nummer           = "";
$autor            = "";
$titel            = "";
$sparte           = "";
$erscheinungsjahr = "";
$verlag           = "";
$beschreibung     = "";
$bereich          = "";

$alert_update = false;
$alert_insert = false;
$alert_delete = false;


if(is_delete()){
    $id = intval($_POST['id']);
    print("delete ".$id);
    $stmt = $conn->prepare("DELETE FROM Buch WHERE ID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if($stmt->affected_rows > 0){
        header('Location: ./edit.php?deleted');
        die();
    }

} elseif(is_deleted()){
    $alert_delete = true;
} else {
    if (is_request()) {
        $id               = intval($_POST['id']);
        if(!($id > 0) && isset($_GET['id'])){
            $id = intval($_GET['id']);
        }
        $nummer           = $_POST['nummer'];
        $autor            = $_POST['autor'];
        $titel            = $_POST['titel'];
        $sparte           = $_POST['sparte'];
        $erscheinungsjahr = $_POST['erscheinungsjahr'];
        $verlag           = $_POST['verlag'];
        $beschreibung     = $_POST['beschreibung'];
        $bereich          = $_POST['bereich'];
        
        if(is_update()){
            $stmt = $conn->prepare("UPDATE Buch SET Nummer=?, Autor=?, Titel=?, Sparte=?, Erscheinungsjahr=?, Verlag=?, Beschreibung=?, Bereich=? WHERE ID=?");
            $stmt->bind_param("ssssssssi", $nummer,$autor,$titel,$sparte,$erscheinungsjahr,$verlag,$beschreibung,$bereich,$id);
            $stmt->execute();
            $alert_update = true;
        } else {
            $stmt = $conn->prepare("INSERT INTO Buch(Nummer, Autor, Titel, Sparte, Erscheinungsjahr, Verlag, Beschreibung, Bereich) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssss", $nummer,$autor,$titel,$sparte,$erscheinungsjahr,$verlag,$beschreibung,$bereich);
            $stmt->execute();
            $alert_insert = true;
        }
    }

    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    } elseif(isset($_POST['id']) && intval($_POST['id'])){
        $id = intval($_POST['id']);
    } elseif(is_request()) {
        $id = $conn->insert_id;
    }
    if(isset($_GET['id']) || is_request()){
        $stmt = $conn->prepare("SELECT id, Nummer, Autor, Titel, Sparte, Erscheinungsjahr, Verlag, Beschreibung, Bereich FROM buch WHERE ID=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $nummer, $autor, $titel, $sparte, $erscheinungsjahr, $verlag, $beschreibung, $bereich);
        $stmt->fetch();
    }
}

?>
<!doctype html>
<html lang="de">
  <head>
    <title>Bearbeiten – Bucherei</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.journal.min.css">
      <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
      .card-header {
        cursor: pointer;
      }
    </style>
  </head>

  <body>
  
  <div class="modal" tabindex="-1" role="dialog" id="deletemodal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buch löschen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Soll das Buch wirklich gelöscht werden?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="executeDelete()">Buch löschen</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
            </div>
        </div>
    </div>
  </div>
  
    <?php include "nav.html";?>
    <div class="container-fluid">
    
    <?php
    
    if($alert_update){
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Erfolg!</strong> Der Eintrag wurde aktualisiert.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    }

    if($alert_insert){
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Erfolg!</strong> Der Eintrag wurde erstellt.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    }

    if($alert_delete){
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Erfolg!</strong> Der Eintrag wurde gelöscht.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    }
    ?>


    
      <div class="card">
        <div class="card-header">
          Bearbeiten / Hinzufügen
        </div>
        <div class="card-body" id="searchform">

          <form method="post" id="editForm">
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                  <label for="inputId">Id</label>
                  <input type="text" class="form-control" id="inputId" placeholder="" value="<?php echo $id; ?>" disabled>
                  <input type="hidden" name="id" value="<?php echo $id; ?>">
                </div>
              </div>
              <div class="col-5">
                <div class="form-group">
                  <label for="inputNummer">Nummer</label>
                  <input type="text" class="form-control" id="inputNummer" placeholder="" name="nummer" value="<?php echo $nummer; ?>">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="inputAutor">Autor</label>
                  <input type="text" class="form-control" id="inputAutor" placeholder="" name="autor" value="<?php echo $autor; ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="inputTitel">Titel</label>
                  <input type="text" class="form-control" id="inputTitel" placeholder="" name="titel" value="<?php echo $titel; ?>">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="inputSparte">Sparte</label>
                  <input type="text" class="form-control" id="inputSparte" placeholder="" name="sparte" value="<?php echo $sparte; ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="inputErscheinungsjahr">Erscheinungsjahr</label>
                  <input type="text" class="form-control" id="inputErscheinungsjahr" placeholder="" name="erscheinungsjahr" value="<?php echo $erscheinungsjahr; ?>">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="inputVerlag">Verlag</label>
                  <input type="text" class="form-control" id="inputVerlag" placeholder="" name="verlag" value="<?php echo $verlag; ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="inputBeschreibung">Beschreibung</label>
                  <input type="text" class="form-control" id="inputBeschreibung" placeholder="" name="beschreibung" value="<?php echo $beschreibung; ?>">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="inputBereich">Bereich</label>
                  <input type="text" class="form-control" id="inputBereich" placeholder="" name="bereich" value="<?php echo $bereich; ?>">
                </div>
              </div>
            </div>
            <button type="submit" name="save" class="btn btn-primary">Speichern</button>
            <a href="edit.php" class="btn btn-secondary">Zurücksetzen</a>
            <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#deletemodal">Löschen</button>
          </form>
        </div>
      </div>

    </div>

    <script src="js/jquery-3.2.1.min.js" ></script>
    <script src="js/popper.min.js" ></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    
      function executeDelete(){
        $('#editForm').append('<input type="hidden" name="delete">');
        $('#editForm').submit();
      }
    
      function resetForm() {
        $('#inputId').val('');
        $('#inputNummer').val('');
        $('#inputAutor').val('');
        $('#inputTitel').val('');
        $('#inputErscheinungsjahr').val('');
        $('#inputVerlag').val('');
        $('#inputBeschreibung').val('');
        $('#inputBereich').val('');
      }

    </script>
  </body>

  </html>
  <?php

$conn->close();
?>