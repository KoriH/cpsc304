<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize filter variables
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$min_wattage = $_GET['min_wattage'] ?? null;
$max_wattage = $_GET['max_wattage'] ?? null;
$rating = $_GET['rating'] ?? null;

$sql = "
    SELECT R1.Name, R1.Rating, R1.Wattage, R2.Cost
    FROM PowerSupply_R1 R1
    JOIN PowerSupply_R2 R2 ON R1.Rating = R2.Rating AND R1.Wattage = R2.Wattage
    WHERE (:min_cost IS NULL OR R2.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R2.Cost <= :max_cost)
    AND (:rating IS NULL OR R1.Rating = :rating)
    AND (:min_wattage IS NULL OR R1.Wattage >= :min_wattage)
    AND (:max_wattage IS NULL OR R1.Wattage <= :max_wattage)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':rating', $rating);
oci_bind_by_name($stmt, ':min_wattage', $min_wattage);
oci_bind_by_name($stmt, ':max_wattage', $max_wattage);
oci_execute($stmt);

$powerSupplies = [];
while ($row = oci_fetch_assoc($stmt)) {
    $powerSupplies[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $psu_name = $_POST['psu_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET PowerSupply = :psu_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':psu_name', $psu_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: selectCase.php?id={$configName}"); 
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Power Supply</title>
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
        .power-supply-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .power-supply-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .power-supply-table th,
        .power-supply-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .power-supply-table th {
            background-color: #f2f2f2;
        }
        .power-supply-table td button {
            margin-right: 5px;
        }
        .power-supply-container {
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
    <div class="power-supply-container">
        <h1>Select Power Supply</h1>

        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Rating:</label>
            <input type="text" name="rating" value="<?php echo htmlspecialchars($rating); ?>">
            <label>Min Wattage:</label>
            <input type="number" name="min_wattage" value="<?php echo htmlspecialchars($min_wattage); ?>">
            <label>Max Wattage:</label>
            <input type="number" name="max_wattage" value="<?php echo htmlspecialchars($max_wattage); ?>">
            <button type="submit">Filter</button>
        </form>

        <?php if (!empty($powerSupplies)) : ?>
            <table class="power-supply-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Wattage</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($powerSupplies as $powerSupply) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($powerSupply['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($powerSupply['RATING']); ?></td>
                            <td><?php echo htmlspecialchars($powerSupply['WATTAGE']); ?></td>
                            <td>$<?php echo htmlspecialchars($powerSupply['COST']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="psu_name" value="<?php echo htmlspecialchars($powerSupply['NAME']); ?>">
                                    <button type="submit">Select this Power Supply</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No power supplies found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
