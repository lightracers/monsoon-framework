<?php

use Config\Config;

require 'src/Config/Config.php';
$database = (new Config())->env['database'];

return [
    "paths" => [
        "migrations" => "data/migrations",
        "seeds"      => "data/seeds",
    ],
    "environments" => [
        "default_migration_table" => "db_versions",
        "default_database"        => "local",
        "local"                   => [
            "adapter" => $database['type'],
            "host"    => $database['hostname'],
            "name"    => $database['database'],
            "user"    => $database['username'],
            "pass"    => $database['password'],
            "port"    => $database['port'],
        ],
    ],
    "version_order"              => "creation",
];
