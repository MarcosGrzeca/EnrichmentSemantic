<?php

require_once("config.php");

$fn = fopen("cat2.html","r");

$ultimoTab = null;

$lines = [];
while(! feof($fn))  {
	$lines[] = fgets($fn);
}
fclose($fn);

$categorias = [];
$categoriasFaltantes = $lines;
foreach ($lines as $key => $value) {
	$numeroTabs = strspn($value, "\t");
	$value = trim($value);
	if ($numeroTabs == 0) {
		unset($categoriasFaltantes[$key]);
		$cat = montarNiveis($categoriasFaltantes, 0, $key);
		if (count($cat)) {
			$categorias[$value] = $cat;
		} else {
			$categorias[$value] = $value;
		}
	}
}

function montarNiveis(&$categoriasFaltantes, $indice, &$lidas) {
	$categorias = [];

	foreach ($categoriasFaltantes as $key => $value) {
		$numeroTabs = strspn($value, "\t");
		if ($numeroTabs <= $indice) {
			break;
		}
		unset($categoriasFaltantes[$key]);
		if ($key <= $lidas) {
			continue;
		}
		$lidas = $key;
		$cat = montarNiveis($categoriasFaltantes, ($indice +2), $lidas);
		$value = trim($value);
		if (count($cat)) {
			$categorias[$value] = $cat;
		} else {
			$categorias[$value] = $value;
		}
	}

	return $categorias;
}

echo "<pre>";
var_export(json_encode($categorias));
//var_export($categorias);
?>