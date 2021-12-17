<?php

if (php_sapi_name() != "cli") {
    die();
}

define('PLUGIN_MANAGER', 1);

require_once("lib.php");

$pluginsfolder = __DIR__ . '/plugins';

echo ">>> Running Plugin Manager <<<" . PHP_EOL;

$help = <<<EOD
Options:
[c]          Uses 'git clone' using 'plugins.json' to generate the proper folder structure for plugins.
[v]          Checks plugin versions.
[s]          Strips git folders in plugins folder.
[p]          Purges the plugins folder.

EOD;

echo $help . PHP_EOL;

$option = fgetc(STDIN);

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
    default;
        break;
}