<?php

$host="localhost";
//$user="root";
//$pass="";
$user="test";
$pass="t3st3r123";
$db="test";
$connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));

$output = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["excel"])){
        $query = "SELECT * FROM mtseljab_warehouse ORDER BY bin ASC";
        $result = mysqli_query($connection, $query);
        if($result){

            $output .="
                <table class='table-bordered'>
                    <tr>
                     <th> Bin Location </th>
                     <th> Material </th>
                     <th> Quantity </th>
                     <th> Bin Status </th>
                    </tr>
                    ";
            while($row = mysqli_fetch_assoc($result)){
                $output .="
                    <tr>
                     <td>" .$row["bin"]. "</td>
                     <td>" .$row["material"]. "</td>
                     <td>" .$row["quantity"]. "</td>
                     <td>" .$row["empty_indicator"]. "</td>
                    </tr>
                    ";
            }
            $output .="</table>";
            header ("Content-Type: application/xlsx");
            header ("Content-Disposition: attachment; filename=report.xls");
            echo $output;
        }

    }

}

