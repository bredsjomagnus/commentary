<?php
/**
 * Define essential Anax paths, end with /
 */
define("ANAX_INSTALL_PATH", realpath(__DIR__ . "/.."));
define("ANAX_APP_PATH", ANAX_INSTALL_PATH);

/**
 * Sample configuration file for test configuration.
 */
require ANAX_INSTALL_PATH . "/config/databaseconfig.php";

/**
 * Include autoloader.
 */
require ANAX_INSTALL_PATH . "/vendor/autoload.php";

/**
 * Include other files to test, for example mock files.
 */
// require ANAX_INSTALL_PATH . "/vendor/anax/database/src/Database/QueryBuilder.php";
// require ANAX_INSTALL_PATH . "/vendor/anax/di/src/DI/DIFactoryConfig.php";
// require ANAX_INSTALL_PATH . "/vendor/maaa16/commentary/src/Commentary/CommController.php";
// require ANAX_INSTALL_PATH . "/vendor/maaa16/commentary/src/Commentary/Commentary.php";
// require ANAX_INSTALL_PATH . "/vendor/maaa16/commentary/src/Commentary/CommAssembler.php";
