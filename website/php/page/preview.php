<?php
    $is_bot = false;
    $preview_mode = $_GET['preview'] ?? false;
    try {
        if (isset($_GET['s'])) {
            require_once(__DIR__ . '/php/db/util.php');
            $pdo = DbUtil::getPdo();
            $query = $preview_mode ?
                "SELECT shortcode, url, expiry, created FROM urls WHERE shortcode = ?":
                "SELECT url FROM urls WHERE shortcode = ?";
            $stmt = $pdo->prepare($query);
            $shortCode = $_GET['s'];
            $stmt->execute([$shortCode]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (is_array($data))
                $url = $data['url'];
            $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is_bot = str_contains($userAgent, "bot");
        }
    } catch(Exception $e) {} finally {
        if (!empty($url)) {
            if (!$is_bot && !$preview_mode)
                header('Location: ' . $url);
        } else header('Location: /error.php?error=Shortened+url+not+found');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta property="og:type" content="website">
    <?php
        $title = "trt.ls";
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent: TurtleBot (trt.ls)'
            )
        );
        try {
            $html = @file_get_contents($url, false, stream_context_create($options));
            preg_match_all('~<title>([^<]*)</title>|<meta property="og:description" content="([^<]*)"~i', $html, $matches);
            $title = !empty($matches[1][0]) ? $matches[1][0] : null;
            $description = !empty($matches[2][0]) ? $matches[2][0] : null;
        } catch(ValueError $err) {
            echo 'Could not fetch the webpage content';
        } finally {
            echo '<title>'.$title.'</title>';
        }
        echo '<meta property="og:url" content="'.$url.'">';
        echo '<meta property="og:image" content="'.$url.'">';
        echo '<meta property="og:title" content="'.$title.'">';
        if (isset($description))
            echo '<meta property="og:description" content="'.$description.'">';
        if ($preview_mode) {
            echo '
                <link rel="stylesheet" href="css/turtle.css">
                <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
                <script src="js/turtle.js"></script>
            ';
        }
    ?>
</head><body>
<?php
if ($preview_mode) {
    /*if (empty($data)) {
        echo 'Error fetching data';
        exit;
    }*/
    require_once(__DIR__ . '/php/model/short.php');
    try {
        $shortened = new Shortened($data['shortcode'], "", $url, $data['expiry'], $data['created']);
    } catch (Exception $e) {
        echo "Error fetching data.";
        exit;
    }
    echo '<div class="index-box flex-col" style="top: 40%; margin: 0 5rem;"><div class="result-table">
        <div>Shortened url preview</div>
        <table>
          <tr>
            <th>target</th>
            <td><a href="'.$url.'">'.$url.'</a>
                <span class="copy-wrapper" title="click to copy url" onclick="copyValue(this, \''.$url.'\')">
                    <img src="img/svg/copy.svg" alt="copy">
                    <img src="img/svg/success.svg" alt="copy-success">
                </span>
            </td>
          </tr>
          <tr>
            <th>created at</th>
            <td unix="'.$shortened->created.'">'.$shortened->getCreationDate().'</td>
          </tr>
          <tr>
            <th>expiration</th>
            <td unix="'.$shortened->expiry.'">'.$shortened->getExpiryFormatted().'</td>
          </tr>
        </table></div></div>
    ';
}
?>
</body></html>