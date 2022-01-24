<?php

if (php_sapi_name() != "cli") {
    die();
}

define('PLUGIN_MANAGER', 1);

// Setting default var.
$OUTPUTFOLDER = './output';
if (file_exists("config.php")) {
    require_once("config.php");
}
require_once("lib.php");

$pluginsfolder = __DIR__ . '/plugins';
$outputfolder = $OUTPUTFOLDER ?? '';

echo ">>> Running Plugin Manager <<<" . PHP_EOL;

$help = <<<EOD
Options:
[c]          Uses 'git clone' using 'plugins.json' to generate the proper folder structure for plugins.
[v]          Checks plugin versions.
[s]          Strips git folders in plugins folder.
[p]          Purges the plugins folder.
[r]          Syncs the plugins folder' contents to the directory specified in config.php.

EOD;

if (!isset($argv[1])) {
    echo $help . PHP_EOL;
    echo 'Select an option to continue: ';
    $option = fgetc(STDIN);
} else {
    // We only support one option at a time.
    // Position 0 = script name (pm.php).
    $option = str_replace('-', '',  $argv[1]);
    if ($option == 'h') {
        echo $help . PHP_EOL;
        exit;
    }
}

switch($option) {
    case 'c':
        gitclone($pluginsfolder);
        break;
    case 'v':
        checkversions($pluginsfolder);
        break;
    case 's':
        stripgit($pluginsfolder);
        break;
    case 'p':
        purgefolder($pluginsfolder);
        break;
    case 'r':
        syncplugins($pluginsfolder, $outputfolder);
        break;
    default;
        echo 'Function not supported, use -h for help.';
        break;
}