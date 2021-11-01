<?php

session_start();

if ( ! isset($_SESSION['email']) ) {
  die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
    header("Location: index.php?name=".urlencode($_SESSION['email']));
    return;
    }


    require_once "pdo.php";



// Check to see if we have some POST data, if we do process it
if (isset($_POST['make']) && isset($_POST['model']) && isset($_POST['mileage']) && isset($_POST['year'])) 
{
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }
    if ( !is_numeric($_POST['mileage']) || !is_numeric($_POST['year']) ) 
    {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
    return;
    } 
    else if (strlen($_POST['make']) < 1)
    {
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
    return;
    }
    else 
    {
        $stmt = $pdo->prepare("INSERT INTO autos1 (make, model, year, mileage) VALUES (:mk, :mo, :yr, :mi)");
            $stmt->execute(array(
                ':mk'=> $_POST['make'],
                ':mo'=> $_POST['model'],
                ':yr'=> $_POST['year'],
                ':mi'=> $_POST['mileage'])
            );
            $_SESSION['success'] = "Record added";
            header( "Location: index.php" );
            return;
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
<h1>Tracking Automobiles for <?php echo($_SESSION['email']) ?> </h1>

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
    Model:
    <input type="text" name="model" size="60">
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