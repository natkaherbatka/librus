<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "szkola";

$connection = mysqli_connect($host, $user, $password, $dbname);

if (!$connection) {
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8");
?>