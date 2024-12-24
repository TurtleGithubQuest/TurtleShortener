<?php

declare(strict_types=1);

global $img_extensions;

?>

include('head');
include('header');

<body>

<div class="index-box flex-col">
    <div class="title">translate('turtle_images')</div>
    <form class="t-form flex-col" action="upload" method="post" enctype="multipart/form-data" style="border: 4px dashed #fff; min-width: 15rem">
        <label><input type="password" name="secret" placeholder="translate('placeholder_access_token')" autocomplete="access_token" required style="z-index:1; text-align: center"></label>
        <input type="file" name="file" id="fileInput" spellcheck="false" maxlength="2083" required>
        <div id="dropZone" class="drop-zone">
            <p>translate('drag_and_drop_here')</p>
            <sub>translate('supported_extensions'): <?= '[' . implode(', ', $img_extensions) . ']' ?></sub>
        </div>
        <input type="submit" value="Upload" style="z-index: 1;">
    </form>
    <?php
        if (isset($_GET['error'])) {
            echo 'Error: ' . $_GET['error'];
        }
    ?>
</div>

include('SeaEffects');
include('Scripts');
</body>