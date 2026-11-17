
<?php
session_start();

if(isset($_SESSION['login']) && isset($_GET['id_ucznia']))
{
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);

    $id_ucznia = $_GET['id_ucznia'];

    $query = "SELECT * FROM uczen WHERE id=$id_ucznia";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    if($row && $row['id_klasy'] == $_SESSION['id_klasy'])
    {
        $query = "DELETE FROM uczen WHERE id=$id_ucznia";
        mysqli_query($connection, $query);
    }

    mysqli_close($connection);
}

header("Location:index.php");
?>
