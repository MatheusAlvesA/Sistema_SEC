<?php
namespace Persistencia;

abstract class ModeloDados {
	protected $banco;

	abstract public function toArray(): array;
	abstract public function toColunasBanco(): array;

	protected function _atualizarAtributo($tabela, $chave, $valor, $condicao_a, $condicao_b): bool {
		return $this->banco->atualizar($tabela, $chave, $valor, $condicao_a, $condicao_b);
	}
}
?>