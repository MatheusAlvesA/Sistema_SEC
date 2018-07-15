<?php
namespace Persistencia;

class ItemContrato extends ModeloDados {
	private $id_parcela_contrato;
	private $id_contrato;
	private $id_produto;
	private $valor_bruto;
	private $data_vencimento;
	private $data_pagamento;
	private $data_prestacao;
	private $deducoes;
	private $valor_a_pagar;
	private $nota_fiscal;
	private $nota_fiscal_a_pagar;
	private $observacao;
	private $medidas;
	private $foi_paga;
	private $numero;

	function __construct (
		int $id_parcela_contrato,
		int $id_contrato,
		/*int ou null*/ $id_produto,
		float $valor_bruto,
		string $data_vencimento,
		/*string ou null*/ $data_pagamento,
		/*string ou null*/ $data_prestacao,
		float $deducoes,
		/*float ou null*/ $valor_a_pagar,
		string $nota_fiscal,
		/*string ou null*/ $nota_fiscal_a_pagar,
		string $observacao,
		/*string ou null*/ $medidas,
		bool $foi_paga,
		int $numero,
		Persistencia $banco
	) {
		if($data_pagamento === '') //Se essa parâmetro for passado para o banco ele vai setar a data como 00/00/0000
			$data_pagamento = null; // Transformando em null para evitar o problema
		if($data_prestacao === '') 
			$data_prestacao = null;
		if($valor_a_pagar !== null) 
			$valor_a_pagar = (float)$valor_a_pagar;

		$this->id_parcela_contrato = $id_parcela_contrato;
		$this->id_contrato = $id_contrato;
		$this->id_produto = $id_produto;
		$this->valor_bruto = $valor_bruto;
		$this->data_vencimento = $data_vencimento;
		$this->data_pagamento = $data_pagamento;
		$this->data_prestacao = $data_prestacao;
		$this->deducoes = $deducoes;
		$this->valor_a_pagar = $valor_a_pagar;
		$this->nota_fiscal = $nota_fiscal;
		$this->nota_fiscal_a_pagar = $nota_fiscal_a_pagar;
		$this->observacao = $observacao;
		$this->medidas = $medidas;
		$this->foi_paga = $foi_paga;
		$this->numero = $numero;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdItem(): int {return $this->id_parcela_contrato;}
	public function getContrato(): Contrato {return $this->banco->getContrato($this->id_contrato);}
	public function getProduto() {
		if($this->id_produto === null)
			return null;
		return $this->banco->getProduto($this->id_produto);
	}
	public function getValorBruto(): float {return $this->valor_bruto;}
	public function getDataVencimento(): string {return $this->data_vencimento;}
	public function getDataPagamento() {return $this->data_pagamento;}
	public function getDataPrestacao() {return $this->data_prestacao;}
	public function getDeducoes(): float {return $this->deducoes;}
	public function getValorAPagar() {return $this->valor_a_pagar;}
	public function getNotaFiscal(): string {return $this->nota_fiscal;}
	public function getNotaFiscalAPagar() {return $this->nota_fiscal_a_pagar;}
	public function getObservacao(): string {return $this->observacao;}
	public function getMedidas(): string {return $this->medidas;}
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

	public function setIdProduto($valor): bool {
		if($this->atualizarAtributo('idproduto', $valor)) {
			$this->id_produto = $valor;
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

	public function setDataPrestacao($valor): bool {
		if($this->atualizarAtributo('dataprestacao', $valor)) {
			$this->data_prestacao = $valor;
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

	public function setValorAPagar($valor): bool {
		if($this->atualizarAtributo('valorapagar', $valor)) {
			$this->valor_a_pagar = $valor;
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

	public function setNotaFiscalAPagar($valor): bool {
		if($this->atualizarAtributo('notafiscalapagar', $valor)) {
			$this->nota_fiscal_a_pagar = $valor;
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

	public function setMedidas(string $valor): bool {
		if($this->atualizarAtributo('medidas', $valor)) {
			$this->medidas = $valor;
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
		$produto = ['id' => null, 'nome' => 'DESCONHECIDO'];
		$retorno = $this->getProduto();
		if($retorno !== null) {
			$produto['id'] = $retorno->getIdProduto();
			$produto['nome'] = $retorno->getNomeProduto();
		}
		
		return ['idParcelaContrato' => $this->id_parcela_contrato,
				'idContrato' => $this->id_contrato,
				'produto' => $produto,
				'valorBruto' => $this->valor_bruto,
				'dataVencimento' => $this->data_vencimento,
				'dataPagamento' => $this->data_pagamento,
				'dataPrestacao' => $this->data_prestacao,
				'deducoes' => $this->deducoes,
				'valorAPagar' => $this->valor_a_pagar,
				'notaFiscal' => $this->nota_fiscal,
				'notaFiscalAPagar' => $this->nota_fiscal_a_pagar,
				'observacao' => $this->observacao,
				'medidas' => $this->medidas,
				'foiPaga' => $this->foi_paga,
				'numero' => $this->numero];
	}
	public function toColunasBanco(): array {
		return ['idcontrato' => $this->id_contrato,
				'idproduto' => $this->id_produto,
				'valorbruto' => $this->valor_bruto,
				'datavencimento' => $this->data_vencimento,
				'datepagamento' => $this->data_pagamento,
				'dataprestacao' => $this->data_prestacao,
				'deducoes' => $this->deducoes,
				'valorapagar' => $this->valor_a_pagar,
				'notafiscal' => $this->nota_fiscal,
				'notafiscalapagar' => $this->nota_fiscal_a_pagar,
				'observacao' => $this->observacao,
				'medidas' => $this->medidas,
				'foipaga' => $this->foi_paga,
				'numero' => $this->numero];
	}
}
?>