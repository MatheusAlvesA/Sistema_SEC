<?php
namespace Persistencia;

class Cliente extends ModeloDados {
	private $id_cliente;
	private $nome_cliente;
	private $logradouro;
	private $bairro;
	private $cidade;
	private $uf;
	private $cep;
	private $telefone;
	private $fax;
	private $email;
	private $celular;
	private $homepage;
	private $titular;
	private $contato;
	private $cgc_cpf;
	private $ativo;
	private $forense;
	private $observacao;
	private $inscestadual;
	private $inscmunicipal;
	private $complemento;
	private $titular_cpf;

	function __construct(int $id_cliente,
						string $nome_cliente,
						string $logradouro,
						string $bairro,
						string $cidade,
						string $id_uf,
						string $cep,
						string $telefone,
						string $fax,
						string $email,
						string $celular,
						string $homepage,
						string $titular,
						string $contato,
						string $cgc_cpf,
						bool $ativo,
						bool $forense,
						string $observacao,
						string $inscestadual,
						string $inscmunicipal,
						string $complemento,
						string $titular_cpf,
						Persistencia $banco)
	{
		$this->id_cliente = $id_cliente;
		$this->nome_cliente = $nome_cliente;
		$this->logradouro = $logradouro;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->uf = $id_uf;
		$this->cep = $cep;
		$this->telefone = $telefone;
		$this->fax = $fax;
		$this->email = $email;
		$this->celular = $celular;
		$this->homepage = $homepage;
		$this->titular = $titular;
		$this->contato = $contato;
		$this->cgc_cpf = $cgc_cpf;
		$this->ativo = $ativo;
		$this->forense = $forense;
		$this->observacao = $observacao;
		$this->inscestadual = $inscestadual;
		$this->inscmunicipal = $inscmunicipal;
		$this->complemento = $complemento;
		$this->titular_cpf = $titular_cpf;
		$this->banco = $banco;
	}

/*
	FUNÇÕES GET
*/
	public function getIdCliente(): int {return $this->id_cliente;}
	public function getNome(): string {return $this->nome_cliente;}
	public function getLogradouro(): string {return $this->logradouro;}
	public function getBairro(): string {return $this->bairro;}
	public function getCidade(): string {return $this->cidade;}
	public function getUF(): string {return $this->banco->getUFNome($this->uf);}
	public function getCep(): String {return $this->cep;}
	public function getTelefone(): String {return $this->telefone;}
	public function getFax(): String {return $this->fax;}
	public function getEmail(): String {return $this->email;}
	public function getCelular(): String {return $this->celular;}
	public function getHomepage(): string {return $this->homepage;}
	public function getTitular(): String {return $this->titular;}
	public function getContato(): String {return $this->contato;}
	public function getCgcCpf(): String {return $this->cgc_cpf;}
	public function getAtivo(): bool {return $this->ativo;}
	public function getForense(): bool {return $this->forense;}
	public function getObservacao(): String {return $this->observacao;}
	public function getInscestadual(): String {return $this->inscestadual;}
	public function getInscmunicipal(): String {return $this->inscmunicipal;}
	public function getComplemento(): String {return $this->complemento;}
	public function getTitularCpf(): String {return $this->titular_cpf;}
	public function getContratos(): Array {return $this->banco->getContratosDoCliente($this->id);}

/*
	FUNÇÕES SET
*/
	public function setNome(string $valor): bool {
		if($this->atualizarAtributo('nomecliente', $valor)) {
			$this->nome_cliente = $valor;
			return true;
		}
		return false;
	}
	public function setLogradouro(string $valor): bool {
		if($this->atualizarAtributo('logradouro', $valor)) {
			$this->logradouro = $valor;
			return true;
		}
		return false;
	}
	public function setBairro(string $valor): bool {
		if($this->atualizarAtributo('bairro', $valor)) {
			$this->bairro = $valor;
			return true;
		}
		return false;
	}
	public function setCidade(string $valor): bool {
		if($this->atualizarAtributo('cidade', $valor)) {
			$this->cidade = $valor;
			return true;
		}
		return false;
	}
	public function setUF(string $valor): bool {
		if($this->atualizarAtributo('uf', $valor)) {
			$this->uf = $valor;
			return true;
		}
		return false;
	}
	public function setCep(string $valor): bool {
		if($this->atualizarAtributo('cep', $valor)) {
			$this->cep = $valor;
			return true;
		}
		return false;
	}
	public function setTelefone(string $valor): bool {
		if($this->atualizarAtributo('telefone', $valor)) {
			$this->telefone = $valor;
			return true;
		}
		return false;
	}
	public function setFax(string $valor): bool {
		if($this->atualizarAtributo('fax', $valor)) {
			$this->fax = $valor;
			return true;
		}
		return false;
	}
	public function setEmail(string $valor): bool {
		if($this->atualizarAtributo('email', $valor)) {
			$this->email = $valor;
			return true;
		}
		return false;
	}
	public function setCelular(string $valor): bool {
		if($this->atualizarAtributo('celular', $valor)) {
			$this->celular = $valor;
			return true;
		}
		return false;
	}
	public function setHomepage(string $valor): bool {
		if($this->atualizarAtributo('homepage', $valor)) {
			$this->homepage = $valor;
			return true;
		}
		return false;
	}
	public function setTitular(string $valor): bool {
		if($this->atualizarAtributo('titular', $valor)) {
			$this->titular = $valor;
			return true;
		}
		return false;
	}
	public function setContato(string $valor): bool {
		if($this->atualizarAtributo('contato', $valor)) {
			$this->contato = $valor;
			return true;
		}
		return false;
	}
	public function setCgcCpf(string $valor): bool {
		if($this->atualizarAtributo('cgc_cpf', $valor)) {
			$this->cgc_cpf = $valor;
			return true;
		}
		return false;
	}
	public function setAtivo(bool $valor): bool {
		// Convertendo o valor booleano em inteiro para garantir compatibiliadade com o banco de dados
		$valor_convertido = 0; //false
		if($valor == true) $valor_convertido = 1; //true

		if($this->atualizarAtributo('ehativo', $valor_convertido)) {
			$this->ativo = $valor;
			return true;
		}
		return false;
	}
	public function setForense(bool $valor): bool {
		// Convertendo o valor booleano em inteiro para garantir compatibiliadade com o banco de dados
		$valor_convertido = 0; //false
		if($valor == true) $valor_convertido = 1; //true

		if($this->atualizarAtributo('forense', $valor_convertido)) {
			$this->forense = $valor;
			return true;
		}
		return false;
	}
	public function setInscEstadual(string $valor): bool {
		if($this->atualizarAtributo('inscestadual', $valor)) {
			$this->inscestadual = $valor;
			return true;
		}
		return false;
	}
	public function setInscMunicipal(string $valor): bool {
		if($this->atualizarAtributo('inscmunicipal', $valor)) {
			$this->inscmunicipal = $valor;
			return true;
		}
		return false;
	}
	public function setComplemento(string $valor): bool {
		if($this->atualizarAtributo('complemento', $valor)) {
			$this->complemento = $valor;
			return true;
		}
		return false;
	}
	public function setTitularCpf(string $valor): bool {
		if($this->atualizarAtributo('titular_cpf', $valor)) {
			$this->titular_cpf = $valor;
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

	private function atualizarAtributo($atributo, $valor) {
		return parent::_atualizarAtributo('cliente', $atributo, $valor, 'idcliente', $this->id_cliente);
	}

	public function toArray(): array {
		return ['idCliente' => $this->id_cliente,
			'nomeCliente' => $this->nome_cliente,
			'logradouro' => $this->logradouro,
			'bairro' => $this->bairro,
			'cidade' => $this->cidade,
			'UF' => $this->uf,
			'cep' => $this->cep,
			'telefone' => $this->telefone,
			'fax' => $this->fax,
			'email' => $this->email,
			'celular' => $this->celular,
			'homepage' => $this->homepage,
			'titular' => $this->titular,
			'contato' => $this->contato,
			'cgcCpf' => $this->cgc_cpf,
			'ativo' => $this->ativo,
			'forense' => $this->forense,
			'inscEstadual' => $this->inscestadual,
			'inscMunicipal' => $this->inscmunicipal,
			'complemento' => $this->complemento,
			'titularCpf' => $this->titular_cpf,
			'observacao' => $this->observacao];
	}
	public function toColunasBanco(): array {
		return ['nomecliente' => $this->nome_cliente,
				'logradouro' => $this->logradouro,
				'bairro' => $this->bairro,
				'cidade' => $this->cidade,
				'uf' => $this->uf,
				'cep' => $this->cep,
				'telefone' => $this->telefone,
				'fax' => $this->fax,
				'email' => $this->email,
				'celular' => $this->celular,
				'homepage' => $this->homepage,
				'titular' => $this->titular,
				'contato' => $this->contato,
				'cgc_cpf' => $this->cgc_cpf,
				'ehativo' => $this->ativo,
				'forense' => $this->forense,
				'inscestadual' => $this->inscestadual,
				'inscmunicipal' => $this->inscmunicipal,
				'complemento' => $this->complemento,
				'titular_cpf' => $this->titular_cpf,
			'observacao' => $this->observacao];
	}
}
?>