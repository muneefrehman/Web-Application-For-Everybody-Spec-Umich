<?php

session_start();

if ( ! isset($_SESSION['name']) ) {
  die("ACCESS DENIED");
}

if ( isset($_POST['cancel']) ) {
    header("Location: index.php?name=".urlencode($_SESSION['name']));
    return;
    }


 require_once "pdo.php";
 require_once "util.php";



// Check to see if we have some POST data, if we do process it
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
     && isset($_POST['headline']) && isset($_POST['summary']) ) {
        
        // Validating profile entries
        $msg = validateProfile();
        if ( is_string($msg) ) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        // Validating position entries
        $msg = validatePos();
        if ( is_string($msg) ) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        // Inserting profile entries
        $stmt = $pdo->prepare("INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                                    VALUES (:user_id, :fn, :ln, :em, :hd, :sm)");
        $stmt->execute(array(
            ":user_id" => $_SESSION['user_id'],
            ":fn" => $_POST['first_name'],
            ":ln" => $_POST['last_name'],
            ":em" => $_POST['email'],
            ":hd" => $_POST['headline'],
            ":sm" => $_POST['summary']));   
            
        $profile_id = $pdo->lastInsertId();
        
        // Inserting position entries
        $rank = 1;
        for ( $i=1; $i<=9; $i++ ) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $stmt = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) 
                                VALUES (:profile_id, :rank, :year, :desc)");
            $stmt->execute(array(
                ":profile_id" => $profile_id,
                ":rank" => $rank,
                ":year" => $year,
                ":desc" => $desc)); 
                
                $rank++;
        }

        $_SESSION['success'] = "Profile added";
        header("Location: index.php");
        return;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Syed Muneef ur Rehman</title>
<?php require_once "bootstrap.php"; ?>
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">   
<h1>Adding Profile for <?php echo($_SESSION['name']) ?> </h1>

<?php
        flashMessages();
      ?>


<form method="post" action="add.php">
<p>
    First Name:
    <input type="text" name="first_name" size="60">
</p>
<p>
    Last Name:
    <input type="text" name="last_name" size="60">
</p>   
<p>
    Email:
    <input type="text" name="email" size="30">
</p>
<p>
    Headline:
    <input type="text" name="headline" size="80">
</p>    
<p>
    Summary:</br>
    <textarea name="summary" rows="8" cols="80"></textarea>
</p>
<p>
    Position: <input type="submit" id="addPos" value="+">
    <div id="position_fields">
    </div>
</p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>
    countPos = 0;

    $(document).ready(function() {
        window.console && console.log("Document ready called");
        $('#addPos').click(function(event) {
            event.preventDefault();
            if (countPos >= 9) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position "+countPos);
            $('#position_fields').append(
                '<div id="position'+countPos+'"> \
                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                <input type="button" value="-" \
                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                </div>'
            );
        });
    })
</script>

</html>