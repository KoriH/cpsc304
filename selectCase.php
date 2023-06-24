<?php
session_start();
include 'db_connection.php';
include 'functions.php';

$user_data = check_login($conn);
$username = $user_data['USERNAME'];

// Initialize min and max values
$min_cost = $_GET['min_cost'] ?? null;
$max_cost = $_GET['max_cost'] ?? null;
$type = $_GET['type'] ?? null;
$form_factor = $_GET['form_factor'] ?? null;

$sql = "
    SELECT R1.Name, R1.Type, R2.FormFactor, R1.Cost
    FROM Case_R1 R1
    JOIN Case_R2 R2 ON R1.Type = R2.Type
    WHERE (:min_cost IS NULL OR R1.Cost >= :min_cost)
    AND (:max_cost IS NULL OR R1.Cost <= :max_cost)
    AND (:type IS NULL OR R1.Type = :type)
    AND (:form_factor IS NULL OR R2.FormFactor = :form_factor)";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':min_cost', $min_cost);
oci_bind_by_name($stmt, ':max_cost', $max_cost);
oci_bind_by_name($stmt, ':type', $type);
oci_bind_by_name($stmt, ':form_factor', $form_factor);
oci_execute($stmt);

$cases = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cases[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $case_name = $_POST['case_name'];
    $configName = $_GET['id'];

    $sql = "UPDATE configuration SET CaseName = :case_name WHERE ConfigurationName = :configName AND Username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':case_name', $case_name);
    oci_bind_by_name($stmt, ':configName', $configName);
    oci_bind_by_name($stmt, ':username', $username);  
    oci_execute($stmt);

    header("Location: configuration.php?id={$configName}"); 
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Case</title>
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
        .case-container {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .case-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .case-table th,
        .case-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .case-table th {
            background-color: #f2f2f2;
        }
        .case-table td button {
            margin-right: 5px;
        }

        .case-container {
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
    <div class="case-container">
        <h1>Select Case</h1>
        <form method="get">
            <label>Min Cost:</label>
            <input type="number" name="min_cost" value="<?php echo htmlspecialchars($min_cost); ?>">
            <label>Max Cost:</label>
            <input type="number" name="max_cost" value="<?php echo htmlspecialchars($max_cost); ?>">
            <label>Type:</label>
            <input type="text" name="type" value="<?php echo htmlspecialchars($type); ?>">
            <label>Form Factor:</label>
            <input type="text" name="form_factor" value="<?php echo htmlspecialchars($form_factor); ?>">
            <button type="submit">Filter</button>
        </form>
        <?php if (!empty($cases)) : ?>
            <table class="case-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Form Factor</th>
                        <th>Cost</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cases as $case) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($case['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($case['TYPE']); ?></td>
                            <td><?php echo htmlspecialchars($case['FORMFACTOR']); ?></td>
                            <td>$<?php echo htmlspecialchars($case['COST']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="case_name" value="<?php echo htmlspecialchars($case['NAME']); ?>">
                                    <button type="submit">Select this Case</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No cases found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
