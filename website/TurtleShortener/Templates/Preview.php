<meta charset="UTF-8">
    <title>trt.ls</title>
    <link rel="stylesheet" href="/css/turtle.css">
    <?php if(isset($isMobile) && $isMobile) {echo '<link rel="stylesheet" href="/css/mobile.css">';} ?>
    <link rel="stylesheet" href="/css/third-party/josetxu_turtle.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
</head>
<body>
include('header');
<div class="index-box flex-col" style="margin: 0 5rem; z-index: 2;">
    <div class="title">translate('url_preview')</div>
    <div class="result-table">
        <table>
          <tr style="border-top: unset;">
            <th>translate('target')</th>
            <td><?php echo '<a href="'.$shortened->url.'">'.$shortened->url.'</a>'; ?>
                <span class="copy-wrapper" title="translate('click_to_copy')" copyValue="<?= $shortened->shortenedUrl ?>">
                    <img src="/img/svg/copy.svg" alt="copy">
                    <img src="/img/svg/success.svg" alt="copy-success">
                </span>
            </td>
          </tr>
          <tr>
            <th>translate('created_at')</th>
              <?= '<td unix="' . $shortened->created . '">' . $shortened->getCreationDate() . '</td>' ?>
          </tr>
          <tr>
            <th>translate('expiration')</th>
              <?= '<td unix="' . $shortened->expiry . '">' . $shortened->getExpiryFormatted() . '</td>' ?>
          </tr>
          <tr>
            <th>translate('searchable')</th>
              <?= '<td>' . (($data['searchable'] ?? true) ? "translate('1')" : "translate('0')") . '</td>'?>
          </tr>
        </table>
    </div>
    <?php
        if (!empty($geoDataSummary)) {
            echo '
                <div id="statistics">
                <div class="title">translate("statistics")</div>
                <div id="stats_container"></div>
            </div>';
        }
    ?>
</div>
include('SeaEffects');
<script src="/js/lib/echarts.min.js"></script>
<script>
    const geoDataSummary = <?= $geoDataSummary ?? 'null' ?>;
    const translations = {
        'countries': "translate('countries')",
        'os': "translate('os')",
        'daily_visits': "translate('daily_visits')",
    };
</script>
include('Scripts');
</body>