<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize filter variables
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$socket_type = $_GET['socket_type'] ?? null;
$form_factor = $_GET['form_factor'] ?? null;
$min_memory_slots = $_GET['min_memory_slots'] ?? null;
$max_memory_slots = $_GET['max_memory_slots'] ?? null;
$max_memory = $_GET['max_memory'] ?? null;

$sql = "
    SELECT R2.Name, R4.MemorySlots, R3.SocketType, R3.FormFactor, R3.Cost, R2.MaxMemory
    FROM Motherboard_R2 R2
    JOIN Motherboard_R3 R3 ON R2.Name = R3.Name
    JOIN Motherboard_R4 R4 ON R3.FormFactor = R4.FormFactor
    WHERE (:min_cost IS NULL OR R3.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R3.Cost <= :max_cost)
    AND (:socket_type IS NULL OR R3.SocketType = :socket_type)
    AND (:form_factor IS NULL OR R3.FormFactor = :form_factor)
    AND (:min_memory_slots IS NULL OR R4.MemorySlots >= :min_memory_slots)
    AND (:max_memory_slots IS NULL OR R4.MemorySlots <= :max_memory_slots)
    AND (:max_memory IS NULL OR R2.MaxMemory <= :max_memory)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':socket_type', $socket_type);
oci_bind_by_name($stmt, ':form_factor', $form_factor);
oci_bind_by_name($stmt, ':min_memory_slots', $min_memory_slots);
oci_bind_by_name($stmt, ':max_memory_slots', $max_memory_slots);
oci_bind_by_name($stmt, ':max_memory', $max_memory);
oci_execute($stmt);

$motherboards = [];
while ($row = oci_fetch_assoc($stmt)) {
    $motherboards[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $motherboard_name = $_POST['motherboard_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET Motherboard = :motherboard_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':motherboard_name', $motherboard_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: selectRAM.php?id={$configName}");
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Motherboard</title>
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
        .mb-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .mb-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .mb-table th,
        .mb-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .mb-table th {
            background-color: #f2f2f2;
        }
        .mb-table td button {
            margin-right: 5px;
        }
        .mb-container {
            max-width: 1100px;
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
    <div class="mb-container">
        <h1>Select Motherboard</h1>
        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Socket Type:</label>
            <input type="text" name="socket_type" value="<?php echo htmlspecialchars($socket_type); ?>">
            <label>Form Factor:</label>
            <input type="text" name="form_factor" value="<?php echo htmlspecialchars($form_factor); ?>">
            <label>Min Memory Slots:</label>
            <input type="number" name="min_memory_slots" value="<?php echo htmlspecialchars($min_memory_slots); ?>">
            <label>Max Memory Slots:</label>
            <input type="number" name="max_memory_slots" value="<?php echo htmlspecialchars($max_memory_slots); ?>">
            <label>Max Memory:</label>
            <input type="number" name="max_memory" value="<?php echo htmlspecialchars($max_memory); ?>">
            <button type="submit">Filter</button>
        </form>
        <?php if (!empty($motherboards)) : ?>
            <table class="mb-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Memory Slots</th>
                        <th>Socket Type</th>
                        <th>Form Factor</th>
                        <th>Cost</th>
                        <th>Max Memory</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($motherboards as $mb) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mb['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($mb['MEMORYSLOTS']); ?></td>
                            <td><?php echo htmlspecialchars($mb['SOCKETTYPE']); ?></td>
                            <td><?php echo htmlspecialchars($mb['FORMFACTOR']); ?></td>
                            <td>$<?php echo htmlspecialchars($mb['COST']); ?></td>
                            <td><?php echo htmlspecialchars($mb['MAXMEMORY']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="motherboard_name" value="<?php echo htmlspecialchars($mb['NAME']); ?>">
                                    <button type="submit">Select this Motherboard</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No motherboards found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
