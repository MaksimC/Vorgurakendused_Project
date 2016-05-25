<?php


function connect_db(){
    global $connection;
    $host="localhost";
    $user="test";
    $pass="t3st3r123";
    $db="test";
    $connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
    mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function logi(){
    // siia on vaja funktsionaalsust (13. nädalal)
    global $connection;
    $errors =array();

    if(isset($_SESSION["user"])){
        header("Location: ?page=loomad");
    } else {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["user"]) || empty($_POST["pass"])) {
                if (empty($_POST["user"])) {
                    $errors[] = "Fill in username!";
                }
                if (empty($POST["pass"])) {
                    $errors[] = "Please enter your password!";
                }

            } else {
                $username = mysqli_real_escape_string($connection, $_POST["user"]);
                $password = mysqli_real_escape_string($connection, $_POST["pass"]);
                $query = "SELECT id FROM mtseljab_kylastajad WHERE username='".$username."' AND passw=sha1('".$password."')";
                $result = mysqli_query($connection, $query) or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    $_SESSION["user"] = $_POST["user"];
                    header("Location: ?page=loomad");
                } else {
                    header("Location: ?page=login");
                }
            }
        }
    }

    include_once('views/login.html');
}

function logout(){
    $_SESSION=array();
    session_destroy();
    header("Location: ?");
}
function kuva_puurid(){
    // siia on vaja funktsionaalsust
    global $connection;
    $puurid = array();


    if(empty($_SESSION["user"])) {
        header("Location: ?page=login");
    } else {
        $query = "SELECT DISTINCT puur FROM loomaaed_mtseljab ORDER BY puur ASC ";
        $result = mysqli_query($connection, $query) or die("$query - " . mysqli_error($connection));

        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while ($puurinumbrid = mysqli_fetch_assoc($result)) {
                $result_loomad = mysqli_query($connection, "SELECT * FROM loomaaed_mtseljab WHERE  puur=" . $puurinumbrid['puur']);
                while ($loomarida = mysqli_fetch_assoc($result_loomad)) {
                    $puurid[$puurinumbrid['puur']][] = $loomarida;
                }
            }
        }
    }

    mysqli_close($connection);
    mysqli_free_result($result);
    include_once('views/puurid.html');


}

function lisa(){
    // siia on vaja funktsionaalsust (13. nädalal)
    global $connection;
    $errors = array();

    if(empty($_SESSION["user"])){
        header("Location: ?page=login");
    } else if ($_SERVER["REQUEST_METHOD"]=="POST"){
        if(empty($_POST["user"]) || empty($POST["pass"])){
            if (empty($POST["nimi"])){
                $errors[] = "Fill in name!";
            } if (empty($POST["puur"])){
                $errors[] = "Please enter puur number!";
            }
        } else {
            upload('liik');
            $nimi = mysqli_real_escape_string ($connection, $_POST["nimi"]);
            $puur = mysqli_real_escape_string ($connection, $_POST["puur"]);
            $liik = mysqli_real_escape_string ($connection, "pildid/".$_FILES["liik"]["name"]);
            $query = "INSERT INTO mtseljab_loomaaed (nimi, puur, liik) VALUES ('$nimi','$puur','$liik')";
            $result = mysqli_query($connection, $query);
            $row = mysqli_fetch_assoc($result);
            if ($row){

                header("Location: ?page=loomad");
            } else {
                header("Location: ?page=loomavorm");
            }
        }
    }


    include_once('views/loomavorm.html');

}
