<?php
session_start();
if(isset($_GET['user_id'])){
    $userId =intval($_GET['user_id']);
}else{
    $userId = false; 
}

$connectedId = $_SESSION['connected_id'];



$mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
if ($mysqli->connect_errno)
{
    echo "<article>";
    echo("Échec de la connexion : " . $mysqli->connect_error);
    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
    echo "</article>";
    exit();
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return (strpos($haystack, $needle) !== false);
    }
}

?>