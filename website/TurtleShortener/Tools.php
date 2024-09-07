<?php
namespace TurtleShortener;

session_start();
$tool = $_GET["t"];
if (!isset($tool)) {
    exit;
}
$tool = strtolower($tool);
if ($tool === "clear") {
    header("Location: ../");
    unset($_SESSION["shortened_array"]);
    exit;
}

if ($tool === "migratedb") {
    echo require("Database/Migrate.php");
} else if ($tool === "upkeep") {
    echo require("Database/UpKeep.php");
} else if ($tool === "build") {
    echo require("Admin/Build.php");
}
