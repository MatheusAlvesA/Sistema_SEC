<?php
namespace Persistencia;

class CategoriaProduto extends ModeloDados {
	private $id_produto_categoria;
	private $nome_categoria;

	function __construct(int $id_produto_categoria, string $nome_categoria, Persistencia $banco) {
		$this->id_produto_categoria = $id_produto_categoria;
		$this->nome_categoria = $nome_categoria;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdCategoria(): int {return $this->id_produto_categoria;}
	public function getNomeCategoria(): string {return $this->nome_categoria;}
/*
	FUNÇÕES SET
*/
	public function setNomeCategoria(string $valor): bool {
		if($this->atualizarAtributo('nomecategoria', $valor)) {
			$this->nome_categoria = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('produtocategoria', $atributo, $valor, 'idprodutocategoria', $this->id_produto_categoria);
	}

	public function toArray(): array {
		return ['idCategoria' => $this->id_produto_categoria,
				'nomeCategoria' => $this->nome_categoria];
	}
	public function toColunasBanco(): array {
		return ['nomeCategoria' => $this->nome_categoria];
	}
}
?>