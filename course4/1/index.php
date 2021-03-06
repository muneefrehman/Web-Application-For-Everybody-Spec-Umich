<?php
require_once "pdo.php";
session_start();

$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Syed Muneef ur Rehman</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
    <div class="container">
      <h1>Muneef Rehman's Resume Registry</h1>
      <?php
        if (! isset($_SESSION['name']) ) {
          echo ("<a href='login.php'>Please log in</a><br>");
          echo('<table border="1">'."\n");
          echo('<tr><th>Name</th><th>Headline</th></tr>');
          $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM profile");
          while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo("<tr><td>");
            echo("<a href='view.php?profile_id=".urlencode($row['profile_id']) . "'>" . htmlentities($row['first_name'] . " ". $row['last_name'])  . "</a>");
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td></tr>\n");
          }
          return;
        }
      ?>
      <?php
        if ( isset($_SESSION['success']) ) {
          echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
          unset($_SESSION['success']);
        }
        if ( isset($_SESSION['error']) ) {
          echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
          unset($_SESSION['error']);
        }
        echo('<table border="1">'."\n");
        echo('<tr><th>Name</th><th>Headline</th><th>Action</th></tr>');
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
            echo("<tr><td>");
            echo("<a href='view.php?profile_id=".urlencode($row['profile_id']) . "'>" . htmlentities($row['first_name'] ." ". $row['last_name'])  . "</a>");
            echo("</td><td>");
            echo(htmlentities($row['headline']));
            echo("</td><td>");
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
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
