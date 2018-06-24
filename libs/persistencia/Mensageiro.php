<?php
namespace Persistencia;

class Mensageiro extends ModeloDados {
	private $id_mensageiro;
	private $nome_mensageiro;

	function __construct(int $id_mensageiro, string $nome_mensageiro, Persistencia $banco) {
		$this->id_mensageiro = $id_mensageiro;
		$this->nome_mensageiro = $nome_mensageiro;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdMensageiro(): int {return $this->id_mensageiro;}
	public function getNomeMensageiro(): string {return $this->nome_mensageiro;}
/*
	FUNÇÕES SET
*/
	public function setNomeMensageiro(string $valor): bool {
		if($this->atualizarAtributo('nomeMensageiro', $valor)) {
			$this->nome_mensageiro = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('mensageiro', $atributo, $valor, 'idmensageiro', $this->id_mensageiro);
	}

	public function toArray(): array {
		return ['idMensageiro' => $this->id_mensageiro,
				'nomeMensageiro' => $this->nome_mensageiro];
	}
	public function toColunasBanco(): array {
		return ['nomeMensageiro' => $this->nome_mensageiro];
	}
}
?>