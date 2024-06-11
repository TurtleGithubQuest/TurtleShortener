<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error: <?php echo $_SESSION['error_code']; ?></title>
    <link rel="stylesheet" href="css/turtle.css">
</head>
<body>
    <div style="display: flex; align-items: center; text-align: center; flex-flow: column; margin-top: 1%; font-size: x-large">
        <div class="JetBrainsMono_Italic" style="margin: 1rem 0">
        <?php
            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            } else echo "404: Not found";
        ?>
        </div>
        <img src="jfif/turtle_0.jfif" alt="turtle" width="50%" style="border-radius:0.3rem">
    </div>
</body>
</html>