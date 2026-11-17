
<?php
session_start();

if(isset($_POST['zaloguj']))
{
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);
    mysqli_set_charset($connection, "utf8");

    $imie = $_POST['login'];
    $nazwisko = $_POST['haslo'];

    $query = "SELECT * FROM wychowawca WHERE imie='$imie' AND nazwisko='$nazwisko'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['login'] = $row['imie'];
        $_SESSION['nazwisko'] = $row['nazwisko'];
        $_SESSION['id_klasy'] = $row['id_klasy'];
        $_SESSION['id_wychowawcy'] = $row['id'];

    }
    else
    {
        $blad = "ZŁE DANE LOGOWANIA";
    }

    mysqli_close($connection);
}

if(!isset($_SESSION['login']))
{
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<h2>LOGOWANIE</h2>

<form action="index.php" method="post">
    <input type="text" name="login" placeholder="LOGIN" required><br><br>
    <input type="password" name="haslo" placeholder="HASŁO" required><br><br>
    <input type="submit" name="zaloguj" value="ZALOGUJ">
</form>

<?php
if(isset($blad))
{
    echo "<p style='color:red;'>$blad</p>";
}
?>

</body>
</html>

<?php
exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Dziennik szkolny</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<form action="logout.php" method="post">
    <input type="submit" value="WYLOGUJ">
</form>

<p>
Zalogowano: <?php echo $_SESSION['login']." ".$_SESSION['nazwisko']; ?>
</p>

<form action="index.php" method="post">
    <select name="klasa" onchange="this.form.submit();">
        <option value="">WYBIERZ KLASĘ</option>
        <option value="1a">1a</option>
        <option value="1b">1b</option>
        <option value="2a">2a</option>
        <option value="2b">2b</option>
    </select>
</form>

<?php

if(isset($_POST['klasa']) && $_POST['klasa'] != "")
{
    $_SESSION['wybrana_klasa'] = $_POST['klasa'];
}

if(isset($_SESSION['wybrana_klasa']))
{
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);
    mysqli_set_charset($connection, "utf8");

    $klasa = $_SESSION['wybrana_klasa'];

    $query = "SELECT uczen.id, uczen.Imie, uczen.Nazwisko,
    uczen.Srednia_ocen, uczen.id_klasy,
    wychowawca.imie AS imie_w,
    wychowawca.nazwisko AS nazwisko_w
    FROM uczen, klasa, wychowawca
    WHERE klasa.nazwa='$klasa'
    AND klasa.id=uczen.id_klasy
    AND wychowawca.id_klasy=klasa.id";

    $result = mysqli_query($connection, $query);

    if(!$result)
    {
        die("Błąd zapytania: " . mysqli_error($connection));
    }

    $liczba_wierszy = mysqli_num_rows($result);

    if($liczba_wierszy == 0)
    {
        echo '<span style="color:red;">Nie ma uczniów w tej klasie!</span>';
    }
    else
    {
        $pierwszy = mysqli_fetch_assoc($result);

        $id_klasy = $pierwszy['id_klasy'];

        echo "<p><strong>KLASA ".$klasa." - ".$pierwszy['imie_w']." ".$pierwszy['nazwisko_w']."</strong></p>";

        echo "<table>
        <thead>
        <tr>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Średnia ocen</th>
        <th></th>
        <th></th>
        </tr>
        </thead>
        <tbody>";

        $srednia_ocen = 0;

        do
        {
            echo "<tr>";

            echo "<td>".$pierwszy['Imie']."</td>";
            echo "<td>".$pierwszy['Nazwisko']."</td>";
            echo "<td>".$pierwszy['Srednia_ocen']."</td>";

            $id = $pierwszy['id'];

            if($_SESSION['id_klasy'] == $id_klasy)
            {
                echo "<td><button onclick=\"window.location.href='edytuj_ucznia.php?id_ucznia=$id'\">EDYTUJ</button></td>";

                echo "<td><button onclick=\"if(confirm('Czy chcesz usunąć ucznia?')) window.location.href='usun_ucznia.php?id_ucznia=$id'\">KASUJ</button></td>";
            }
            else
            {
                echo "<td></td><td></td>";
            }

            echo "</tr>";

            $srednia_ocen += $pierwszy['Srednia_ocen'];

        } while($pierwszy = mysqli_fetch_assoc($result));

        echo "</tbody></table>";

        echo "<p>Średnia ocen klasy: ".round($srednia_ocen/$liczba_wierszy, 2)."</p>";

        if($_SESSION['id_klasy'] == $id_klasy)
        {
?>

<hr>

<p><strong>DODAWANIE NOWEGO UCZNIA</strong></p>

<form action="dodaj_ucznia.php" method="post">
    <input type="text" name="imie" placeholder="IMIĘ" required><br>
    <input type="text" name="nazwisko" placeholder="NAZWISKO" required><br>
    <input type="number" step="0.01" name="srednia_ocen" placeholder="ŚREDNIA OCEN" required><br>
    <input type="submit" name="dodaj_ucznia" value="ZAPISZ">
</form>

<?php
        }
    }

    mysqli_close($connection);
}

?>

</body>
</html>
