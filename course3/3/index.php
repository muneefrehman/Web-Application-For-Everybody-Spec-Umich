<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Syed Muneef ur Rehman</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
      <h1>Welcome to Automobiles Database</h1>
      <?php
        if (! isset($_SESSION['email']) ) {
          echo ('<a href="login.php">Please log in</a><br>');
          echo ('Attempt to <a href="add.php">add data</a> without logging in');
          return;
        }
      ?>
      <?php
        if ( isset($_SESSION['error']) ) {
          echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
          unset($_SESSION['error']);
        }
        if ( isset($_SESSION['success']) ) {
          echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
          unset($_SESSION['success']);
        }
        echo('<table border="1">'."\n");
        echo('<tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr>');
        $stmt = $pdo->query("SELECT autos_id, make, model, year, mileage FROM autos1");
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo "<tr><td>";
            echo(htmlentities($row['make']));
            echo("</td><td>");
            echo(htmlentities($row['model']));
            echo("</td><td>");
            echo(htmlentities($row['year']));
            echo("</td><td>");
            echo(htmlentities($row['mileage']));
            echo("</td><td>");
            echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
            echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
            echo("</td></tr>\n");
        }
?>
</table>
    </div>
    <a href="add.php">Add New Entry</a>
    <br>
    <br>
    <a href="logout.php">Logout</a>
  </body>
</html>
