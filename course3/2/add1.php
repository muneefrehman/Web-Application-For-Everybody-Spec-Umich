"Can't figure out what's wrong with this code"
"It's not working"

<?php
session_start();

if ( ! isset($_SESSION['who'] ) ) {
    die("Not logged in");

if ( isset($_POST['cancel']) ) {
    header("Location: view.php?name=".urlencode($_SESSION['who']));
    return;
    }

require_once "pdo.php";

if ( isset($_POST['make']) ) {
    if ( strlen($_POST['make']) > 1 ) {
        if ( is_numeric($_POST['year']) && is_numeric($_POST['mileage']) ) {
            $stmt = $pdo->prepare("INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)");
            $stmt->execute(array(
                ':mk'=> $_POST['make'],
                ':yr'=> $_POST['year'],
                ':mi'=> $_POST['mileage'])
            );
            $_SESSION['success'] = "Record Inserted";
            header( "Location: view.php" );
            return;
        } else {
            $_SESSION['error'] = "Mileage and year must be numeric";
            header( 'Location: add.php' );
            return;
        }
    } else { $_SESSION['error'] = "Make is required";
            header( 'Location: add.php' );
            return;
        }
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
<h1>Tracking autos for <?php echo($_SESSION['who']) ?> </h1>

<?php
        if ( isset($_SESSION['error']) ) {
          echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
          unset($_SESSION['error']);
        }
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
<input type="submit" name="cancel" value="Cancel">
</form>

</html>