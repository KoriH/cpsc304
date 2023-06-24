<?php
include 'db_connection.php';

$configName = $_GET['id'];

$sql = "
    SELECT 'SSD' AS StorageType, COUNT(*) AS Count
    FROM SSD_R1
    UNION ALL
    SELECT 'HDD' AS StorageType, COUNT(*) AS Count
    FROM HDD_R1";

$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

$storageCounts = [];
while ($row = oci_fetch_assoc($stmt)) {
    $storageCounts[] = $row;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Storage</title>
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
    <div class="storage-container">
        <h1>Select Storage</h1>
        <table class="storage-table">
            <thead>
                <tr>
                    <th>Storage Type</th>
                    <th>Number of Options</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($storageCounts as $storageCount) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($storageCount['STORAGETYPE']); ?></td>
                        <td><?php echo htmlspecialchars($storageCount['COUNT']); ?></td>
                        <td>
                            <form method="get" action="<?php echo htmlspecialchars($storageCount['STORAGETYPE'] === 'SSD' ? "selectSSD.php" : "selectHDD.php"); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($configName); ?>">
                                <button type="submit">Select <?php echo htmlspecialchars($storageCount['STORAGETYPE']); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</body>
</html>
