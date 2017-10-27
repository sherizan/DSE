<?php

require 'vendor/autoload.php';

use App\SQLiteConnection;

?>

<!DOCTYPE html>
<html>
<head>
  <title>Distributed Search Engine</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.10.2/sweetalert2.all.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">SIM DSE</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <?php
    // Connect to SQLite
    $pdo = (new SQLiteConnection())->connect();
  ?>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <?php
        
        if ($pdo != null) {
            echo '<a class="nav-link" href="#" style="color:green"><i class="fa fa-check-circle-o"></i> Connected <span class="sr-only">(current)</span></a>';
        } else {
          echo '<a class="nav-link" href="#">dot failed <span class="sr-only">(current)</span></a>';
        }
        ?>
        
      </li>
    </ul>
  </div>
</nav>

<div class="container">
  <div class="row my-4">
    <div class="col">

    </div>
  </div>
  <div class="row p-3 my-4 mx-auto justify-content-md-center align-items-center">
    <div class="col-md-4 col-xs-12 align-self-center">
      <form class="form-inline my-2 my-lg-0" action="index.php" id="needs-validation" novalidate>
        <div class="input-group">
          <label for="validationCustom01"></label>
          <input class="form-control form-control-lg" minlength="2" type="text" placeholder="Search something" id="validationCustom01" name="keyword" required>
          <span class="input-group-btn">
            <button class="btn btn-outline-success btn-lg" type="submit"><i class="fa fa-search"></i> Go</button>
          </span>
        </div>
      </form>
      <script>
      (function() {
        'use strict';

        window.addEventListener('load', function() {
          var form = document.getElementById('needs-validation');
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        }, false);
      })();
      </script>

    </div>
  </div>

</div>

<div class="container">
  <div class="row my-4">
    <div class="col">
      
      <div class="card">
        <div class="card-body">
        	
          <?php

          if (isset($_GET['keyword'])) {
            
            $new_keyword = $_GET['keyword'];

            if ($new_keyword != null) {

              // Select word based on what user searches
              $sql = "SELECT id, word FROM words WHERE word like '%$new_keyword%' ";

              foreach ($pdo->query($sql) as $row) {
                $words_id = $row['id'];
                $words_word = $row['word'];
                echo $words_id;
                
                $sql = "SELECT word_id, file_id, COUNT(file_id) as id_count FROM links WHERE word_id = '$words_id' ";

                foreach ($pdo->query($sql) as $row) {
                  $links_word_id = $row['word_id'];
                  $links_file_id = $row['file_id'];
                  $links_count = $row['id_count'];

                  echo "<h3>Found " . $links_count . " results for <b><i>" . $new_keyword . "</i></b>.</h3>";

                  echo $links_file_id;

                  $sql = "SELECT id, file_path, COUNT(id) as file_count FROM files WHERE id = '$links_file_id' ";

                  foreach ($pdo->query($sql) as $row) {
                    $files_id = $row['id'];
                    $files_path = $row['file_path'];

                    echo "<p>File path:</p>";
                    echo "<p>" . $files_path . "</p>";
                    
                    if (file_exists($files_path)) {
                      echo '<p>Content:</p>';
                      echo '<pre>';
                      echo file_get_contents($files_path);
                      echo '</pre>';
                    }
                  }
                }
              }
        	  }
            else {
        			echo "<div><center><h1>Please Search Again, No Such File/Word</h1></center></div>";
        		}
          }
          else {
            echo "<div><center><h1>Search Something</h1></center></div>";
          }
        ?>
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>