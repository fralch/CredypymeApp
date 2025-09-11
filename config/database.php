<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('S_MASTER_CONNECTION', 'master'),
    'records' => env('S_RECORDS_CONNECTION', 'records'),

    'master_2' => env('S_MASTER_CONNECTION_2', 'master_2'),
    'records_2' => env('S_RECORDS_CONNECTION_2', 'records_2'),

    'master_3' => env('S_MASTER_CONNECTION_3', 'master_3'),
    'records_3' => env('S_RECORDS_CONNECTION_3', 'records_3'),

    'master_5' => env('S_MASTER_CONNECTION_5', 'master_5'),
    'records_5' => env('S_RECORDS_CONNECTION_5', 'records_5'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'master' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_MASTER_HOST', 'localhost'),
            'port' => env('S_MASTER_PORT', '3306'),
            'database' => env('S_MASTER_DATABASE', 'solucion_master'),
            'username' => env('S_MASTER_USERNAME', 'root'),
            'password' => env('S_MASTER_PASSWORD', ''),
            'unix_socket' => env('S_MASTER_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'records' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_RECORDS_HOST', 'localhost'),
            'port' => env('S_RECORDS_PORT', '3306'),
            'database' => env('S_RECORDS_DATABASE', 'solucion_records'),
            'username' => env('S_RECORDS_USERNAME', 'root'),
            'password' => env('S_RECORDS_PASSWORD', ''),
            'unix_socket' => env('S_RECORDS_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'master_2' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_MASTER_HOST_2', 'localhost'),
            'port' => env('S_MASTER_PORT_2', '3306'),
            'database' => env('S_MASTER_DATABASE_2', 'solucion_master_2'),
            'username' => env('S_MASTER_USERNAME_2', 'root'),
            'password' => env('S_MASTER_PASSWORD_2', ''),
            'unix_socket' => env('S_MASTER_SOCKET_2', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'records_2' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_RECORDS_HOST_2', 'localhost'),
            'port' => env('S_RECORDS_PORT_2', '3306'),
            'database' => env('S_RECORDS_DATABASE_2', 'solucion_records_2'),
            'username' => env('S_RECORDS_USERNAME_2', 'root'),
            'password' => env('S_RECORDS_PASSWORD_2', ''),
            'unix_socket' => env('S_RECORDS_SOCKET_2', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'master_3' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_MASTER_HOST_3', 'localhost'),
            'port' => env('S_MASTER_PORT_3', '3306'),
            'database' => env('S_MASTER_DATABASE_3', 'solucion_master_3'),
            'username' => env('S_MASTER_USERNAME_3', 'root'),
            'password' => env('S_MASTER_PASSWORD_3', ''),
            'unix_socket' => env('S_MASTER_SOCKET_3', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'records_3' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_RECORDS_HOST_3', 'localhost'),
            'port' => env('S_RECORDS_PORT_3', '3306'),
            'database' => env('S_RECORDS_DATABASE_3', 'solucion_records_3'),
            'username' => env('S_RECORDS_USERNAME_3', 'root'),
            'password' => env('S_RECORDS_PASSWORD_3', ''),
            'unix_socket' => env('S_RECORDS_SOCKET_3', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'master_5' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_MASTER_HOST_5', 'localhost'),
            'port' => env('S_MASTER_PORT_5', '3306'),
            'database' => env('S_MASTER_DATABASE_5', 'solucion_master_5'),
            'username' => env('S_MASTER_USERNAME_5', 'root'),
            'password' => env('S_MASTER_PASSWORD_5', ''),
            'unix_socket' => env('S_MASTER_SOCKET_5', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'records_5' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('S_RECORDS_HOST_5', 'localhost'),
            'port' => env('S_RECORDS_PORT_5', '3306'),
            'database' => env('S_RECORDS_DATABASE_5', 'solucion_records_5'),
            'username' => env('S_RECORDS_USERNAME_5', 'root'),
            'password' => env('S_RECORDS_PASSWORD_5', ''),
            'unix_socket' => env('S_RECORDS_SOCKET_5', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],


        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
