<?php
session_start();
include 'functions.php';
include 'db_connection.php';

$user_data = check_login($conn);

$username = $user_data['USERNAME'];

// Check if delete_success session variable is set
if (isset($_SESSION['delete_success'])) {
    echo "<div class='success-message'>" . $_SESSION['delete_success'] . "</div>";
    unset($_SESSION['delete_success']);
}

// Check if delete_error session variable is set
if (isset($_SESSION['delete_error'])) {
    echo "<div class='error-message'>" . $_SESSION['delete_error'] . "</div>";
    unset($_SESSION['delete_error']);
}

// Check if add_success session variable is set
if (isset($_SESSION['add_success'])) {
    echo "<div class='success-message'>" . $_SESSION['add_success'] . "</div>";
    unset($_SESSION['add_success']);
}

// Check if add_error session variable is set
if (isset($_SESSION['add_error'])) {
    echo "<div class='error-message'>" . $_SESSION['add_error'] . "</div>";
    unset($_SESSION['add_error']);
}

$sql = "SELECT * FROM configuration WHERE username = :username";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':username', $username);
oci_execute($stmt);

$configurations = array();
while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) {
    $configurations[] = $row;
}

$sql = "
    SELECT 
        CASE WHEN UPPER(CPU) LIKE '%INTEL%' THEN 'Intel' ELSE 'AMD' END AS CPUManufacturer,
        COUNT(*) AS CPUCOUNT
    FROM configuration
    WHERE username = :username
    GROUP BY CASE WHEN UPPER(CPU) LIKE '%INTEL%' THEN 'Intel' ELSE 'AMD' END";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':username', $username);
oci_execute($stmt);

$cpuCounts = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cpuCounts[] = $row;
}

$sql = "
    SELECT 
        CASE WHEN UPPER(CPU) LIKE '%INTEL%' THEN 'Intel' ELSE 'AMD' END AS CPUManufacturer
    FROM configuration
    WHERE username = :username
    GROUP BY CASE WHEN UPPER(CPU) LIKE '%INTEL%' THEN 'Intel' ELSE 'AMD' END
    HAVING COUNT(*) > 1";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':username', $username);
oci_execute($stmt);

$cpuManufacturersMultipleBuilds = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cpuManufacturersMultipleBuilds[] = $row;
}

// Query for average configurations per user
$sql = "
    SELECT AVG(config_count) AS average_configurations_per_user
    FROM (
        SELECT COUNT(*) AS config_count 
        FROM configuration 
        GROUP BY username
    )";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':username', $username);
oci_execute($stmt);

$row = oci_fetch_assoc($stmt);
$avgConfigurationsPerUser = $row['AVERAGE_CONFIGURATIONS_PER_USER'];

// Query for finding users that have created configurations for all cases
$sql = "
    SELECT u.Username
FROM users u
WHERE NOT EXISTS (
    SELECT c.Name
    FROM Case_R1 c
    WHERE NOT EXISTS (
        SELECT *
        FROM configuration co
        WHERE co.Username = u.Username
        AND co.CaseName = c.Name
    )
)
";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':username', $username);
oci_execute($stmt);

$usersWithAllCases = [];
while ($row = oci_fetch_assoc($stmt)) {
    $usersWithAllCases[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Computer Configuration Website</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .config-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            max-width: 1000px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: auto;
            max-height: 800px;
        }
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .config-table th,
        .config-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .config-container {
    max-width: 1500px;
    min-width: 1500px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    overflow: auto;
    max-height: 700px;
    margin: 10 auto; 
}
    </style>
    <script>
        function addNewConfig() {
            var configName = prompt("Please enter the name for the new configuration:");
            if (configName == null || configName == "") {
            } else {
                window.location.href = 'createConfig.php?name=' + encodeURIComponent(configName);
            }
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Configurations</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <button onclick="window.location.href='login.php?id=<?php echo $config['id']; ?>'">Logout</button>
    <button onclick="window.location.href='passwordUpdate.php'">Update Password</button>
    <span class="navbar-text">
      Welcome <?php echo $username ?>!
    </span>
</nav>
<div class="config-container">
    <table class="config-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>CPU</th>
            <th>Cooler</th>
            <th>Motherboard</th>
            <th>RAM</th>
            <th>Video Card</th>
            <th>Storage</th>
            <th>Power Supply</th>
            <th>Case</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($configurations as $config) : ?>
            <tr>
                <td><?php echo $config['CONFIGURATIONNAME']; ?></td>
                <td><?php echo $config['CPU']; ?></td>
                <td><?php echo $config['COOLER']; ?></td>
                <td><?php echo $config['MOTHERBOARD']; ?></td>
                <td><?php echo $config['MEMORY']; ?></td>
                <td><?php echo $config['VIDEOCARD']; ?></td>
                <td>
                        <?php 
                            if (!is_null($config['SSD'])) {
                                echo $config['SSD'];
                            } elseif (!is_null($config['HDD'])) {
                                echo $config['HDD'];
                            } else {
                                
                            }
                        ?>
                    </td>
                <td><?php echo $config['POWERSUPPLY']; ?></td>
                <td><?php echo $config['CASENAME']; ?></td>
                <td>
                    <a href="deleteConfig.php?username=<?php echo $user_data['USERNAME']; ?>&ConfigurationName=<?php echo $config['CONFIGURATIONNAME']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="addNewConfig()">Add New Configuration</button>

    <h2>CPU Counts</h2>
    <table class="config-table">
        <thead>
        <tr>
            <th>CPU Manufacturer</th>
            <th>Count</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cpuCounts as $cpuCount) : ?>
            <tr>
                <td><?php echo $cpuCount['CPUMANUFACTURER']; ?></td>
                <td><?php echo $cpuCount['CPUCOUNT']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>CPU Manufacturers Used in Multiple Builds</h2>
    <table class="config-table">
        <thead>
        <tr>
            <th>CPU Manufacturer</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cpuManufacturersMultipleBuilds as $cpuManufacturer) : ?>
            <tr>
                <td><?php echo $cpuManufacturer['CPUMANUFACTURER']; ?></td>
                
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Average Configurations Per User</h2>
    <table class="config-table">
        <thead>
            <tr>
                <th>Average Configurations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $avgConfigurationsPerUser; ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Users With All Cases</h2>
    <table class="config-table">
        <thead>
            <tr>
                <th>Username</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usersWithAllCases as $user) : ?>
                <tr>
                    <td><?php echo $user['USERNAME']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
</div>
</body>
</html>
