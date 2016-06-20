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
    //$user="root";
    //$pass="";
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
                $query = "SELECT role FROM mtseljab_wrh_users WHERE username='".$username."' AND password=sha1('".$password."')";
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
            $query = "SELECT id FROM mtseljab_wrh_users WHERE username='$username'";
            $result = mysqli_query($connection,$query);
            $row = mysqli_fetch_assoc($result);
            if($row){
                $errors[] = "User with this name already exists, please choose another one";
            }else{



                $username = test_input($_POST["user"]);
                $password = test_input($_POST["pass"]);
                $user_role = test_input($_POST["role"]);
                $query = "INSERT INTO mtseljab_wrh_users (username, password, role) VALUES ('".$username."', sha1('".$password."'), '".$user_role."')";
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
        }
    } else {
        header ("Location: ?page=startpage");
    }

    include_once('views/login_page.html');
}


function show_warehouse(){

    global $connection;

    if (empty($_SESSION["user"])){
        header ("Location: ?page=login");
    }else{
        $query = "SELECT id, bin, SUT, material, quantity, empty_indicator FROM mtseljab_warehouse ORDER by bin ASC";
        $result = mysqli_query($connection, $query);

    }

    include_once ("views/warehouse.html");
}

function retrieve_material($id){
    global $connection;

    $query = "SELECT id, bin, SUT, material, quantity, empty_indicator FROM mtseljab_warehouse where ID= '".$id."' " ;
    $result = mysqli_query($connection, $query)or die("$query - ".mysqli_error($connection));
    if ($result){
        $material = mysqli_fetch_assoc($result);
        return $material;
    } else {
        header("Location: ?page=start_page");
    }
}


function pick(){
    global $connection;
    $material=null;
    error_reporting(E_ALL & ~E_NOTICE);

    if (empty($_SESSION["user"])){
        header("Location: ?page=login");
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = test_input($_GET["id"]);
        $material = retrieve_material($id);

    } else if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $errors=array();
        $material = retrieve_material($_POST["id"]);
        $quantity_new = test_input($_POST["quantity"]);
        $quantity_upd = ($material["quantity"]) - $quantity_new;

        if(empty($_POST["quantity"])) {
            $errors[]="picking quantity is missing";
        }

        if ($quantity_new > $material["quantity"]){
            $errors[]= "picked quantity cannot be greater than quantity in the bin";
        }

        if (empty($errors)) {
            $id = test_input($_POST["id"]);

            if ($quantity_new == $material["quantity"]){
                $query = "UPDATE mtseljab_warehouse SET material ='', quantity=NULL, empty_indicator='empty' WHERE id=".$id;
                $result = mysqli_query($connection, $query) or die("$query - ".mysqli_error($connection));
                $id = mysqli_affected_rows($connection);
                if($id){
                    header("Location: ?page=warehouse");
                } else{
                    header("Location: ?page=pick");
                }
            } else{

                $query = "UPDATE mtseljab_warehouse SET quantity=".$quantity_upd." WHERE id=".$id;
                $result = mysqli_query($connection, $query);
                //$ids = mysqli_insert_id($connection);
                if($result){
                    header("Location: ?page=warehouse");
                } else{
                    header("Location: ?page=pick"/*&id=".$id*/);
                }
            }
            exit(0);
        }
    } else {
        header("Location: ?page=warehouse");
    }

    include_once('views/pick.html');
}


function goods_receipt() {
    global $connection;
    $errors=array();

    if(empty($_SESSION["user"])){
        header ("Location: ?page=login");
    }

    if ($_SERVER["REQUEST_METHOD"]== "POST"){
        $material = test_input($_POST["material"]);
        $quantity = test_input($_POST["quantity"]);
        $SUT = test_input($_POST["sut"]);

        if (empty($_POST["material"])){
            $errors[] = "Field materials cannot be empty";
        }

        if (empty($_POST["quantity"])){
            $errors[] = "Enter Goods Receipt quantity";
        }

        if (empty($_POST["sut"])){
            $errors[] = "Select Storage Unit Type";
        }

        if (empty($errors)){

            $query = "SELECT id FROM mtseljab_warehouse WHERE SUT= '$SUT' AND empty_indicator= 'empty' LIMIT 1";
            $result = mysqli_query($connection, $query);
            $bin = mysqli_fetch_assoc($result);
            if(isset($bin['id'])){

                $query = "UPDATE mtseljab_warehouse SET material = '$material', quantity = $quantity, empty_indicator = 'full' WHERE id = ".$bin["id"];
                $result = mysqli_query($connection, $query);
                $row = mysqli_affected_rows($connection);
                if($row>0){
                    header("Location: ?page=warehouse");
                }else{
                    $errors[] = "No empty bins of this type. Please create new bins to WH";
                    header ("Location: ?page=login");
                }
            } else {
                $errors[] = "No empty bins of this type. Please create new bins WH";

            }
        }
    }
    include_once ("views/receipt.html");
}


function stock_taking(){
    global $connection;
    $material_DB = null;

    if(empty($_SESSION["user"])){
        header ("Location: ?page=login");
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        $id = $_GET["id"];
        $material_DB = retrieve_material($id);

    } else if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $errors = array();
        $id = test_input($_POST['id']);
        $material = test_input($_POST['material']);
        $material_DB = retrieve_material($id);
        $quantity_new = test_input($_POST['quantity']);

        If ($quantity_new == 0) {
            $query = " UPDATE mtseljab_warehouse SET material ='', quantity = NULL, empty_indicator = 'empty' WHERE id =$id ";
            $result = mysqli_query($connection, $query);
            if($result){
                header("Location: ?page=warehouse");
            } else{
                header("Location: ?page=stock_taking");
            }

        } else{
            $query = " UPDATE mtseljab_warehouse SET material ='$material', quantity = $quantity_new, empty_indicator = 'full' WHERE id =$id ";
            $result = mysqli_query($connection, $query);
            if($result){
                header("Location: ?page=warehouse");
            } else{
                header("Location: ?page=stock_taking");
            }

        }

    } else {
        header("Location: ?page=loomad");
    }

    include_once ("views/stock_taking.html");
}

function create_bins(){
    global $connection;
    $errors = array();

    if(empty($_SESSION["user"])){
        header ("Location: ?page=login");
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        for ($x = 1; $x <= 3; $x++) {

            $bin = test_input($_POST["bin".$x]);
            $sut = test_input($_POST["sut".$x]);

            $query = "SELECT bin FROM mtseljab_warehouse WHERE bin = '$bin' ";
            $result = mysqli_query($connection, $query);
            $id = mysqli_fetch_assoc($result);
            if($id){
                $errors[] = "Bin with this name already exists. Please choose another name.";
            } else {
                $query = "INSERT INTO mtseljab_warehouse (bin, SUT, empty_indicator) VALUES ('$bin', '$sut', 'empty') ";
                $result = mysqli_query ($connection, $query);
                $id = mysqli_insert_id($connection);
                if($id){
                    header ("Location: ?page=warehouse");
                }else{
                    $errors[]="failed to create bin";
                }
            }
        }
    }

    include_once ("views/create_bins.html");
}


function delete_bins(){
    global $connection;
    if (empty($_SESSION["user"])){
        header ("Location: ?page=login");
    }else{
        $query = "SELECT id, bin, SUT, empty_indicator FROM mtseljab_warehouse WHERE empty_indicator='empty' ORDER by bin ASC";
        $result = mysqli_query($connection, $query);

    }

    if($_SERVER["REQUEST_METHOD"]=="POST") {
        $id = test_input($_POST["id"]);
        $query = "DELETE FROM mtseljab_warehouse  WHERE id='$id'";
        $result = mysqli_query($connection, $query);
        $affectedrows = mysqli_affected_rows($connection);
        if($affectedrows){
            header("Location: ?page=delete_bins");
        } else {
            header("Location: ?page=warehouse");
        }

    }

    include_once ("views/delete_bins.html");
}