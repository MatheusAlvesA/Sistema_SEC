<?php
namespace Persistencia;

class ItemContrato extends ModeloDados {
	private $id_parcela_contrato;
	private $id_contrato;
	private $valor_bruto;
	private $data_vencimento;
	private $data_pagamento;
	private $deducoes;
	private $nota_fiscal;
	private $observacao;
	private $foi_paga;
	private $numero;

	function __construct (
		int $id_parcela_contrato,
		int $id_contrato,
		float $valor_bruto,
		string $data_vencimento,
		/*string ou null*/ $data_pagamento,
		float $deducoes,
		string $nota_fiscal,
		string $observacao,
		bool $foi_paga,
		int $numero,
		Persistencia $banco
	) {
		if($data_pagamento === '') //Se essa parâmetro for passado para o banco ele vai setar a fata como 00/00/0000
			$data_pagamento = null; // Transformando em null para evitar o problema

		$this->id_parcela_contrato = $id_parcela_contrato;
		$this->id_contrato = $id_contrato;
		$this->valor_bruto = $valor_bruto;
		$this->data_vencimento = $data_vencimento;
		$this->data_pagamento = $data_pagamento;
		$this->deducoes = $deducoes;
		$this->nota_fiscal = $nota_fiscal;
		$this->observacao = $observacao;
		$this->foi_paga = $foi_paga;
		$this->numero = $numero;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdItem(): int {return $this->id_parcela_contrato;}
	public function getContrato(): Contrato {return $this->banco->getContrato($this->id_contrato);}
	public function getValorBruto(): float {return $this->valor_bruto;}
	public function getDataVencimento(): string {return $this->data_vencimento;}
	public function getDataPagamento(): string {return $this->data_pagamento;}
	public function getDeducoes(): float {return $this->deducoes;}
	public function getNotaFiscal(): string {return $this->nota_fiscal;}
	public function getObservacao(): string {return $this->observacao;}
	public function getFoiPaga(): bool {return $this->foi_paga;}
	public function getNumero(): int {return $this->numero;}
/*
	FUNÇÕES SET
*/

	public function setIdItem(int $valor): bool {
		$this->id_parcela_contrato = $valor;
		return true;
	}

	public function setIdContrato(int $valor): bool {
		if($this->atualizarAtributo('idcontrato', $valor)) {
			$this->id_contrato = $valor;
			return true;
		}
		return false;
	}
	public function setValorBruto(float $valor): bool {
		if($this->atualizarAtributo('valorbruto', $valor)) {
			$this->valor_bruto = $valor;
			return true;
		}
		return false;
	}
	public function setDataVencimento(string $valor): bool {
		if($this->atualizarAtributo('datavencimento', $valor)) {
			$this->data_pagamento = $valor;
			return true;
		}
		return false;
	}
	public function setDataPagamento($valor): bool {
		if($this->atualizarAtributo('datepagamento', $valor)) {
			$this->data_pagamento = $valor;
			return true;
		}
		return false;
	}
	public function setDeducoes(float $valor): bool {
		if($this->atualizarAtributo('deducoes', $valor)) {
			$this->deducoes = $valor;
			return true;
		}
		return false;
	}
	public function setNotaFiscal(string $valor): bool {
		if($this->atualizarAtributo('notafiscal', $valor)) {
			$this->nota_fiscal = $valor;
			return true;
		}
		return false;
	}
	public function setObservacao(string $valor): bool {
		if($this->atualizarAtributo('observacao', $valor)) {
			$this->observacao = $valor;
			return true;
		}
		return false;
	}
	public function setFoiPaga(int $valor): bool {
		$valor_convertido = false; //Assumimos falso
		if($valor != 0) {// se não for falso
			$valor_convertido = true;
			$valor = 1; //garantindo que sempre será 0 ou 1
		}

		if($this->atualizarAtributo('foipaga', $valor)) {
			$this->foi_paga = $valor_convertido;
			return true;
		}
		return false;
	}
	public function setNumero(int $valor): bool {
		if($this->atualizarAtributo('numero', $valor)) {
			$this->numero = $valor;
			return true;
		}
		return false;
	}

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('parcelacontrato', $atributo, $valor, 'idparcelacontrato', $this->id_parcela_contrato);
	}

	public function toArray(): array {
		return ['idParcelaContrato' => $this->id_parcela_contrato,
				'idContrato' => $this->id_contrato,
				'valorBruto' => $this->valor_bruto,
				'dataVencimento' => $this->data_vencimento,
				'dataPagamento' => $this->data_pagamento,
				'deducoes' => $this->deducoes,
				'notaFiscal' => $this->nota_fiscal,
				'observacao' => $this->observacao,
				'foiPaga' => $this->foi_paga,
				'numero' => $this->numero];
	}
	public function toColunasBanco(): array {
		return ['idcontrato' => $this->id_contrato,
				'valorbruto' => $this->valor_bruto,
				'datavencimento' => $this->data_vencimento,
				'datepagamento' => $this->data_pagamento,
				'deducoes' => $this->deducoes,
				'notafiscal' => $this->nota_fiscal,
				'observacao' => $this->observacao,
				'foipaga' => $this->foi_paga,
				'numero' => $this->numero];
	}
}
?>