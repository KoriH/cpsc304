<?php
session_start();
include 'functions.php';
include 'db_connection.php';

$user_data = check_login($conn);

$configuration_name = $_GET['ConfigurationName'];

$sql = "SELECT * FROM yourComputerTable WHERE username = :username AND ConfigurationName = :ConfigurationName";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':ConfigurationName', $ConfigurationName);
oci_bind_by_name($stmt, ':username', $user_data['username']);
oci_execute($stmt);

$computer = oci_fetch_assoc($stmt);


oci_close($conn);
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
    <style>
        .component {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .component h2, .component p, .component button {
            margin: 0;
        }

        .component p {
            width: 200px; 
            text-align: left;
        }
    </style>

</head>
<body>
    <h1>Computer Components</h1>

    <div class="container">
        <div class="component">
            <h2>CPU</h2>
            <p><?php echo $computer['CPU'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectCPU.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Cooler</h2>
            <p><?php echo $computer['CPU Cooler'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectCooler.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Motherboard</h2>
            <p><?php echo $computer['Motherboard'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectMotherboard.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Video Card</h2>
            <p><?php echo $computer['Video Card'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectVideoCard.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>RAM</h2>
            <p><?php echo $computer['RAM'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectRAM.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Storage</h2>
            <p><?php echo $computer['Storage'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectStorage.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Power Supply</h2>
            <p><?php echo $computer['Power Supply'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectPowerSupply.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>

        <div class="component">
            <h2>Case</h2>
            <p><?php echo $computer['Case'] ?? 'No component selected'; ?></p>
            <button onclick="window.location.href='selectCase.php?id=<?php echo $computer_id; ?>'">Select a part</button>
        </div>
        
        <button onclick="window.location.href='configuration.php?id=<?php echo $computer_id; ?>'">Return to configurations</button>
    </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
