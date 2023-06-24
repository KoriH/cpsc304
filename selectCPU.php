<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize min and max values
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$min_core = $_GET['min_core'] ?? null;
$max_core = $_GET['max_core'] ?? null;

// Prepare SQL
$sql = "
    SELECT R1.Name, R1.hasStockCooler, R2.BaseClock, R2.CoreCount, R1.Cost, R1.Wattage
    FROM CPU_R1 R1
    JOIN CPU_R2 R2 ON R1.Name = R2.Name
    WHERE (:min_cost IS NULL OR R1.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R1.Cost <= :max_cost)
    AND (:min_core IS NULL OR R2.CoreCount >= :min_core)
    AND (:max_core IS NULL OR R2.CoreCount <= :max_core)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':min_core', $min_core);
oci_bind_by_name($stmt, ':max_core', $max_core);
oci_execute($stmt);

$cpus = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cpus[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpu_name = $_POST['cpu_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET CPU = :cpu_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':cpu_name', $cpu_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);
    oci_execute($stmt);

    header("Location: selectCooler.php?id={$configName}");
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select CPU</title>
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
        .cpu-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .cpu-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cpu-table th,
        .cpu-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .cpu-table th {
            background-color: #f2f2f2;
        }
        .cpu-table td button {
            margin-right: 5px;
        }

        .cpu-container {
            max-width: 1200px;
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
    <div class="cpu-container">
        <h1>Select CPU</h1>

        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Min Core Count:</label>
            <input type="number" name="min_core" value="<?php echo htmlspecialchars($min_core); ?>">
            <label>Max Core Count:</label>
            <input type="number" name="max_core" value="<?php echo htmlspecialchars($max_core); ?>">
            <button type="submit">Filter</button>
        </form>

        <?php if (!empty($cpus)) : ?>
            <table class="cpu-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Has Stock Cooler</th>
                        <th>Base Clock</th>
                        <th>Core Count</th>
                        <th>Cost</th>
                        <th>Wattage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cpus as $cpu) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cpu['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($cpu['HASSTOCKCOOLER'] ? 'Yes' : 'No'); ?></td>
                            <td><?php echo htmlspecialchars($cpu['BASECLOCK']); ?></td>
                            <td><?php echo htmlspecialchars($cpu['CORECOUNT']); ?></td>
                            <td>$<?php echo htmlspecialchars($cpu['COST']); ?></td>
                            <td><?php echo htmlspecialchars($cpu['WATTAGE']); ?> W</td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cpu_name" value="<?php echo htmlspecialchars($cpu['NAME']); ?>">
                                    <button type="submit">Select this CPU</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No CPUs found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
