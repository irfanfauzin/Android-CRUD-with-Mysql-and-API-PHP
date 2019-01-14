# Android CRUD with Mysql and API PHP
An Application with functions : Register, Login, Update Password, and Delete account


## Creating new database

1. Create new database
2. Create table users 

## Set up Database Connection with API

Create DbConnect.php 

```<?
$host = 'localhost'; // hostname
$db   = 'SIS'; // database name
$user = 'root'; // user
$pass = ''; // password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
```

and Copy Api.php to same folder with DbConnect.php

## Configuring URLs
if you are using Xampp change on URLs.class

```String ROOT_URL = "https://localhost/{directory name}/Api.php?apicall="```
