<?php
namespace TurtleShortener\Error;
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
<body style="overflow: hidden">
    <div class="error-wrapper">
        <div class="text">
        <?php
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            } else if(isset($_GET['error']))
                echo $_GET['error'];
            else echo "404: Not found";
        ?>
        </div>
        <div class="error-img"><img src="img/jfif/turtle_0.jfif" alt="turtle"></div>
    </div>
</body>
</html>
