<?php
namespace Website\Php;

session_start();
$tool = $_GET["t"];
if (!isset($tool))
    exit;
if ($tool == "clear") {
    header("Location: ../");
    unset($_SESSION["shortened_array"]);
    exit;
} else if ($tool == "migratedb") {
    echo require("db/migrate.php");
} else if ($tool == "upkeep") {
    echo require("db/upkeep.php");
}
