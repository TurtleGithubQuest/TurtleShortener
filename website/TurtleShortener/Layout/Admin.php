include('head');
include('header');

<body>
<div class="wrapper">
    <div id="wrapper-content">
        <div class="index-box flex-col">
            <form class="t-form flex-col" method="post" response-type="console">
                <label>Admin token
                    <input type="password" name="token" required>
                </label>
                <input type="submit" value="Rebuild" formaction="/tools?t=build">
                <input type="submit" value="Migrate db" formaction="/tools?t=migratedb">
                <input type="submit" value="Upkeep" formaction="/tools?t=upkeep">
            </form>
        </div>
    </div>
</div>
</body>
include('SeaEffects');
include('Scripts');