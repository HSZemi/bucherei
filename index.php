<?php

// Bucherei
// Copyright (C) 2017 HSZemi
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.


function is_request()
{
    return isset($_POST['id']) && isset($_POST['nummer']) && isset($_POST['autor']) && isset($_POST['titel']) && isset($_POST['sparte']) && isset($_POST['erscheinungsjahr']) && isset($_POST['verlag']) && isset($_POST['beschreibung']) && isset($_POST['bereich']);
}

include 'dbconfig.php';

// Create connection
$conn = new mysqli(SERVER_NAME, USER_NAME, USER_PASSWORD, DB_NAME);
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
$rowcount         = 0;

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
        $a="%$nummer%";
        $b="%$autor%";
        $c="%$titel%";
        $d="%$sparte%";
        $e="%$erscheinungsjahr%";
        $f="%$verlag%";
        $g="%$beschreibung%";
        $h="%$bereich%";
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
        $stmt->bind_param("ssssssss", $a,$b,$c,$d,$e,$f,$g,$h);
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
    <link rel="stylesheet" href="css/theme.bootstrap_4.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
    .card-header {
        cursor: pointer;
    }
    #results {
        padding: 0;
    }
    footer{
        margin-top: 2em;
    }
    @media print {    
        .no-print, .no-print * {
            display: none !important;
        }
        .th-nr:before{
            content: "Nr";
        }
        .th-autor:before{
            content: "Autor";
        }
        .th-titel:before{
            content: "Titel";
        }
        .th-sparte:before{
            content: "Sparte";
        }
        .th-jahr:before{
            content: "Jahr";
        }
        .th-bereich:before{
            content: "Bereich";
        }
        th div{
            display: none;
        }
    }
    abbr[title] {
        text-decoration: none;
    }
    </style>
  </head>

  <body>
    <?php include "nav.html";?>
    <div class="container-fluid">
      <div class="card no-print">
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
        <div class="card-header no-print" onclick="toggleResults()">
          Ergebnisse (<span id="rowcount">lädt…</span>)
        </div>
        <div class="card-body" id="results">
          <table class="table table-bordered table-sm" id="resulttable">
            <thead>
              <tr>
                <th class="th-nr">Nr</th>
                <th class="th-autor">Autor</th>
                <th class="th-titel">Titel</th>
                <th class="th-sparte">Sparte</th>
                <th title="Erscheinungsjahr" class="th-jahr">Jahr</th>
                <th class="no-print">Verlag</th>
                <th class="no-print">Beschreibung</th>
                <th class="th-bereich">Bereich</th>
                <th class="no-print" data-sorter="false"></th>
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
            <td class='no-print'>$rverlag</td>
            <td class='no-print'>$rbeschreibung</td>
            <td>$rbereich</td>
            <td class='no-print'><a href='./edit.php?id=$rid'><i class='fa fa-pencil-square-o' aria-hidden='true'></i>
</a></td>
        </tr>";
    }
    if(!$has_result) {
        echo "<tr><td colspan='8'>0 results</td></tr>";
    }
    $rowcount = $stmt->num_rows;
    $stmt->close();
}
?>

            </tbody>
          </table>

        </div>
      </div>

      <footer class="text-center text-muted">
      »Bucherei« ist freie Software, veröffentlicht unter Open-Source-Lizenz auf <a href="https://github.com/hszemi/bucherei" target="_blank"><abbr title="github.com/hszemi/bucherei">Github</abbr></a>
      </footer>

    </div>

    <script src="js/jquery-3.2.1.min.js" ></script>
    <script src="js/popper.min.js" ></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
      $(function(){ 
        $("#resulttable").tablesorter({theme : "bootstrap"});
        $("#rowcount").text('<?php echo $rowcount; ?>');
      }); 
      
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