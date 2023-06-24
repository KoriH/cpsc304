<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$memory_type = $_GET['memory_type'] ?? null;
$min_amount = $_GET['min_amount'] ?? null;
$max_amount = $_GET['max_amount'] ?? null;
$min_clock_speed = $_GET['min_clock_speed'] ?? null;
$max_clock_speed = $_GET['max_clock_speed'] ?? null;

$sql = "
    SELECT R1.Name, R1.Type, R1.Amount, R1.ClockSpeed, R2.Cost
    FROM Memory_R1 R1
    JOIN Memory_R2 R2 ON R1.Type = R2.Type AND R1.Amount = R2.Amount AND R1.ClockSpeed = R2.ClockSpeed
    WHERE (:min_cost IS NULL OR R2.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R2.Cost <= :max_cost)
    AND (:memory_type IS NULL OR R1.Type = :memory_type)
    AND (:min_amount IS NULL OR R1.Amount >= :min_amount)
    AND (:max_amount IS NULL OR R1.Amount <= :max_amount)
    AND (:min_clock_speed IS NULL OR R1.ClockSpeed >= :min_clock_speed)
    AND (:max_clock_speed IS NULL OR R1.ClockSpeed <= :max_clock_speed)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':memory_type', $memory_type);
oci_bind_by_name($stmt, ':min_amount', $min_amount);
oci_bind_by_name($stmt, ':max_amount', $max_amount);
oci_bind_by_name($stmt, ':min_clock_speed', $min_clock_speed);
oci_bind_by_name($stmt, ':max_clock_speed', $max_clock_speed);
oci_execute($stmt);

$memories = [];
while ($row = oci_fetch_assoc($stmt)) {
    $memories[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memory_name = $_POST['memory_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET Memory = :memory_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':memory_name', $memory_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: selectVideoCard.php?id={$configName}"); 
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Memory</title>
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
        .memory-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .memory-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .memory-table th,
        .memory-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .memory-table th {
            background-color: #f2f2f2;
        }
        .memory-table td button {
            margin-right: 5px;
        }
        .memory-container {
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
    <div class="memory-container">
        <h1>Select Memory</h1>
        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Memory Type:</label>
            <input type="text" name="memory_type" value="<?php echo htmlspecialchars($memory_type); ?>">
            <label>Min Amount:</label>
            <input type="number" name="min_amount" value="<?php echo htmlspecialchars($min_amount); ?>">
            <label>Max Amount:</label>
            <input type="number" name="max_amount" value="<?php echo htmlspecialchars($max_amount); ?>">
            <label>Min Clock Speed:</label>
            <input type="number" name="min_clock_speed" value="<?php echo htmlspecialchars($min_clock_speed); ?>">
            <label>Max Clock Speed:</label>
            <input type="number" name="max_clock_speed" value="<?php echo htmlspecialchars($max_clock_speed); ?>">
            <button type="submit">Filter</button>
        </form>
        <?php if (!empty($memories)) : ?>
            <table class="memory-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Clock Speed</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memories as $memory) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($memory['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($memory['TYPE']); ?></td>
                            <td><?php echo htmlspecialchars($memory['AMOUNT']); ?></td>
                            <td><?php echo htmlspecialchars($memory['CLOCKSPEED']); ?></td>
                            <td>$<?php echo htmlspecialchars($memory['COST']); ?></td>
                            <td>
                            <form method="post">
                                <input type="hidden" name="memory_name" value="<?php echo htmlspecialchars($memory['NAME']); ?>">
                                    <button type="submit">Select this Memory</button>
                                        </form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No memories found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
