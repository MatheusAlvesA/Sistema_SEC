<?php
namespace Sistema;

use Persistencia\Persistencia;
use Persistencia\Relatorio;
use Persistencia\PersistenciaException;

class Sistema {
	private $persistencia;

	function __construct() {
		try {
			$this->persistencia = new Persistencia();
		}
		catch(PersistenciaException $e) {
			Logger::logar($e);
			throw new SistemaException('Falha ao obter a conexão com o banco de dados', 0, $e);
		}
	}

	public function buscarNomeCliente(string $nome) {
		$lista = $this->listarNomesCliente();
		$parecidos = [];
		foreach ($lista as $key => $value) {
			$resultado = $this::strParecidas($value['nome'], $nome);
			if($resultado == 1 || $resultado == 2) {
				array_push($parecidos, $value);
			}
		}
		return $parecidos;
	}

	public function buscarCliente(string $nome): Array {
		$lista = $this->listarNomesCliente();
		$parecidos = [];
		foreach ($lista as $key => $value) {
			$resultado = $this::strParecidas($value['nome'], $nome);
			if($resultado == 1 || $resultado == 2) {
				array_push($parecidos, $this->persistencia->getCliente((int)$value['id'])->toArray());
			}
		}
		return $parecidos;
	}

	public function listarNomesCliente(): Array {
		try {
			return $this->persistencia->getNomeClientes();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarFuncionarios(): Array {
		try {
			return $this->persistencia->getFuncionarios();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarMensageiros(): Array {
		try {
			return $this->persistencia->getMensageiros();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarTiposAcesso(): Array {
		try {
			return $this->persistencia->getTiposAcesso();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarProdutos(): Array {
		try {
			return $this->persistencia->getProdutos();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarUFs(): Array {
		try {
			return $this->persistencia->getUFs();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarCategoriasProduto(): Array {
		try {
			return $this->persistencia->getCategoriasProduto();
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarEmails(): Array {
		try {
			return $this->persistencia->getRelatorio(Relatorio::EMAIL);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarClientesEmAtraso(): Array {
		try {
			return $this->persistencia->getRelatorio(Relatorio::INADIMPLENTES);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
	}
	public function listarItensNotaFiscal(int $numero): Array {
		$retorno = [];
		try {
			$retorno = $this->persistencia->getRelatorio(Relatorio::NOTAFISCAL, $numero);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}

		if(count($retorno) > 0) {
			$cliente = $retorno[0]->getContrato()->getNomeCliente();
		}

		$lista = array_map(function($item) {
			return $item->toArray();
		}, $retorno);

		return ['cliente' => $cliente, 'itens' => $lista];
	}

	public function listarClientes() {
		$nomes = $this->listarNomesCliente();

		$clientes = [];
		try {
			foreach ($nomes as $key => $value)
				array_push($clientes, $this->persistencia->getCliente($value['id'])->toArray());
		}
		catch(PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
		return $clientes;
	}

	public function listarClientesAtivos() {
		$clientes = $this->listarClientes();

		$ativos = [];
		foreach ($clientes as $key => $value)
			if($value['ativo'])
				array_push($ativos, $value);

		return $ativos;
	}

	public function listarClientesInativos() {
		$clientes = $this->listarClientes();

		$inativos = [];
		foreach ($clientes as $key => $value)
			if(!$value['ativo'])
				array_push($inativos, $value);

		return $inativos;
	}

	public function listarItensContrato(int $id) {
		try {
			$itens = $this->persistencia->getItensdeContrato($id);
		}
		catch(PersistenciaException $e) {
			Logger::logar($e);
			return [];
		}
		$acumulador = [];
		
		foreach ($itens as $chave => $valor) {
			$acumulador[$chave] = $valor->toArray();
		}

		return $acumulador;
	}


	public function listarContratosCliente(int $id_cliente) {
		try {
			$contratos = $this->persistencia->getContratosDoCliente($id_cliente);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return []; // nenhum contrato encontrado
		}
		$acumulador = [];
		foreach ($contratos as $key => $value) {
			$acumulador[$key] = $value->toArray();
		}

		return $acumulador;
	}

	public function listarServicosFinalizadosUltimos12Meses() { //todo
		try {
			$r = $this->persistencia->getRelatorio(Relatorio::itensPagosNosUltimos12Meses);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return [ 'nServicos' => [], 'valorServicos' => [] ];
		}
		// Processando dados
		usort($r, function($a, $b) {
			$dataA = explode('-', $a->getDataPagamento());
			$dataB = explode('-', $b->getDataPagamento());

			if($dataA[0] < $dataB[0]) return -1;
			if($dataA[0] > $dataB[0]) return  1;

			if($dataA[1] < $dataB[1]) return -1;
			if($dataA[1] > $dataB[1]) return  1;

			if($dataA[2] < $dataB[2]) return -1;
			if($dataA[2] > $dataB[2]) return  1;

			return 0;
		});

		$nServicos = [];
		$valorServicos = [];
		foreach ($r as $chave => $item) {
			$nMes = (int) explode('-', $item->getDataPagamento())[1];
			$mes = $this->int2Mes( $nMes );

			if(array_key_exists($mes, $nServicos))
				$nServicos[$mes] += 1;
			else
				$nServicos[$mes] = 1;

			if(array_key_exists($mes, $valorServicos))
				$valorServicos[$mes] += round($item->getValorBruto()-$item->getDeducoes());
			else
				$valorServicos[$mes] = round($item->getValorBruto()-$item->getDeducoes());
		}

		return [
			'nServicos' => $nServicos,
			'valorServicos' => $valorServicos
		];
	}
	// Esta função recebe um inteiro de 1 até 12 e retorna o mês respectivo em 3 letas
	private function int2Mes(int $m): string {
		switch ($m) {
			case 1:
				return 'JAN';
				break;
			case 2:
				return 'FEV';
				break;
			case 3:
				return 'MAR';
				break;
			case 4:
				return 'ABR';
				break;
			case 5:
				return 'MAI';
				break;
			case 6:
				return 'JUN';
				break;
			case 7:
				return 'JUL';
				break;
			case 8:
				return 'AGO';
				break;
			case 9:
				return 'SET';
				break;
			case 10:
				return 'OUT';
				break;
			case 11:
				return 'NOV';
				break;
			case 12:
				return 'DEZ';
				break;

			default:
				return 'ERRO';
				break;
		}
	}

	public function criarContrato(bool $ativo,
		$dataCancelamento, // esse elemento pode ser string ou nulo
		string $dataEmissao,
		string $dataFinal,
		string $dataInicial,
		int $idCategoria,
		int $idCliente,
		int $idFuncionario, 
		int $idMensageiro,
		int $idTipoAcesso,
		$idsProdutos,
		string $observacao,
		float $valorTotal): bool
	{
		$novoContrato = new \Persistencia\Contrato(0, // será preenchido automaticamente
			$dataEmissao,
			$dataInicial,
			$dataFinal,
			$dataCancelamento,
			$idTipoAcesso,
			$idMensageiro,
			$idFuncionario,
			$observacao,
			$ativo,
			$valorTotal,
			0, // será preenchido automaticamente
			0, // será preenchido automaticamente
			$idCategoria,
			$idsProdutos,
			$idCliente,
			'', // será preenchido automaticamente
			$this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoContrato);
			if($r) {
				$novoContrato->setIdProdutos($idsProdutos);
			}
			return $r;
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarCliente(
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
			string $titular_cpf): bool
	{
		if($nome_cliente == '') return false; //Precisa ter pelo menos o nome

		$novoCliente = new \Persistencia\Cliente(0,
			$nome_cliente,
			$logradouro,
			$bairro,
			$cidade,
			$id_uf,
			$cep,
			$telefone,
			$fax,
			$email,
			$celular,
			$homepage,
			$titular,
			$contato,
			$cgc_cpf,
			$ativo,
			$forense,
			$observacao,
			$inscestadual,
			$inscmunicipal,
			$complemento,
			$titular_cpf,
			$this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoCliente);
			return $r;
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarItem(
		int $id_contrato,
		$id_produto,
		float $valor_bruto,
		string $data_vencimento,
		$data_pagamento, // Pode ser string ou nulo
		$data_prestacao,
		float $deducoes,
		$valor_a_pagar,
		$nota_fiscal,
		$nota_fiscal_a_pagar,
		string $observacao,
		$medidas,
		bool $foi_paga,
		int $numero)
	{
		$novoItem = new \Persistencia\ItemContrato(0,
			$id_contrato,
			$id_produto,
			$valor_bruto,
			$data_vencimento,
			$data_pagamento,
			$data_prestacao,
			$deducoes,
			$valor_a_pagar,
			$nota_fiscal,
			$nota_fiscal_a_pagar,
			$observacao,
			$medidas,
			$foi_paga,
			$numero,
			$this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoItem);
			if($r)
				return $novoItem->getIdItem();
			return false;
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarFuncionario(string $nome): bool {
		$novoFuncionario = new \Persistencia\Funcionario(0, $nome, $this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoFuncionario);
			return $r;
		} catch(Exception $e) { // O banco não aceitou o novo funcionario por qualquer razão que seja
			return false;
		}
	}

	public function criarMensageiro(string $nome): bool {
		$novoMensageiro = new \Persistencia\Mensageiro(0, $nome, $this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoMensageiro);
			return $r;
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarTipoAcesso(string $nome): bool {
		$novotipoAcesso = new \Persistencia\TipoAcesso(0, $nome, $this->persistencia);
		try {
			$r = $this->persistencia->inserir($novotipoAcesso);
			return $r;
		}  catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarCategoriaProduto(string $nome): bool {
		$novaCategoriaProduto = new \Persistencia\CategoriaProduto(0, $nome, $this->persistencia);
		try {
			$r = $this->persistencia->inserir($novaCategoriaProduto);
			return $r;
		}  catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function criarProduto(string $nome, int $categoria): bool {
		$novoProduto = new \Persistencia\Produto(0, $categoria, $nome, $this->persistencia);
		try {
			$r = $this->persistencia->inserir($novoProduto);
			return $r;
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function deletarContrato(int $id): bool {
		try {
			return $this->persistencia->deleteContrato($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarCliente(int $id): bool {
		try {
			return $this->persistencia->deleteCliente($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarItem(int $id): bool {
		try {
			return $this->persistencia->deleteItem($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarFuncionario(int $id): bool {
		try {
			return $this->persistencia->deleteFuncionario($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarMensageiro(int $id): bool {
		try {
			return $this->persistencia->deleteMensageiro($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarTipoAcesso(int $id): bool {
		try {
			return $this->persistencia->deleteTipoAcesso($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarCategoriaProduto(int $id): bool {
		try {
			return $this->persistencia->deleteCategoriaProduto($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}
	public function deletarProduto(int $id): bool {
		try {
			return $this->persistencia->deleteProduto($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}
	}

	public function atualizarContrato(int $id, array $lista): bool {
		try {
			$contrato = $this->persistencia->getContrato($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}

		$sucesso = true;

		foreach ($lista as $key => $elemento) {
			if(!$this->aplicarAtualizacaoContrato($contrato, $elemento))
				$sucesso = false;
		}

		return $sucesso;
	}

	private function aplicarAtualizacaoContrato($contrato, $elemento): bool {
		/*
			Qualquer uma dessas operações pode acarretar em uma exceção causada por uma falha de acesso ao banco ou um parâmetro inválido
		*/

		if(isset($elemento['ativo'])) {
			try {return $contrato->setAtivo((bool) $elemento['ativo']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idCliente'])) {
			try {return $contrato->setIdCliente((int) $elemento['idCliente']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idsProdutos'])) {
			try {return $contrato->setIdProdutos($elemento['idsProdutos']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idCategoria'])) {
			try {return $contrato->setIdCategoria((int) $elemento['idCategoria']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idFuncionario'])) {
			try {return $contrato->setIdFuncionario((int) $elemento['idFuncionario']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idMensageiro'])) {
			try {return $contrato->setIdMensageiro((int) $elemento['idMensageiro']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['idTipoAcesso'])) {
			try {return $contrato->setIdTipoAceso((int) $elemento['idTipoAcesso']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['dataEmissao'])) {
			try {return $contrato->setDataEmissao($elemento['dataEmissao']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['dataFinal'])) {
			try {return $contrato->setDataFinal($elemento['dataFinal']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['dataInicial'])) {
			try {return $contrato->setDataInicial($elemento['dataInicial']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['valorTotal'])) {
			try {return $contrato->setValorTotal((float) $elemento['valorTotal']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['observacao'])) {
			try {return $contrato->setObservacao($elemento['observacao']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['dataCancelamento']) || is_null($elemento['dataCancelamento'])) { // este tem que vir por ultimo
			try {return $contrato->setDataCancelamento($elemento['dataCancelamento']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		return false; // Não encontrou o que era pra atualizar
	}

	public function atualizarCliente(int $id, array $lista): bool {
		try {
			$cliente = $this->persistencia->getCliente($id);
		} catch (PersistenciaException $e) {
			Logger::logar($e);
			return false;
		}

		$sucesso = true;

		foreach ($lista as $key => $elemento) {
			if(!$this->aplicarAtualizacaoCliente($cliente, $elemento))
				$sucesso = false;
		}

		return $sucesso;
	}
	private function aplicarAtualizacaoCliente($cliente, $elemento): bool {
		/*
			Qualquer uma dessas operações pode acarretar em uma exceção causada por uma falha de acesso ao banco ou um parâmetro inválido
		*/

		if(isset($elemento['nomeCliente'])) {
			try {return $cliente->setNome($elemento['nomeCliente']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['logradouro'])) {
			try {return $cliente->setLogradouro($elemento['logradouro']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['bairro'])) {
			try {return $cliente->setBairro($elemento['bairro']);}
			catch(Exception $e) {return false;}
		}
		if(isset($elemento['cidade'])) {
			try {return $cliente->setCidade($elemento['cidade']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['UF'])) {
			try {return $cliente->setUF($elemento['UF']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['cep'])) {
			try {return $cliente->setCep($elemento['cep']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['telefone'])) {
			try {return $cliente->setTelefone($elemento['telefone']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['fax'])) {
			try {return $cliente->setFax($elemento['fax']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['email'])) {
			try {return $cliente->setEmail($elemento['email']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['celular'])) {
			try {return $cliente->setCelular($elemento['celular']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['homepage'])) {
			try {return $cliente->setHomepage($elemento['homepage']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['titular'])) {
			try {return $cliente->setTitular($elemento['titular']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['contato'])) {
			try {return $cliente->setContato($elemento['contato']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['cgcCpf'])) {
			try {return $cliente->setCgcCpf($elemento['cgcCpf']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['ativo'])) {
			try {return $cliente->setAtivo((bool) $elemento['ativo']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['inscEstadual'])) {
			try {return $cliente->setInscEstadual($elemento['inscEstadual']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['inscMunicipal'])) {
			try {return $cliente->setInscMunicipal($elemento['inscMunicipal']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['complemento'])) {
			try {return $cliente->setComplemento($elemento['complemento']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['titularCpf'])) {
			try {return $cliente->setTitularCpf($elemento['titularCpf']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		if(isset($elemento['observacao'])) {
			try {return $cliente->setObservacao($elemento['observacao']);}
			catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}
		}
		return false; // Não encontrou o que era pra atualizar
	}

	public function atualizarItem(int $id, $dados): bool {
			try {
				$item = $this->persistencia->getItemContrato($id);
			} catch (PersistenciaException $e) {
				Logger::logar($e);
				return false;
			}

			if(array_key_exists('idProduto', $dados)) { //Pode ser nulo, evitando usar isset
				if($dados['idProduto'] !== null && gettype($dados['idProduto']) !== 'integer') // Este valor só pode ser nulo ou inteiro
					return false;

				try {$item->setIdProduto($dados['idProduto']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(isset($dados['valorBruto'])) {
				try {$item->setValorBruto((float) $dados['valorBruto']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}
			
			if(isset($dados['dataVencimento'])) {
				try {$item->setDataVencimento((string) $dados['dataVencimento']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(array_key_exists('dataPagamento', $dados)) {
				if($dados['dataPagamento'] === '') $dados['dataPagamento'] = null;
				try {$item->setDataPagamento($dados['dataPagamento']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(array_key_exists('dataPrestacao', $dados)) { //Pode ser nulo, evitando usar isset
				if($dados['dataPrestacao'] !== null && gettype($dados['dataPrestacao']) !== 'string') // Este valor só pode ser nulo ou string
					return false;

				if($dados['dataPrestacao'] === '') $dados['dataPrestacao'] = null;
				try {$item->setDataPrestacao($dados['dataPrestacao']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(isset($dados['deducoes'])) {
				try {$item->setDeducoes((float) $dados['deducoes']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(array_key_exists('valorAPagar', $dados)) { //Pode ser nulo, evitando usar isset
				if($dados['valorAPagar'] !== null && gettype($dados['valorAPagar']) !== 'integer' && gettype($dados['valorAPagar']) !== 'double') // Este valor só pode ser nulo, inteiro ou double
					return false;

				try {$item->setValorAPagar($dados['valorAPagar']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(isset($dados['notaFiscal'])) {
				try {$item->setNotaFiscal((string) $dados['notaFiscal']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(array_key_exists('notaFiscalAPagar', $dados)) { //Pode ser nulo, evitando usar isset
				if($dados['notaFiscalAPagar'] !== null && gettype($dados['notaFiscalAPagar']) !== 'string') // Este valor só pode ser nulo ou string
					return false;

				try {$item->setNotaFiscalAPagar($dados['notaFiscalAPagar']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(isset($dados['observacao'])) {
				try {$item->setObservacao((string) $dados['observacao']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(array_key_exists('medidas', $dados)) { //Pode ser nulo, evitando usar isset
				if($dados['medidas'] !== null && gettype($dados['medidas']) !== 'string') // Este valor só pode ser nulo ou string
					return false;

				try {$item->setMedidas($dados['medidas']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

			if(isset($dados['numero'])) {
				try {$item->setNumero((int) $dados['numero']);}
				catch (PersistenciaException $e) {
					Logger::logar($e);
					return false;
				}
			}

		return true;
	}

	public static function limparLogErros() {Logger::limpar();}

	/*
		Essa função retorna 0 se as strings passadas forem muito diferentes
		retorna 1 se forem parecidas
		retorna 2 se forem identicas
	*/
	public static function strParecidas(String $str1, String $str2): int {
		if($str1 == '' || $str2 == '') return 0; // Não dá para comparar se a string for vazia

		$str1 = \preg_replace(['/Á/','/É/','/Í/','/Ó/','/Ú/','/Â/','/Ê/','/Î/','/Ô/','/Û/','/Ã/','/Õ/'], ['A','E','I','O','U','A','E','I','O','U','A','O'] , mb_strtoupper($str1));
		$str2 = \preg_replace(['/Á/','/É/','/Í/','/Ó/','/Ú/','/Â/','/Ê/','/Î/','/Ô/','/Û/','/Ã/','/Õ/'], ['A','E','I','O','U','A','E','I','O','U','A','O'] , mb_strtoupper($str2));

		if($str1 == $str2) return 2; // strings perfeitamente iguais
		if(mb_strlen($str1) == mb_strlen($str2)) return 0; // tem o mesmo tamanho mas não são iguais

		$menor = '';
		$maior = '';
		if(mb_strlen($str1) < mb_strlen($str2)) {
			$menor = $str1;
			$maior = $str2;
		} else {
			$menor = $str2;
			$maior = $str1;
		}

		if(strpos($maior, $menor) !== false)
			return 1;

		return 0;
	}
}
?>