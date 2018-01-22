<?php 

require_once("config.php");

if (isset($_REQUEST["name"]) && isset($_REQUEST["ativar"])) {
	query("UPDATE painel SET ativo = '" . escape($_REQUEST["ativar"]) . "' WHERE nome = '" . escape($_REQUEST["name"]) . "'");
}
?>