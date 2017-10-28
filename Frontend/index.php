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

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <?php

          $db_fail = 0;
          $show_error = '';

          try {

            // Connect to SQLite
            $pdo = (new SQLiteConnection())->connect();

            echo '<a class="nav-link" href="#" style="color:green"><i class="fa fa-check-circle-o"></i> Connected <span class="sr-only">(current)</span></a>';

            $db_fail = 1;

          } catch (PDOException $e) {

            $show_error = $e->getMessage();

            echo '<a class="nav-link" href="#" style="color:red"><i class="fa fa-close"></i> Connection failed</a>';
            $db_fail = 0;

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
      
      <div style="border: 1px solid #f3f5f8;border-radius: 5px;">
        <div style="padding: 30px;">
        	
          <?php

          if ($db_fail != 0) {

            if (isset($_GET['keyword'])) {
              
              $new_keyword = $_GET['keyword'];

              // If user search more than 1 word 
              list($one, $two) = explode(" ", $new_keyword, 2);

              // Select word based on what user searches
              $sql = "SELECT id, word FROM words WHERE word like '%$new_keyword%' ";

              $rows = $pdo->query($sql);
              $results = $rows->fetchAll();

              if(empty($results)) {

                echo 'Oops! We cannot find any files with the word <b>' . $new_keyword . '</b>';

              } else {

                foreach ($results as $row) {

                  $words_id = $row['id'];
                  $words_word = $row['word'];

                }

                $sql = "SELECT word_id, file_id FROM links WHERE word_id = '$words_id' ";

                $rows = $pdo->query($sql);
                $results = $rows->fetchAll();

                echo "<h3>Found results for <b><i>" . $new_keyword . "</i></b>.</h3>";
                echo "<hr>";

                foreach ($results as $row) {

                  $links_file_id = $row['file_id'];

                  $sql = "SELECT id, file_path FROM files WHERE id = '$links_file_id' ";

                  $rows = $pdo->query($sql);
                  $results = $rows->fetchAll();

                  foreach ($results as $row) :

                    $files_id = $row['id'];
                    $files_path = $row['file_path'];

                  ?>

                  <div class="card my-4">
                    <div class="card-body">
                      
                      <?php if (file_exists($files_path)) : ?>

                        <p><span class="badge badge-light">Content</span></p>
                        
                        <?php 
                          echo "<pre>";
                          echo file_get_contents($files_path); 
                          echo "</pre>";
                        ?>

                      <p><a href="<?php echo $files_path; ?>" target="_blank">View '<?php echo $files_path; ?>'</a></p>

                      <?php endif; ?>
                    </div>
                  </div>
                    
                  <?php endforeach;

                }

              }

            }

          } else {

            echo "Please check your database connection.";

          }

        ?>
        </div>
      </div>
    </div>
  </div>
</div>


</body>
</html>