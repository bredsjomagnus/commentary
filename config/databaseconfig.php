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

// $dbconfig = [
//         "dsn"             => "mysql:host=blu-ray.student.bth.se;dbname=maaa16;",
//         "username"        => "maaa16",
//         "password"        => "tWabjxC6eEH6",
//         "driver_options"  => [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
//         "fetch_mode"      => \PDO::FETCH_OBJ,
//         "table_prefix"    => null,
//         "session_key"     => "Anax\Database",
//
//         // True to be very verbose during development
//         "verbose"         => false,
//
//         // True to be verbose on connection failed
//         "debug_connect"   => false,
//     ];
return [
    "dsn"             => "sqlite:" . ANAX_INSTALL_PATH . "/data/db.sqlite",
    "username"        => null,
    "password"        => null,
    "driver_options"  => null,
    "fetch_mode"      => \PDO::FETCH_OBJ,
    "table_prefix"    => null,
    "session_key"     => "Anax\Database",

    // True to be very verbose during development
    "verbose"         => null,

    // True to be verbose on connection failed
    "debug_connect"   => false,
];
// return $dbconfig;
