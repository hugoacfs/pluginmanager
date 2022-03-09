<?php

if (!defined('PLUGIN_MANAGER')) {
    die;
}

function logoutput(string $message, string $header = 'INFO') {
    $logsfolder = __DIR__ . '/logs';
    if (!file_exists($logsfolder)) {
        mkdir($logsfolder);
    }
    $filename = "$logsfolder/" . date('Ymd') . '.log';
    $line = date('\[Y-m-d\_H:i:s\]') . "[$header]: $message \n";
    $log = fopen($filename, 'a+');
    fwrite($log, $line);
    fclose($log);
}

function gitclone (string $pluginsfolder) {
    $plugins = file_get_contents('plugins.json');
    $plugins = json_decode($plugins);

    /**
     * We do community and custom plugins differently,
     * because we might want to change how we treat
     * different plugins.
     */

    foreach ($plugins->community as $plugin) {
        $output = '';
        $status = 'INFO';
        $output = shell_exec("git clone --depth=1 -b '$plugin->branch' $plugin->repo $pluginsfolder/$plugin->path/$plugin->name 2>&1");
        if (isset($plugin->commit)) {
            $output = shell_exec("git -C $pluginsfolder/$plugin->path/$plugin->name fetch origin $plugin->commit 2>&1");
            $output = shell_exec("git -C $pluginsfolder/$plugin->path/$plugin->name checkout $plugin->commit 2>&1");
        }
        if ($output == null) {
            $output = "No output detected for $plugin->name" . PHP_EOL;
            $status = 'ERROR';
        }
        echo $output;
        logoutput($output, $status);
    }

    foreach ($plugins->custom as $plugin) {
        $output = '';
        $status = 'INFO';
        $output = shell_exec("git clone --depth=1 -b '$plugin->branch' $plugin->repo $pluginsfolder/$plugin->path/$plugin->name 2>&1");
        if (isset($plugin->commit)) {
            $output = shell_exec("git -C $pluginsfolder/$plugin->path/$plugin->name fetch origin $plugin->commit 2>&1");
            $output = shell_exec("git -C $pluginsfolder/$plugin->path/$plugin->name checkout $plugin->commit 2>&1");
        }
        if ($output == null) {
            $output = "No output detected for $plugin->name" . PHP_EOL;
            $status = 'ERROR';
        }
        echo $output;
        logoutput($output, $status);
    }
}

function checkversions (string $pluginsfolder) {
    define('MOODLE_INTERNAL', true);
    require('component.php');
    $plugins = file_get_contents('plugins.json');
    $plugins = json_decode($plugins);
    echo "Checking versions..." . PHP_EOL;

    $requiresattention = [];

    foreach ($plugins->community as $k => $p) {
        $output = '';
        $status = 'INFO';
        $plugin = new stdClass;
        include("$pluginsfolder/$p->path/$p->name/version.php");
        if ($p->version != $plugin->version) {
            $p->versiondotphp = $plugin->version;
            $requiresattention[$k] = $p;
            $status = 'WARNING';
            $output .= ">WARNING<      $p->name" . PHP_EOL;
            $output .= "  JSON:           $p->version" . PHP_EOL;
            $output .= "  DOWNLOADED:     $plugin->version" . PHP_EOL;
            $output .= "  VCS:            $p->repo" . PHP_EOL;
        } else {
            $output .= ">OK<           $p->name" . PHP_EOL;
        }
        echo $output;
        logoutput($output, $status);
    }

    foreach ($plugins->custom as $k => $p) {
        $output = '';
        $status = 'INFO';
        $plugin = new stdClass;
        include("$pluginsfolder/$p->path/$p->name/version.php");
        if ($p->version != $plugin->version) {
            $p->versiondotphp = $plugin->version;
            $requiresattention[$k] = $p;
            $status = 'WARNING';
            $output .= ">WARNING<      $p->name" . PHP_EOL;
            $output .= "  JSON:           $p->version" . PHP_EOL;
            $output .= "  DOWNLOADED:     $plugin->version" . PHP_EOL;
            $output .= "  VCS:            $p->repo" . PHP_EOL;
        } else {
            $output .= ">OK<           $p->name" . PHP_EOL;
        }
        echo $output;
        logoutput($output, $status);
    }

    $requiresattention = json_encode($requiresattention, JSON_UNESCAPED_SLASHES);
    file_put_contents('requiresattention.json',$requiresattention);
}

function purgefolder (string $pluginsfolder) {
    if (!file_exists($pluginsfolder)) {
        echo "Folder does not exist." . PHP_EOL;
        return;
    }
    echo "Running: sudo rm -r $pluginsfolder" . PHP_EOL;
    echo exec("sudo rm -r $pluginsfolder");
}

function stripgit (string $pluginsfolder) {
    echo exec("find $pluginsfolder -name .git -exec rm -rf {} \;");
}

function syncplugins (string $pluginsfolder, string $outputfolder) {
     if (!file_exists($pluginsfolder)) {
        $message = "Plugins folder does not exist, aborting" . PHP_EOL;
        $status = 'ERROR';
        logoutput($message, $status);
        echo $message;
        return;
    }
     if (!file_exists($outputfolder)) {
        $message = "Output folder does not exist." . PHP_EOL;
        $status = 'ERROR';
        logoutput($message, $status);
        echo $message;
        return;
    }
    if (strlen($outputfolder) <= 1) {
        $message = 'Issues finding output folder, aborting.';
        $status = 'ERROR';
        logoutput($message, $status);
        echo $message;
        return;
    }
    $status = 'INFO';
    echo exec("rsync -avz $pluginsfolder/ $outputfolder/ ");
}