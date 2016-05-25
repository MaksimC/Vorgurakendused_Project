<?php
session_start();
require_once ("functions.php");
connect_db();

$page = "start";
if(isset($_GET["page"]) && $_GET["page"] !=""){
    $page = htmlspecialchars($_GET["page"]);
}

include_once ("views/header.html");

switch($page){
    case "login":
        login();
        break;
    case "logout":
        logout();
        break;
    case "register":
        register();
        break;
    case "create_bins":
        create_bins();
        break;
    case "receipt":
        receipt();
        break;
    case "stock_taking":
        stock_taking();
        break;
    case "warehouse":
        show_warehouse();
        break;
    default:
        include_once ("views/start_page.html");
        break;
}
include_once ("views/footer.html");

?>