# Moodle Plugin Manager

## Setup
Simple Moodle plugins manager for Linux systems using Git and PHP.

1. Create a `plugins.json` file based off the `plugins-dist.json`
2. Usage: `$ php pm.php` and follow onscreen instructions.
3. (optional) Create a `config.php` from the dist file.

## Usage
There's two ways of running the script:

- Run `$ php pm.php` and follow the onscreen instructions.
- If you already know the option you want to run specify it by running ` $ php pm.php -x ` where the 'x' is replaced with the correct option from the list.

### Options:
- [`c`] Uses 'git clone' using 'plugins.json' to generate the proper folder structure for plugins.
- [`v`] Checks plugin versions.
- [`s`] Strips git folders in plugins folder.
- [`p`] Purges the plugins folder.
- [`r`] Syncs the plugins folder' contents to the directory specified in config.php.
