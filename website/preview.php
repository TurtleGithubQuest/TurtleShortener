<?php
    $is_bot = false;
    try {
        require_once(__DIR__.'/php/db/util.php');
        $pdo = DbUtil::getPdo();
        $stmt = $pdo->prepare("SELECT url FROM urls WHERE shortcode = ?");
        $shortCode = $_GET['s'];
        $stmt->execute([$shortCode]);
        $url = $stmt->fetchColumn();

        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_bot = str_contains($userAgent, "bot");
    } catch(Exception $e) {} finally {
        if (!$is_bot)
            if (isset($url))
                header('Location: ' . $url);
            else {
                header('Location: /error.php?error=Shortened+url+not+found');
            }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>trt.ls</title>
    <meta property="og:type" content="website">
    <?php
        echo '<meta property="og:url" content="'.$url.'">';
        echo '<meta property="og:image" content="'.$url.'">';
    ?>
    <!-- todo: custom title, description-->
    <meta property="og:title" content="trt.ls">
    <meta property="og:description" content="">
</head><body></body></html>