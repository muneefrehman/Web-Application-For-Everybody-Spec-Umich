<?php
require_once "pdo.php";
session_start();
require_once "util.php";

if ( ! isset($_SESSION['name']) ) {
    die("ACCESS DENIED");
  }

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
        && isset($_POST['headline']) && isset($_POST['summary']) ) {
            
            // Validating profile entries
            $msg = validateProfile();
            if ( is_string($msg) ) {
                $_SESSION['error'] = $msg;
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }

            // Validating position entries
            $msg = validatePos();
            if ( is_string($msg) ) {
                $_SESSION['error'] = $msg;
                header("Location: edit.php?profile_id=".$_POST['profile_id']);
                return;
            }

            // Updating profile entries
            $stmt = $pdo->prepare("UPDATE profile SET first_name = :fn, last_name = :ln, email = :em,
                                headline = :hd, summary = :sm WHERE profile_id = :profile_id");
            $stmt->execute(array(
                ":profile_id" => $_POST['profile_id'],
                ":fn" => $_POST['first_name'],
                ":ln" => $_POST['last_name'],
                ":em" => $_POST['email'],
                ":hd" => $_POST['headline'],
                ":sm" => $_POST['summary']));                    

            // Clear out old position entries
            $stmt = $pdo->prepare("DELETE from position WHERE profile_id = :profile_id");
            $stmt->execute(array(
                ":profile_id" => $_POST['profile_id'])); 
                
            // Inserting the updated position entries
            $rank = 1;
            for ( $i=0; $i<=9; $i++ ) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];

                $stmt = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description)
                                VALUES (:profile_id, :rank, :year, :desc)");
                $stmt->execute(array(
                    ":profile_id" => $_POST['profile_id'],
                    ":rank" => $rank,
                    ":year" => $year,
                    ":desc" => $desc));

                    $rank++;
             } 

             $_SESSION['success'] = "Profile updated";
             header("Location: index.php");
             return;
        }  


// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

// Load up the position rows
$positions = loadPos($pdo, $profile_id);

?>


<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Syed Muneef ur Rehman</title>
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>    
    <h1>Editing Profile for <?php echo($_SESSION['name']) ?> </h1>
    <?php flashMessages(); ?>
    <form method="post" action="edit.php">
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">    
    <p>
        First Name:
        <input type="text" name="first_name" size="60" value="<?= $fn ?>">
    </p>
    <p>
        Last Name:
        <input type="text" name="last_name" size="60" value="<?= $ln ?>">
    </p>
    <p>
        Email:
        <input type="text" name="email" size="30" value="<?= $em ?>">
    </p>
    <p>
        Headline:
        <input type="text" name="headline" size="80" value="<?= $hd ?>">
    </p>
    <p>
        Summary:</br>
        <textarea name="summary" rows="8" cols="80"><?= $sm ?></textarea>
    </p>    
    
    <?php
    $pos = 0;
    echo ('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
    echo ('<div id="position_fields">'."\n");
    foreach ($positions as $position) {
        $pos++;
        echo ('<div id="position'.$pos.'">'."\n");
        echo ('<p>Year: <input type="text" name="year'.$pos.'"');
        echo (' value="'.$position['year'].'" />'."\n");
        echo ('<input type="button" value="-" ');
        echo ('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
        echo ("</p>\n");
        echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
        echo (htmlentities($position['description'])."\n");
        echo ("\n</textarea>\n</div>\n");
    }
    echo ("</div></p>\n");
    ?>


    <p><input type="submit" value="Save"/>
    <a href="index.php">Cancel</a></p>
    </form>
</body>

<script>
        countPos = <?= $pos ?>;

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



