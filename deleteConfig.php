<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

if (isset($_GET['ConfigurationName'])) {
    $config_name = $_GET['ConfigurationName'];

    $sql = "DELETE FROM configuration WHERE ConfigurationName = :config_name AND username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':config_name', $config_name);
    oci_bind_by_name($stmt, ':username', $username);

    $success = oci_execute($stmt);

    if ($success) {
        oci_commit($conn);
        oci_free_statement($stmt);
        oci_close($conn);

        $_SESSION['delete_success'] = "Configuration deleted successfully.";
        header("Location: configuration.php");
        exit();
    } else {
        $_SESSION['delete_error'] = "Error deleting configuration.";
    }
} else {
    $_SESSION['delete_error'] = "No configuration name provided.";
}

oci_close($conn);
?>
