<?php
session_start();
$settings = require_once(__DIR__ . '/php/settings.php');
$img_name_length = $settings['img_name_length'];
$img_extensions = $settings['img_extensions'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>trt.ls</title>
    <link rel="stylesheet" href="css/turtle.css">
</head>
<body>
<div class="turtle-box" style="position:absolute; top: 20%; right: 2%; opacity: 80%">
    <div class="bubbles">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
        <div class="bubble b6"></div>
        <div class="bubble b7"></div>
        <div class="bubble b8"></div>
        <div class="bubble b9"></div>
        <div class="bubble b10"></div>
    </div>
    <div class="bubbles mirror">
        <div class="bubble b1"></div>
        <div class="bubble b2"></div>
        <div class="bubble b3"></div>
        <div class="bubble b4"></div>
        <div class="bubble b5"></div>
        <div class="bubble b6"></div>
        <div class="bubble b7"></div>
        <div class="bubble b8"></div>
        <div class="bubble b9"></div>
        <div class="bubble b10"></div>
    </div>
    <div class="turtle">
        <div class="head"><div class="eyes"></div></div>
        <div class="leg1"></div>
        <div class="leg2"></div>
        <div class="leg3"></div>
        <div class="leg4"></div>
        <div class="tail"></div>
        <div class="body"><span></span></div>
        <div class="body-tail"></div>
    </div>
</div>
<div class="index-box flex-col">
    <div class="title">Turtle Images</div>
    <form class="t-form flex-col" action="upload.php" method="post" style="border: 4px dashed #fff; min-width: 15rem">
        <input type="file" name="file" spellcheck="false" maxlength="2083" required>
        <input type="text" name="secret" placeholder="your access token" required style="z-index:1; text-align: center">
        <p>Drag and drop here</p>
        <sub>Supported extensions: <?php echo "[".implode(", ", $img_extensions)."]"; ?></sub>
        <input type="submit" value="Upload" style="z-index: 1;">
    </form>
</div>
</body>
</html>