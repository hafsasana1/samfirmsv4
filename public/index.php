<?php

declare(strict_types=1);

/*
 * CodeIgniter 4 - Public/index.php
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our Routes and
 * fires up the app.
 */

// Load the paths config file
// This is the line that might need to be changed, depending on your folder structure.
$pathsConfig = FCPATH . '../app/Config/Paths.php';
// ^^^ Change this if you move your application folder

require $pathsConfig;

$paths = new Config\Paths();

// Location of the framework bootstrap file.
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));