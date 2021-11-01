<?php
session_start();

// Check if we are logged
if ( ! isset($_SESSION['email'] ) ) {
    die("Not logged in");
    }
  

require_once "pdo.php";
?>


<!DOCTYPE html>
<html>
<head>
<title>Syed Muneef ur Rehman</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
 
<div class="container">   
 
<h1>Tracking Autos for <?php echo($_SESSION['email']); ?></h1>

<?php
        if ( isset($_SESSION['success']) ) {
          echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
          unset($_SESSION['success']);
        }
?>

<h2>Automobiles</h2>
<ul>
<?php
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo("<li>".$row['year']." ".$row['make']." "."/"." ".$row['mileage']."</li>");
}
?>
</ul>
<p>
<a href="add.php">Add New</a> |
<a href="logout.php">Logout</a>
</p>
