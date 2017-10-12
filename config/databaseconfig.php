<?php
/**
 * Config file for Database.
 *
 * Example for MySQL.
 *  "dsn" => "mysql:host=localhost;dbname=test;",
 *  "username" => "test",
 *  "password" => "test",
 *  "driver_options"  => [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
 *
 * Example for SQLite.
 *  "dsn" => "sqlite:memory::",
 *
 */

$dbconfig = [
        // "dsn"             => "mysql:host=host;dbname=databasename;",
        // "username"        => "username",
        // "password"        => "password",
        // "driver_options"  => [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
        // "fetch_mode"      => \PDO::FETCH_OBJ,
        // "table_prefix"    => null,
        // "session_key"     => "Anax\Database",
        //
        // // True to be very verbose during development
        // "verbose"         => true,
        //
        // // True to be verbose on connection failed
        // "debug_connect"   => true,
    ];

return $dbconfig;
