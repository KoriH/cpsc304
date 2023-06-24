<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize filter variables
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$brand = $_GET['brand'] ?? null;
$min_memory = $_GET['min_memory'] ?? null;
$max_memory = $_GET['max_memory'] ?? null;
$memory_type = $_GET['memory_type'] ?? null;
$min_wattage = $_GET['min_wattage'] ?? null;
$max_wattage = $_GET['max_wattage'] ?? null;

$sql = "
    SELECT R2.Memory, R3.Name, R3.Wattage, R4.Brand, R3.MemoryType, R2.Cost
    FROM VideoCard_R2 R2
    JOIN VideoCard_R3 R3 ON R2.Memory = R3.Memory
    JOIN VideoCard_R4 R4 ON R3.Name = R4.Name
    WHERE (:min_cost IS NULL OR R2.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R2.Cost <= :max_cost)
    AND (:brand IS NULL OR R4.Brand = :brand)
    AND (:min_memory IS NULL OR R2.Memory >= :min_memory)
    AND (:max_memory IS NULL OR R2.Memory <= :max_memory)
    AND (:memory_type IS NULL OR R3.MemoryType = :memory_type)
    AND (:min_wattage IS NULL OR R3.Wattage >= :min_wattage)
    AND (:max_wattage IS NULL OR R3.Wattage <= :max_wattage)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':brand', $brand);
oci_bind_by_name($stmt, ':min_memory', $min_memory);
oci_bind_by_name($stmt, ':max_memory', $max_memory);
oci_bind_by_name($stmt, ':memory_type', $memory_type);
oci_bind_by_name($stmt, ':min_wattage', $min_wattage);
oci_bind_by_name($stmt, ':max_wattage', $max_wattage);
oci_execute($stmt);

$videoCards = [];
while ($row = oci_fetch_assoc($stmt)) {
    $videoCards[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $video_card_name = $_POST['video_card_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET VideoCard = :video_card_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':video_card_name', $video_card_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: selectStorage.php?id={$configName}"); 
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Video Card</title>
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
        .vc-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .vc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .vc-table th,
        .vc-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .vc-table th {
            background-color: #f2f2f2;
        }
        .vc-table td button {
            margin-right: 5px;
        }
        .vc-container {
            max-width: 1400px;
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
    <div class="vc-container">
        <h1>Select Video Card</h1>

        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Brand:</label>
            <input type="text" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
            <label>Min Memory:</label>
            <input type="number" name="min_memory" value="<?php echo htmlspecialchars($min_memory); ?>">
            <label>Max Memory:</label>
            <input type="number" name="max_memory" value="<?php echo htmlspecialchars($max_memory); ?>">
            <label>Memory Type:</label>
            <input type="text" name="memory_type" value="<?php echo htmlspecialchars($memory_type); ?>">
            <label>Min Wattage:</label>
            <input type="number" name="min_wattage" value="<?php echo htmlspecialchars($min_wattage); ?>">
            <label>Max Wattage:</label>
            <input type="number" name="max_wattage" value="<?php echo htmlspecialchars($max_wattage); ?>">
            <button type="submit">Filter</button>
        </form>

        <?php if (!empty($videoCards)) : ?>
            <table class="vc-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Memory</th>
                        <th>Memory Type</th>
                        <th>Wattage</th>
                        <th>Brand</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videoCards as $vc) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vc['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($vc['MEMORY']); ?> GB</td>
                            <td><?php echo htmlspecialchars($vc['MEMORYTYPE']); ?></td>
                            <td><?php echo htmlspecialchars($vc['WATTAGE']); ?> W</td>
                            <td><?php echo htmlspecialchars($vc['BRAND']); ?></td>
                            <td>$<?php echo htmlspecialchars($vc['COST']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="video_card_name" value="<?php echo htmlspecialchars($vc['NAME']); ?>">
                                    <button type="submit">Select this Video Card</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No video cards found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
