<?php
session_start();
include 'functions.php';
include 'db_connection.php';

$user_data = check_login($conn);

if (isset($_GET['username']) && isset($_GET['ConfigurationName'])) {
    $username = $_GET['username'];
    $ConfigurationName = $_GET['ConfigurationName'];

    header("Location: computer.php?username=" . urlencode($username) . "&ConfigurationName=" . urlencode($ConfigurationName));
    exit();
} else {
    echo "Error: username or ConfigurationName is not set.";
}

oci_close($conn);
?>
