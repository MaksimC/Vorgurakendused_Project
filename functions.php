<?php

function test_input($data) {
    global $connection;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($connection, $data);
    return $data;
}

function connect_db(){
    global $connection;
    $host="localhost";
    $user="test";
    $pass="t3st3r123";
    $db="test";
    $connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
    mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function login(){

    global $connection;
    $errors =array();

    if(isset($_SESSION["user"])){
        header("Location: ?page=warehouse");
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
                $username = test_input($_POST["user"]);
                $password = test_input($_POST["pass"]);
                $query = "SELECT role FROM mtseljab_wrh_users WHERE username='".$username."' AND password=/*sha1*/('".$password."')";
                $result = mysqli_query($connection, $query) or die("Error when logging to DB ".mysqli_error($connection));
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    $_SESSION["user"] = $_POST["user"];
                    $_SESSION["role"] = $row["role"];
                    header("Location: ?page=warehouse");
                } else {
                    header("Location: ?page=login");

                }
            }
        }
    }

    include_once('views/login_page.html');
}

function logout(){
    $_SESSION=array();
    session_destroy();
    header("Location: ?");
}

function register(){
    global $connection;
    $errors =array();


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["user"]) || empty($_POST["pass"])) {
            if (empty($_POST["user"])) {
                $errors[] = "Fill in username!";
            }
            if (empty($POST["pass"])) {
                $errors[] = "Please enter your password!";
            }

        } else {
            $username = test_input($_POST["user"]);
            $password = test_input($_POST["pass"]);
            $user_role = test_input($_POST["role"]);
            $query = "INSERT INTO mtseljab_wrh_users (username, password, role) VALUES ('".$username."', '".$password."', '".$user_role."')";
            $result = mysqli_query($connection, $query) or die("Error when logging to DB ".mysqli_error($connection));
            $row = mysqli_insert_id($connection);
            if ($row) {
                $_SESSION["user"] = $_POST["user"];
                $_SESSION["role"] = $_POST["role"];
                header("Location: ?page=warehouse");
            } else {
                header("Location: ?page=login");

            }
        }
    } else {
        header ("Location: ?page=startpage");
    }

    include_once('views/login_page.html');
}

/*
function register1(){
    global $connection;
    $errors = array();

    if ($_SERVER["REQUEST_METHOD"]==$_POST){

        if(empty($_POST["user"])) {
            $errors[]="Enter username for registering";
        }

        if(empty($_POST["user"])) {
            $errors[]="Enter password for registering";
        }

        if(empty($errors)){
            $username = test_input($_POST["user"]);
            $password = test_input($_POST["pass"]);
            $user_role = test_input($_POST["role"]);
            $query = "INSERT INTO mtseljab_wrh_users (username, password, role) VALUES ('".$username."', '".$password."', '".$user_role."')";
            $result = mysqli_query($connection, $query) or die("Error when writing to DB ".mysqli_error($connection));
            $row = mysqli_insert_id($connection);
            if($row){
                $_SESSION["role"] = $_POST["role"];
                $_SESSION["user"] = $_POST["user"];
                header ("Location: ?page=warehouse");
            }else {
                header("Location: ?page=login");
            }
        }
    } else {
        header ("Location: ?page=startpage");
    }
    include_once('views/login.html');
}

function show_warehouse(){

    include_once ("views/warehouse.html");
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
*/