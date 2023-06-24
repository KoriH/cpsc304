<?php
session_start();
include 'functions.php';
include 'db_connection.php';


$user_data = check_login($conn);
$username = $user_data['USERNAME']; 


$configName = $_GET['name'];


$check_sql = "SELECT COUNT(*) FROM configuration WHERE username = :username AND ConfigurationName = :configName";
$check_stmt = oci_parse($conn, $check_sql);
oci_bind_by_name($check_stmt, ':username', $username);
oci_bind_by_name($check_stmt, ':configName', $configName);
oci_execute($check_stmt);


$count = oci_fetch_row($check_stmt)[0];


if ($count > 0) {
    echo "<script>
    alert('A configuration with the name \"$configName\" already exists for user \"$username\". Please choose a different name.');
    window.location.href = 'configuration.php';
    </script>";
    exit;
}


$defaultCooler = null;
$defaultCPU = null;
$defaultMotherboard = null;
$defaultPowerSupply = null;
$defaultVideoCard = null;
$defaultMemory = null;
$defaultSSD = null;
$defaultHDD = null;
$defaultCase = null;


$sql = "INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
        VALUES (:username, :configName, :cooler, :cpu, :motherboard, :powerSupply, :videoCard, :memory, :ssd, :hdd, :caseName)";

$stmt = oci_parse($conn, $sql);


oci_bind_by_name($stmt, ':username', $username);
oci_bind_by_name($stmt, ':configName', $configName);
oci_bind_by_name($stmt, ':cooler', $defaultCooler);
oci_bind_by_name($stmt, ':cpu', $defaultCPU);
oci_bind_by_name($stmt, ':motherboard', $defaultMotherboard);
oci_bind_by_name($stmt, ':powerSupply', $defaultPowerSupply);
oci_bind_by_name($stmt, ':videoCard', $defaultVideoCard);
oci_bind_by_name($stmt, ':memory', $defaultMemory);
oci_bind_by_name($stmt, ':ssd', $defaultSSD);
oci_bind_by_name($stmt, ':hdd', $defaultHDD);
oci_bind_by_name($stmt, ':caseName', $defaultCase);


$r = oci_execute($stmt);


if(!$r) {
    $e = oci_error($stmt);
    $_SESSION['add_error'] = "Error adding new configuration: " . htmlentities($e['message']);
    $_SESSION['add_error_sql'] = htmlentities($e['sqltext']);
    $_SESSION['add_error_offset'] = $e['offset'];
} else {
    $_SESSION['add_success'] = "New configuration added successfully.";
    header('Location: selectCPU.php?id=' . $configName);
}

oci_close($conn);


oci_close($conn);
?>
