<?php

require 'vendor/autoload.php';

use App\SQLiteConnection;

?>

<!DOCTYPE html>
<html>
<head>
  <title>Distributed Search Engine</title>
  <meta charset="utf-8"
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.10.2/sweetalert2.all.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="/dse">SIM DSE</a>
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
          <input class="form-control form-control-lg" minlength="2" type="text" placeholder="Search something.." id="validationCustom01" name="keyword" required style="border-radius: 5px 0px 0px 5px;">
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
              
              // Lowercase search string
              $new_keyword = strtolower($_GET['keyword']);

              // Exclude operator
              $exclude_slash = ' \ ';
              $pos = strpos($new_keyword, $exclude_slash);
              $exclude = '';
              
              // If there is exclude operator
              if ($pos) {

                $include = trim(strstr($new_keyword, $exclude_slash, true));
                $exclude = trim(strrchr($new_keyword, $exclude_slash));

                $sql = "SELECT id, word FROM words WHERE word = '$include'";

                $rows = $pdo->query($sql);
                $results = $rows->fetchAll();

                foreach ($results as $row) {

                  $words_id = $row['id'];
                  $words_word = $row['word'];

                }

                // Find word id for excluded word
                $sql = "SELECT id FROM words WHERE word = '$exclude'";

                $rows = $pdo->query($sql);
                $results = $rows->fetchAll();

                foreach ($results as $row) {

                  $exc_words_id = $row['id'];

                }

                // Find file id for excluded word
                $sql = "SELECT file_id FROM links WHERE word_id = '$exc_words_id' ";

                $rows = $pdo->query($sql);
                $results = $rows->fetchAll();

                foreach ($results as $row) {

                  $exc_links_file_id = $row['file_id'];

                }

                $sql = "SELECT word_id, file_id FROM links WHERE word_id = '$words_id' AND NOT file_id = '$exc_links_file_id' ";

                $rows = $pdo->query($sql);
                $results = $rows->fetchAll();

                echo "<h3>Found results for <b><i>" . $include . "</i></b> and <span style='color:red;'>excluding</span> " . $exclude . ".</h3>";
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

                        <p><span class="badge badge-light"><a href="<?php echo $files_path; ?>" target="_blank"><?php echo $files_path; ?></a></span></p>
                        
                        <?php 
                          echo "<pre>";
                          echo file_get_contents($files_path); 
                          echo "</pre>";
                        ?>

                      <?php endif; ?>
                    </div>
                  </div>

                  <?php endforeach;

                }

              // If don't have exclude operator, back to normal search
              } else {

                // Put every word into array and add comas for DB search
                $pieces = explode(" ", $new_keyword);
                $newarray = implode("','", $pieces);

                $sql = "SELECT id, word FROM words WHERE word in ('$newarray')";

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

                          <p><span class="badge badge-light"><a href="<?php echo $files_path; ?>" target="_blank"><?php echo $files_path; ?></a></span></p>
                          
                          <?php 
                            echo "<pre>";
                            echo file_get_contents($files_path); 
                            echo "</pre>";
                          ?>

                        <?php endif; ?>
                      </div>
                    </div>

                    <?php endforeach;

                  }

                  echo "<hr><a href='/dse' class='btn btn-primary'><i class='fa fa-back'></i> Back to home</a>";

                }

              }

            } else {

              echo "<div class='alert alert-warning'>";
              echo "<h3>Search Features</h3>";
              echo "<ul class=''>";
              echo "<li>Single or multiple word search</li>";
              echo "<li>Search exact words with &ldquo;lazy dog&rdquo;</li>";
              echo "<li>Exclude a word with &#92; charlie</li>";
              echo "<li>Simply combine them like &ldquo;lazy dog&rdquo; &#92; charlie</li>";
              echo "</ul>";
              echo "</div>";
            
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