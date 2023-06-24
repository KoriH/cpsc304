<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize min and max values
$min_speed = $_GET['min_speed'] ?? null;
$max_speed = $_GET['max_speed'] ?? null;
$min_wattage = $_GET['min_wattage'] ?? null;
$max_wattage = $_GET['max_wattage'] ?? null;
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;

// Prepare SQL
$sql = "
    SELECT R1.Name, R1.Fanspeed, R2.Wattage, R2.Cost
    FROM Cooler_R1 R1
    JOIN Cooler_R2 R2 ON R1.Fanspeed = R2.Fanspeed
    WHERE (:min_speed IS NULL OR R1.Fanspeed >= :min_speed)
    AND (:max_speed IS NULL OR R1.Fanspeed <= :max_speed)
    AND (:min_wattage IS NULL OR R2.Wattage >= :min_wattage)
    AND (:max_wattage IS NULL OR R2.Wattage <= :max_wattage)
    AND (:min_cost IS NULL OR R2.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R2.Cost <= :max_cost)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_speed', $min_speed);
oci_bind_by_name($stmt, ':max_speed', $max_speed);
oci_bind_by_name($stmt, ':min_wattage', $min_wattage);
oci_bind_by_name($stmt, ':max_wattage', $max_wattage);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_execute($stmt);

$coolers = [];
while ($row = oci_fetch_assoc($stmt)) {
    $coolers[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cooler_name = $_POST['cooler_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET Cooler = :cooler_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':cooler_name', $cooler_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);
    oci_execute($stmt);

    header("Location: selectMotherboard.php?id={$configName}");
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Cooler</title>
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
        .cooler-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .cooler-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cooler-table th,
        .cooler-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .cooler-table th {
            background-color: #f2f2f2;
        }
        .cooler-table td button {
            margin-right: 5px;
        }

        .cooler-container {
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
    <div class="cooler-container">
        <h1>Select Cooler</h1>

        <form method="get">
            <label>Min Fan Speed:</label>
            <input type="number" name="min_speed" value="<?php echo htmlspecialchars($min_speed); ?>">
            <label>Max Fan Speed:</label>
            <input type="number" name="max_speed" value="<?php echo htmlspecialchars($max_speed); ?>">
            <label>Min Wattage:</label>
            <input type="number" name="min_wattage" value="<?php echo htmlspecialchars($min_wattage); ?>">
            <label>Max Wattage:</label>
            <input type="number" name="max_wattage" value="<?php echo htmlspecialchars($max_wattage); ?>">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <button type="submit">Filter</button>
        </form>

        
        <?php if (!empty($coolers)) : ?>
            <table class="cooler-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Fan Speed</th>
                        <th>Wattage</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coolers as $cooler) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cooler['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($cooler['FANSPEED']); ?></td>
                            <td><?php echo htmlspecialchars($cooler['WATTAGE']); ?></td>
                            <td>$<?php echo htmlspecialchars($cooler['COST']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="cooler_name" value="<?php echo htmlspecialchars($cooler['NAME']); ?>">
                                    <button type="submit">Select this Cooler</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No coolers found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
