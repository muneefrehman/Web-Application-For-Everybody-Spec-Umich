<?php

// Flash messages
function flashMessages() {
    if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }   

    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
}

// Validation for profiles
function validateProfile() {
    if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 || strlen($_POST['email']) == 0
            || strlen($_POST['headline']) == 0 || strlen($_POST['summary']) == 0 ) {
                return "All fields are required";
            }
    
    if ( strpos($_POST['email'], '@') === false) {
        return "Email address must contain @";
    }
    return true;
}

// Look through the POST data and return true or error message
function validatePos() {
    for ( $i=1; $i<=9; $i++ ) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

// Loading positions
function loadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare("SELECT * FROM position 
                            WHERE profile_id = :profile_id ORDER BY rank");
    $stmt->execute(array(
        ":profile_id" => $profile_id));
    $positions = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $positions[] = $row;
    }       
    return $positions;                     
}