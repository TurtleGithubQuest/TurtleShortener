<?php
namespace TurtleShortener\Misc;

require_once(__DIR__ . '/../bootstrap.php');

header('Content-type:application/json;charset=utf-8');
error_reporting(E_ERROR);

$settings = $GLOBALS['settings'];
//include_once(__DIR__ . "/utils.php");
$tokens = $settings['img_tokens'];
$img_dir = $settings['img_dir'];
$img_name_length = $settings['img_name_length'];
$img_extensions = $settings['img_extensions'];

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['secret'])) {
    $secret = $_POST['secret'];
    $status = 'ERROR';
    $uploadedName = '';
    $host = filter_input(INPUT_SERVER, 'HTTP_HOST') ?? filter_input(INPUT_SERVER, 'REQUEST URI');
    if (!isset($host))
        $message = "Could not determine host.";
    else if (in_array($secret, $tokens)) {
        $target_file = $_FILES["file"]["name"];
        $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if (is_null($target_file)) {
            $message = 'No file provided.';
        } else if (in_array($fileType, $img_extensions)) {
            $filename = substr(md5(uniqid(rand(), true)), 0, $img_name_length);
            $date = date("Ymd");
            $target_dir = '../../'.$img_dir . '/' . $date;
            if(!file_exists($target_dir))
                mkdir($target_dir, 0750, true);
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . '/' . $filename . '.' . $fileType)) {
                $status = 'OK';
                $uploadedName = $date . '/' . $filename . '.' . $fileType;
                $message = '';
            } else
                $message = 'File upload failed. Does the folder exist?';
        } else $message = 'File extension not allowed: ' . $target_file . '.';
    } else $message = 'Invalid access token.';
    $ref = $_SERVER['HTTP_REFERER'];
    // Request is probably from user interface, redirect;
    if (isset($ref)  && parse_url($ref, PHP_URL_HOST) == $host) {
        if ($status == 'OK')
            header("Location: ". $img_dir . $uploadedName);
        else
            header("Location: error.php?error=" . urlencode($message));
    } else {
        $json = ['status' => $status, 'errormsg' => $message, 'url' => $GLOBALS['utils']->getProtocol().'://'.$host.'/'.$img_dir.$uploadedName];
        echo json_encode($json);
    }
}
