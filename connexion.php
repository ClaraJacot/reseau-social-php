<?php
$mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
if ($mysqli->connect_errno)
{
    echo "<article>";
    echo("Échec de la connexion : " . $mysqli->connect_error);
    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
    echo "</article>";
    exit();
}
?>