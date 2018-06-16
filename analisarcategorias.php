<?php 

require_once("config.php");
require_once("Categorias/Categoria.php");

$tweets = query("SELECT * FROM tweets_nlp WHERE tipo = 'C'");


$arvoreCategorias = new Categoria("root");
foreach (getRows($tweets) as $key => $value) {
    $arv = explode("/", $value["palavra"]);
    if (trim($arv[0]) == "") {
        unset($arv[0]);
    }
    montarArvore($arv, 1, $arvoreCategorias);
}

echo "<pre>";

function montarArvore($arv, $count = 0, $arvoreCategoriasTmp) {
    if (isset($arv[$count])) {
        //debug("Categoria  " . $arv[$count]);
        //debug($arvoreCategoriasTmp);
        $arvore = $arvoreCategoriasTmp->incluirFilho($arv[$count]);
        montarArvore($arv, ++$count, $arvore);
    } else {
        //return true;
    }
}


$contDesconsideradas = 0;





function montarHtml($nodo, $nivel = 0) {
    global $contDesconsideradas;
    $marcos = array();
    foreach ($nodo->getFilhos() as $key => $value) {
        if ($nivel == 0 && $value->getCount() < 10) {
            $contDesconsideradas++;
            continue;
        }
        $identacao = "";
        for ($i=0; $i < $nivel; $i++) { 
            $identacao .= "&nbsp;&nbsp;";
        }
        //debug($identacao);
        echo "<tr><td>" . $identacao . $value->getNome() . "</td><td>" . $value->getCount() . "</td></tr>";
        
        $marcos[] = array("nodes" => montarHtml($value, ($nivel + 1)), "text" => $value->getNome(), "tags" => array($value->getCount()));
    }
    return $marcos;
}

$marcos = array();

echo "<table>";
echo "<thead><tr><th>Categoria</th><th>Count</th></tr></thead><tbody>";
$final = montarHtml($arvoreCategorias);
debug(json_encode($final));
echo "</tbody></table>";

echo "<br/>Desconsiderei " . $contDesconsideradas;
echo "</pre>";
?>