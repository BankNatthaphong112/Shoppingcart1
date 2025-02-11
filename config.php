<?php
//var url
$base_url = 'http://localhost/Shoppingcart';

//var database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'shoppingcart';

//connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if($conn->connect_error){
    die('Database connection error: ' . $conn->connect_error);
}
