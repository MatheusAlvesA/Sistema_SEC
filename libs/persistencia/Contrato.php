<?php
namespace Persistencia;

class Contrato extends ModeloDados {
	private $id_contrato;
	private $data_emissao;
	private $data_inicial;
	private $data_final;
	private $data_cancelamento;
	private $id_tipo_acesso;
	private $id_mensageiro;
	private $id_funcionario;
	private $observacao;
	private $ativo;
	private $valor_total;
	private $valor_utilizado;
	private $saldo_disponivel;
	private $id_categoria;
	private $ids_produto;
	private $id_cliente;
	private $nome_cliente;

	function __construct(int $id_contrato,
						string $data_emissao,
						string $data_inicial,
						string $data_final,
						$data_cancelamento, // pode ser string ou nulo
						int $id_tipo_acesso,
						int $id_mensageiro,
						int $id_funcionario,
						string $observacao,
						bool $ativo,
						float $valor_total,
						float $valor_utilizado,
						float $saldo_disponivel,
						int $id_categoria,
						array $ids_produto,
						int $id_cliente,
						string $nome_cliente,
						Persistencia $banco)
	{
		$this->id_contrato = $id_contrato;
		$this->data_emissao = $data_emissao;
		$this->data_inicial = $data_inicial;
		$this->data_final = $data_final;
		$this->data_cancelamento = $data_cancelamento;
		$this->id_tipo_acesso = $id_tipo_acesso;
		$this->id_mensageiro = $id_mensageiro;
		$this->id_funcionario = $id_funcionario;
		$this->observacao = $observacao;
		$this->ativo = $ativo;
		$this->valor_total = $valor_total;
		$this->valor_utilizado = $valor_utilizado;
		$this->saldo_disponivel = $saldo_disponivel;
		$this->id_categoria = $id_categoria;
		$this->ids_produto = $ids_produto;
		$this->id_cliente = $id_cliente;
		$this->nome_cliente = $nome_cliente;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdContrato(): int {return $this->id_contrato;}
	public function getDataEmissao(): string {return $this->data_emissao;}
	public function getDataInicial(): string {return $this->data_inicial;}
	public function getDataFinal(): string {return $this->data_final;}
	public function getDataCancelamento(): string {return $this->data_cancelamento;}
	public function getIdTipoAcesso(): TipoAcesso {return $this->banco->getTipoAcesso($this->id_tipo_acesso);}
	public function getIdMensageiro(): Mensageiro {return $this->banco->getMensageiro($this->id_mensageiro);}
	public function getIdFuncionario(): Funcionario {return $this->banco->getFuncionario($this->id_funcionario);}
	public function getObservacao(): string {return $this->observacao;}
	public function getAtivo(): bool {return $this->ativo;}
	public function getValorTotal(): float {return $this->valor_total;}
	public function getValorUtilizado(): float {return $this->valor_utilizado;}
	public function getSaldoDisponivel(): float {return $this->saldo_disponivel;}
	public function getCategoriaProduto(): CategoriaProduto {return $this->banco->getCategoriaProduto($this->id_categoria);}
	public function getProdutos(): Array {
		$lista = [];
		foreach ($this->ids_produto as $key => $value) {
			array_push($lista, $this->banco->getProduto($value));
		}
		return $lista;
	}
	public function getNomeCliente(): string {return $this->nome_cliente;}
	public function getCliente(): Cliente {return $this->banco->getCliente($this->id_cliente);}
	public function getItensDesteContrato(): array {return $this->banco->getItensdeContrato($this->id_contrato);}

/*
	FUNÇÕES SET
*/
	public function setDataEmissao($valor): bool {
		if(is_null($valor)) return false; // Este elemento não pode ser nulo
		if($this->atualizarAtributo('dataemissao', $valor)) {
			$this->data_emissao = $valor;
			return true;
		}
		return false;
	}
	public function setIdContrato(int $valor): bool {
		if(is_null($valor)) return false; // Este elemento não pode ser nulo
		$this->id_contrato = $valor;
		return true;
	}
	public function setDataInicial($valor): bool {
		if(is_null($valor)) return false; // Este elemento não pode ser nulo
		if($this->atualizarAtributo('datainicial', $valor)) {
			$this->data_inicial = $valor;
			return true;
		}
		return false;
	}
	public function setDataFinal($valor): bool {
		if(is_null($valor)) return false; // Este elemento não pode ser nulo
		if($this->atualizarAtributo('datafinal', $valor)) {
			$this->data_final = $valor;
			return true;
		}
		return false;
	}
	public function setDataCancelamento($valor): bool {
		if($this->atualizarAtributo('datacancelamento', $valor)) {
			$this->data_cancelamento = $valor;
			return true;
		}
		return false;
	}
	public function setIdTipoAceso(int $valor): bool {
		if($this->atualizarAtributo('idtipoacesso', $valor)) {
			$this->id_tipo_acesso = $valor;
			return true;
		}
		return false;
	}
	public function setIdMensageiro(int $valor): bool {
		if($this->atualizarAtributo('idmensageiro', $valor)) {
			$this->id_mensageiro = $valor;
			return true;
		}
		return false;
	}
	public function setIdFuncionario(int $valor): bool {
		if($this->atualizarAtributo('idfuncionario', $valor)) {
			$this->id_funcionario = $valor;
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
	public function setAtivo(bool $valor): bool {
		// Convertendo o valor booleano em inteiro para garantir compatibiliadade com o banco de dados
		$valor_convertido = 0; //false
		if($valor == true) $valor_convertido = 1; //true

		if($this->atualizarAtributo('ativo', $valor_convertido)) {
			$this->ativo = $valor;
			return true;
		}
		return false;
	}
	public function setValorTotal(float $valor): bool {
		if($this->atualizarAtributo('valor_total', $valor)) {
			$this->valor_total = $valor;
			return true;
		}
		return false;
	}
	public function setIdCategoria(int $valor): bool {
		if($this->atualizarAtributo('id_categoria', $valor)) {
			$this->id_categoria = $valor;
			return true;
		}
		return false;
	}
	public function setIdProdutos(array $valores): bool {
		$produtos = array_map(function($v) {return $v['id'];}, $this->banco->getProdutos());  // lista dos ids de todos os produtos que existem
		
		foreach ($valores as $key => $value) // percorre todos os valores
			if(!in_array($value, $produtos)) // se qualquer valor não for um id de produto válido...
				throw new Exception("Erro na função setIdProdutos. Produto: {$value} inválido"); // ...lance uma exceção	

		return $this->banco->setProdutosDoContrato($this->id_contrato, $valores);
	}
	public function setIdCliente(int $valor): bool {
		if($this->atualizarAtributo('idcliente', $valor)) {
			$this->id_cliente = $valor;
			return true;
		}
		return false;
	}

	protected function atualizarAtributo($chave, $valor): bool {
		return parent::_atualizarAtributo('contrato', $chave, $valor, 'idcontrato', $this->id_contrato);
	}

	public function toArray(): array {
		return ['idContrato' => $this->id_contrato,
				'idMensageiro' => $this->id_mensageiro,
				'dataEmissao' => $this->data_emissao,
				'dataInicial' => $this->data_inicial,
				'dataFinal' => $this->data_final,
				'dataCancelamento' => $this->data_cancelamento,
				'idTipoAcesso' => $this->id_tipo_acesso,
				'idMensageiro' => $this->id_mensageiro,
				'idFuncionario' => $this->id_funcionario,
				'observacao' => $this->observacao,
				'ativo' => $this->ativo,
				'valorTotal' => $this->valor_total,
				'idCategoria' => $this->id_categoria,
				'idsProdutos' => $this->ids_produto,
				'idCliente' => $this->id_cliente,
				'valorUtilizado' => $this->valor_utilizado,
				'saldoDisponivel' => $this->saldo_disponivel,
				'nomeCliente' => $this->nome_cliente];
	}

/*
	Diferente de toArray, essa função retorna um vetor com chaves iguais as colunas do banco de dados
*/
	public function toColunasBanco(): array {
		return ['idmensageiro' => $this->id_mensageiro,
				'dataemissao' => $this->data_emissao,
				'datainicial' => $this->data_inicial,
				'datafinal' => $this->data_final,
				'datacancelamento' => $this->data_cancelamento,
				'idtipoacesso' => $this->id_tipo_acesso,
				'idmensageiro' => $this->id_mensageiro,
				'idFuncionario' => $this->id_funcionario,
				'observacao' => $this->observacao,
				'ativo' => (int)$this->ativo,
				'valor_total' => $this->valor_total,
				'id_categoria' => $this->id_categoria,
				'idcliente' => $this->id_cliente
				];
	}
}
?>