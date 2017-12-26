<?php

function is_request()
{
    return isset($_POST['id']) && isset($_POST['nummer']) && isset($_POST['autor']) && isset($_POST['titel']) && isset($_POST['sparte']) && isset($_POST['erscheinungsjahr']) && isset($_POST['verlag']) && isset($_POST['beschreibung']) && isset($_POST['bereich']);
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
$stmt             = "";
$id               = "";
$nummer           = "";
$autor            = "";
$titel            = "";
$sparte           = "";
$erscheinungsjahr = "";
$verlag           = "";
$beschreibung     = "";
$bereich          = "";

if (is_request()) {
    $id               = $_POST['id'];
    $nummer           = $_POST['nummer'];
    $autor            = $_POST['autor'];
    $titel            = $_POST['titel'];
    $sparte           = $_POST['sparte'];
    $erscheinungsjahr = $_POST['erscheinungsjahr'];
    $verlag           = $_POST['verlag'];
    $beschreibung     = $_POST['beschreibung'];
    $bereich          = $_POST['bereich'];
    
    if ($id != null) {
        $stmt = $conn->prepare("SELECT id, Nummer, Autor, Titel, Sparte, Erscheinungsjahr, Verlag, Beschreibung, Bereich FROM buch WHERE ID=?");
        $stmt->bind_param("i", $id);
    } else {
        $stmt = $conn->prepare("SELECT id, Nummer, Autor, Titel, Sparte, Erscheinungsjahr, Verlag, Beschreibung, Bereich 
        FROM buch 
        WHERE Nummer LIKE ?
        AND Autor like ?
        AND Titel like ?
        AND Sparte like ?
        AND Erscheinungsjahr like ?
        AND Verlag like ?
        AND Beschreibung like ? 
        AND Bereich like ?");
        $stmt->bind_param("ssssssss", $a="%$nummer%",$b="%$autor%",$c="%$titel%",$d="%$sparte%",$e="%$erscheinungsjahr%",$f="%$verlag%",$g="%$beschreibung%",$h="%$bereich%");
    }
}


?>
<!doctype html>
<html lang="de">
  <head>
    <title>Suche – Bucherei</title>
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
    <?php include "nav.html";?>
    <div class="container-fluid">
      <div class="card">
        <div class="card-header" onclick="toggleSearch()">
          Suche
        </div>
        <div class="card-body" id="searchform">

          <form method="post">
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                  <label for="inputId">Id</label>
                  <input type="number" class="form-control" id="inputId" placeholder="" name="id" value="<?php echo $id; ?>">
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
            <button type="submit" class="btn btn-primary">Suchen</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Zurücksetzen</button>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header" onclick="toggleResults()">
          Ergebnisse
        </div>
        <div class="card-body" id="results">
          <table class="table">
            <thead>
              <tr>
                <th>Nummer</th>
                <th>Autor</th>
                <th>Titel</th>
                <th>Sparte</th>
                <th>Erscheinungsjahr</th>
                <th>Verlag</th>
                <th>Beschreibung</th>
                <th>Bereich</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              <?php
if (is_request()) {
    $stmt->execute();
    $stmt->bind_result($rid, $rnummer, $rautor, $rtitel, $rsparte, $rerscheinungsjahr, $rverlag, $rbeschreibung, $rbereich);
    $has_result = false;
    while ($stmt->fetch()) {
        $has_result = true;
        echo "<tr>
            <td>$rnummer</td>
            <td>$rautor</td>
            <td>$rtitel</td>
            <td>$rsparte</td>
            <td>$rerscheinungsjahr</td>
            <td>$rverlag</td>
            <td>$rbeschreibung</td>
            <td>$rbereich</td>
            <td><a href='./edit.php?id=$rid'><i class='fa fa-pencil-square-o' aria-hidden='true'></i>
</a></td>
        </tr>";
    }
    if(!$has_result) {
        echo "<tr><td colspan='8'>0 results</td></tr>";
    }
    $stmt->close();
}
?>

            </tbody>
          </table>

        </div>
      </div>


    </div>

    <script src="js/jquery-3.2.1.min.js" ></script>
    <script src="js/popper.min.js" ></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
      function resetForm() {
        $('#inputId').val('');
        $('#inputNummer').val('');
        $('#inputAutor').val('');
        $('#inputTitel').val('');
        $('#inputSparte').val('');
        $('#inputErscheinungsjahr').val('');
        $('#inputVerlag').val('');
        $('#inputBeschreibung').val('');
        $('#inputBereich').val('');
      }
      function toggleSearch() {
        $('#searchform').slideToggle();
      }
      function toggleResults() {
        $('#results').slideToggle();
      }
    $(function(){
    <?php
if (is_request()) {
        echo 'toggleSearch();';
      } else {
        echo 'toggleResults();';
      }
?>
	});
    </script>
  </body>

  </html>
  <?php

$conn->close();
?>