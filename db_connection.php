<?php # connection header file for database access (reduced code duplication)

$db_username = 'ora_kori0909';
$db_password = 'a33788043';
$db_connection_string = 'dbhost.students.cs.ubc.ca:1522/stu';

$conn = oci_connect($db_username, $db_password, $db_connection_string);

if (!$conn) {
    $error_message = oci_error();
    die("Connection failed: " . $error_message['message']);
}

?>