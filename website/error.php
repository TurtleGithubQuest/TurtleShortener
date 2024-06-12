<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error<?php if(isset($_SESSION['error_code'])) echo ": ".$_SESSION['error_code']; ?></title>
    <link rel="stylesheet" href="css/turtle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
</head>
<body>
    <div style="display: flex; align-items: center; text-align: center; flex-flow: column; margin-top: 1%; font-size: x-large">
        <div class="JetBrainsMono_Italic" style="margin: 1rem 0">
        <?php
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            } else if(isset($_GET['error']))
                echo $_GET['error'];
            else echo "404: Not found";
        ?>
        </div>
        <img src="img/jfif/turtle_0.jfif" alt="turtle" width="50%" style="border-radius:0.3rem">
    </div>
</body>
</html>