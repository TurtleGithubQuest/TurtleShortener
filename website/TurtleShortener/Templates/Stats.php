include('head');
include('header');

<body>
<div class="index-box flex-col" style="margin: 0 5rem; z-index: 2;">
<?php
    if (!empty($geoDataRangeSummary)) {
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
    const translations = {
        'countries': "translate('countries')",
        'operating systems': "translate('os')",
        'sources': "translate('sources')",
        'cities': "translate('cities')",
    };
</script>
include('Scripts');
</body>