<?php

function check_login($conn) 
{
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':username', $username);
        oci_execute($stmt);

        if($user_data = oci_fetch_assoc($stmt)) {
            return $user_data;
        } else {
            error_log("User data could not be fetched for username: " . $username);
        }
    } else {
        error_log("Session username is not set");
    }

    header("Location: login.php");
    die;
}

function check_configuration($conn)
{
    
}

?>