<?php
namespace Persistencia;

class Funcionario extends ModeloDados {
	private $id_funcionario;
	private $nome_funcionario;

	function __construct(int $id_funcionario, string $nome_funcionario, Persistencia $banco) {
		$this->id_funcionario = $id_funcionario;
		$this->nome_funcionario = $nome_funcionario;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdFuncionario(): int {return $this->id_funcionario;}
	public function getNomeFuncionario(): string {return $this->nome_funcionario;}
/*
	FUNÇÕES SET
*/
	public function setNomeFuncionario(string $valor): bool {
		if($this->atualizarAtributo('nomefuncionario', $valor)) {
			$this->nome_funcionario = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('funcionario', $atributo, $valor, 'idfuncionario', $this->id_funcionario);
	}

	public function toArray(): array {
		return ['idFuncionario' => $this->id_funcionario,
				'nomeFuncionario' => $this->nome_funcionario];
	}
	public function toColunasBanco(): array {
		return ['nomefuncionario' => $this->nome_funcionario];
	}
}
?>