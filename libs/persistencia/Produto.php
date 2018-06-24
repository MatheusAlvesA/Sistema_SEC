<?php
namespace Persistencia;

class Produto extends ModeloDados {
	private $id_produto;
	private $id_categoria;
	private $nome_produto;

	function __construct(int $id_produto,
						int $id_categoria,
						string $nome_produto,
						Persistencia $banco)
	{
		$this->id_produto = $id_produto;
		$this->id_categoria = $id_categoria;
		$this->nome_produto = $nome_produto;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdProduto(): int {return $this->id_produto;}
	public function getCategoria(): CategoriaProduto {return $this->banco->getCategoriaProduto($this->id_categoria);}
	public function getNomeProduto(): string {return $this->nome_produto;}

/*
	FUNÇÕES SET
*/
	public function setIdCategoria(int $valor): bool {
		if($this->atualizarAtributo('idcategoria', $valor)) {
			$this->id_categoria = $valor;
			return true;
		}
		return false;
	}
	public function setNome(string $valor): bool {
		if($this->atualizarAtributo('nomeProduto', $valor)) {
			$this->nome_produto = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('produto', $atributo, $valor, 'idproduto', $this->id_categoria);
	}

	public function toArray(): array {
		return ['idProduto' => $this->id_produto,
			'idCategoria' => $this->id_categoria,
			'nomeProduto' => $this->nome_produto];
	}
	public function toColunasBanco(): array {
		return ['idcategoria' => $this->id_categoria,
			'nomeProduto' => $this->nome_produto];
	}
}
?>