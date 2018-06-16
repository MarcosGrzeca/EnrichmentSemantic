<?php 

class Categoria {

	protected $nome = "";
	protected $count = 1;
	protected $filhos = array();


	function __construct($nome) {
		$this->nome = $nome;
	}

	function getNome() {
		return $this->nome;
	}

	function getCount() {
		return $this->count;
	}

	function increment() {
		$this->count++;
	}

	function incluirFilho($nome) {
		if (!isset($this->filhos[$nome])) {
			$this->filhos[$nome] = new Categoria($nome);
		} else {
			$this->filhos[$nome]->increment();
		}
		return $this->filhos[$nome];
	}

	function getFilhos() {
		return $this->filhos;
	}
}

?>