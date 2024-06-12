<?php
header('Content-type:application/json;charset=utf-8');
error_reporting(E_ERROR);
$settings = require_once(__DIR__ . '/php/settings.php');
$tokens = $settings['img_tokens'];
$img_dir = $settings['img_dir'];
$img_name_length = $settings['img_name_length'];
$img_extensions = $settings['img_extensions'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['secret'])) {
    $secret = $_POST['secret'];
    if (in_array($secret, $tokens)) {
        $filename = substr(md5(uniqid(rand(), true)), 0, $img_name_length);
        $target_file = $_FILES["file"]["name"];
        $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if (in_array($fileType, $img_extensions)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $img_dir . $filename . '.' . $fileType))
                $json = ['status' => 'OK', 'errormsg' => '', 'url' => $filename . '.' . $fileType];
            else
                $json = ['status' => 'ERROR', 'errormsg' => '', 'url' => 'File upload failed. Does the folder exist and did you CHMOD the folder?'];
        } else $json = ['status' => 'ERROR', 'errormsg' => '', 'url' => 'File extension not allowed: ' . $fileType . '.'];
    } else $json = ['status' => 'ERROR', 'errormsg' => '', 'url' => 'Invalid secret key.'];

    echo(json_encode($json));
}