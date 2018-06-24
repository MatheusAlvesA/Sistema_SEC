<?php
namespace Persistencia;

class TipoAcesso extends ModeloDados {
	private $id_tipo_acesso;
	private $tipo_acesso;

	function __construct(int $id_tipo_acesso, string $tipo_acesso, Persistencia $banco) {
		$this->id_tipo_acesso = $id_tipo_acesso;
		$this->tipo_acesso = $tipo_acesso;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdTipoAcesso(): int {return $this->id_tipo_acesso;}
	public function getTipoAcesso(): string {return $this->tipo_acesso;}
/*
	FUNÇÕES SET
*/
	public function setTipoAcesso(string $valor): bool {
		if($this->atualizarAtributo('nometipoacesso', $valor)) {
			$this->tipoacesso = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('tipoacesso', $atributo, $valor, 'idtipoacesso', $this->id_tipo_acesso);
	}

	public function toArray(): array {
		return ['idTipoAcesso' => $this->id_tipo_acesso,
				'tipoAcesso' => $this->tipo_acesso];
	}
	public function toColunasBanco(): array {
		return ['nometipoacesso' => $this->tipo_acesso];
	}
}
?>