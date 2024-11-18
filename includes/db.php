<?php 

    $servername = "localhost";
    $nome_utilizador = "root";
    $password = "";
    $dbname = "exame_semi_auromatico";

    $conn = new mysqli($servername, $nome_utilizador, $password, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

?>