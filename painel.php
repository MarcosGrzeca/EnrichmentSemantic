<?php 

require_once("config.php");

$tweets = query("SELECT * FROM painel");

$table = "<table><tr><th>Nome</th><th>Situação</th><th>Ativar</th><th>Desativar</th></tr>";

foreach (getRows($tweets) as $key => $value) {
    $table .= "<tr><td>" . $value["nome"] . "</td><td>" . $value["ativo"] . "</td><td><a href='painel_status.php?name=" . $value["nome"] . "&ativar=1'>Ativar</a></td><td><a href='painel_status.php?name=" . $value["nome"] . "&ativar=0'>Desativar</a></td></tr>";
}

$table .= "</table>";
echo $table;
?>