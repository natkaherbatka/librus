
<?php
session_start();

if(!isset($_SESSION['login']))
{
    header("Location:index.php");
    exit();
}

require_once "dbconnect.php";
$connection = mysqli_connect($host, $user, $password, $dbname);

if(isset($_GET['id_ucznia']))
{
    $id_ucznia = $_GET['id_ucznia'];

    $query = "SELECT * FROM uczen WHERE id=$id_ucznia";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    if(!$row || $row['id_klasy'] != $_SESSION['id_klasy'])
    {
        header("Location:index.php");
        exit();
    }

    $nazwisko = $row['Nazwisko'];
    $imie = $row['Imie'];
    $srednia_ocen = $row['Srednia_ocen'];

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>EDYCJA UCZNIA</title>
</head>
<body>

<p><strong>EDYCJA UCZNIA</strong></p>

<form action="edytuj_ucznia.php" method="post">
    <input type="text" name="imie" value="<?php echo $imie; ?>" required><br>
    <input type="text" name="nazwisko" value="<?php echo $nazwisko; ?>" required><br>
    <input type="number" step="0.01" name="srednia_ocen" value="<?php echo $srednia_ocen; ?>" required><br>
    <input type="hidden" name="id_ucznia" value="<?php echo $id_ucznia; ?>">
    <input type="submit" name="edytuj_ucznia" value="ZAPISZ">
</form>

</body>
</html>

<?php
}
elseif(isset($_POST['edytuj_ucznia']))
{
    $id_ucznia = $_POST['id_ucznia'];

    $query = "SELECT * FROM uczen WHERE id=$id_ucznia";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    if($row && $row['id_klasy'] == $_SESSION['id_klasy'])
    {
        $imie = $_POST['imie'];
        $nazwisko = $_POST['nazwisko'];
        $srednia_ocen = $_POST['srednia_ocen'];

        $query = "UPDATE uczen SET Nazwisko='$nazwisko',
        Imie='$imie', Srednia_ocen='$srednia_ocen'
        WHERE id=$id_ucznia";

        mysqli_query($connection, $query);
    }

    header("Location:index.php");
}

mysqli_close($connection);
?>
