<?php

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

require_once "pdo.php";

$failure = false;
$success = false;

if ( isset($_POST['make']) ) {
    if ( strlen($_POST['make']) > 1 ) {
        if ( is_numeric($_POST['year']) && is_numeric($_POST['mileage']) ) {
            $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
            $stmt->execute(array(
                ':mk'=> $_POST['make'],
                ':yr'=> $_POST['year'],
                ':mi'=> $_POST['mileage'])
            );
            $success = "Record Inserted";
        } else {
            $failure = "Mileage and year must be numeric";
        }
    } else { $failure = "Make is required";
        }
}    

?>
<!DOCTYPE html>
<html>
<head>
<title>Syed Muneef ur Rehman</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">   
<h1>Tracking autos for <?php echo($_GET['name']) ?> </h1>

<?php
    if ( $failure != false) { echo ('<p style="color: red;">'.htmlentities($failure)."</p>\n"); }
    if ( $success != false ) { echo ('<p style="color: green;">'.htmlentities($success)."</p>\n"); }
 ?>

<form method="post">
<p>
    Make:
    <input type="text" name="make" size="60">
</p>
<p>
    Year:
    <input type="text" name="year">
</p>
<p>
    Mileage:
    <input type="text" name="mileage">
</p>    
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>

<ul>
<?php
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo("<li>".$row['year']." ".$row['make']." "."/"." ".$row['mileage']."</li>");
}
?>
</ul>

</div>
</body>
</html>
