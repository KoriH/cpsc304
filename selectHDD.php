<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize filter variables
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$min_capacity = $_GET['min_capacity'] ?? null;
$max_capacity = $_GET['max_capacity'] ?? null;
$rpm = $_GET['rpm'] ?? null;

$sql = "
    SELECT R1.Name, R1.Capacity, R1.RPM, R2.Cost
    FROM HDD_R1 R1
    JOIN HDD_R2 R2 ON R1.Capacity = R2.Capacity
    WHERE (:min_cost IS NULL OR R2.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R2.Cost <= :max_cost)
    AND (:rpm IS NULL OR R1.RPM = :rpm)
    AND (:min_capacity IS NULL OR R1.Capacity >= :min_capacity)
    AND (:max_capacity IS NULL OR R1.Capacity <= :max_capacity)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':rpm', $rpm);
oci_bind_by_name($stmt, ':min_capacity', $min_capacity);
oci_bind_by_name($stmt, ':max_capacity', $max_capacity);
oci_execute($stmt);

$hdds = [];
while ($row = oci_fetch_assoc($stmt)) {
    $hdds[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hdd_name = $_POST['hdd_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET HDD = :hdd_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':hdd_name', $hdd_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: selectPowerSupply.php?id={$configName}"); 
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select HDD</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .storage-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .storage-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .storage-table th,
        .storage-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .storage-table th {
            background-color: #f2f2f2;
        }
        .storage-table td button {
            margin-right: 5px;
        }
        .storage-container {
            max-width: 1080px;
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
</head>
<body>
    <div class="storage-container">
        <h1>Select HDD</h1>

        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>RPM:</label>
            <input type="text" name="rpm" value="<?php echo htmlspecialchars($rpm); ?>">
            <label>Min Capacity:</label>
            <input type="number" name="min_capacity" value="<?php echo htmlspecialchars($min_capacity); ?>">
            <label>Max Capacity:</label>
            <input type="number" name="max_capacity" value="<?php echo htmlspecialchars($max_capacity); ?>">
            <button type="submit">Filter</button>
        </form>

        <?php if (!empty($hdds)) : ?>
            <table class="storage-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>RPM</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hdds as $hdd) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($hdd['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($hdd['CAPACITY']); ?></td>
                            <td><?php echo htmlspecialchars($hdd['RPM']); ?></td>
                            <td>$<?php echo htmlspecialchars($hdd['COST']); ?></td>
                            <td>
                                <form method="post">
                                <input type="hidden" name="hdd_name" value="<?php echo htmlspecialchars($hdd['NAME']); ?>">
                                    <button type="submit">Select this HDD</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No HDDs found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
