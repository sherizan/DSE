<?php

require 'vendor/autoload.php';

use App\SQLiteConnection;

?>

<!DOCTYPE html>
<html>
<head>
  <title>Distributed Search Engine</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
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
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
  <div class="row my-4">
    <div class="col">
      <?php

        // Connect to SQLite
        $pdo = (new SQLiteConnection())->connect();
        if ($pdo != null) {
            echo '<div class="alert alert-success" role="alert">Connected to the SQLite database successfully!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        } else {
          echo '<div class="alert alert-danger" role="alert">Whoops, could not connect to the SQLite database!</div>';
        }
      ?>
    </div>
  </div>
  <div class="row p-3 my-4 mx-auto justify-content-md-center align-items-center">
    <div class="col-4 align-self-center">
      <form class="form-inline my-2 my-lg-0" action="index.php">
        <input class="form-control mr-sm-2" type="text" placeholder="Search something" name="keyword" required="true">
        <input type="submit" class="btn btn-outline-success my-2 my-sm-0" value="Go"/>
      </form>
    </div>
  </div>
</div>

<div class="container">
  <div class="row my-4">
    <div class="col">
      
      <h4 class="my-2">Results</h4>

      <div class="card">
        <div class="card-body">
          <?php

          if(isset($_GET['keyword']))
          {
            $new_keyword = $_GET['keyword'];


              if($new_keyword != null)
              {
              // Select word based on what user searches
              $sql = "SELECT id, word FROM words WHERE word like '%$new_keyword%' ";

              foreach ($pdo->query($sql) as $row) {
                  $words_id = $row['id'];
                  $words_word = $row['word'];
                  // echo $words_id . "\t";
                  // echo $words_word . "\n";
              }

              $sql = "SELECT word_id, file_id FROM links WHERE word_id = '$words_id' "; 

              foreach ($pdo->query($sql) as $row) {
                  $links_word_id = $row['word_id'];
                  $links_file_id = $row['file_id'];
                  // echo $links_work_id . "\t";
                  // echo $links_file_id . "\n";
              }

              $sql = "SELECT id, file_path FROM files WHERE id = '$links_file_id' ";

              foreach ($pdo->query($sql) as $row) {
                  $files_id = $row['id'];
                  $files_path = $row['file_path'];
                  // echo $files_id . "\t";
                  echo "<b>" . $new_keyword . "</b><br>";
                  echo $files_path . "<br>";
              }

              $sql = "SELECT word_id FROM links WHERE file_id = '$files_id' ";

              foreach ($pdo->query($sql) as $row) {
                  $links_word_id = $row['word_id'];
                  $sql = "SELECT word FROM words WHERE id like '$links_word_id' ";
                  foreach ($pdo->query($sql) as $row) {
                    $words_word = $row['word'];
                    echo "<b>" . $words_word . "</b><br>";
                  }
              }
            }
          }
          else
          {
            echo "<center><h1>Search Something</h1></center>";
          }

           

          ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$("#btn-search").click( function() {
  $('#myModal').modal()
});
</script>

</body>
</html>