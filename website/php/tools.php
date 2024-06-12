<?php
session_start();
$tool = $_GET["t"];
if (!isset($tool))
    exit;
if ($tool == "clear") {
    header("Location: ../index.php");
    unset($_SESSION["shortened_array"]);
    exit;
} else if ($tool == "migratedb") {
    echo require("db/migrate.php");
}
