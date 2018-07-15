<?php
namespace Persistencia;
require_once '../config.php';

use Config;

class Persistencia {
	private $con; // A conexão com o banco, Instancia da classe PDO

	function __construct() {
  		try {
   			@$this->con = new \PDO('mysql:host='.Config::SQL_host.';dbname='.Config::SQL_db, Config::SQL_user, Config::SQL_pass);
  		}
  		catch(\PDOException $e) {
  			$nova = new PersistenciaException('Falha na conexão com o banco de dados', 0, $e);
  			$nova->setEstado('host: '.Config::SQL_host.', banco: '.Config::SQL_db.', User: '.Config::SQL_user.', Senha: '.Config::SQL_pass.'.');
      		throw $nova;
  		}
	}

	public function getContrato(int $id): Contrato {
		$consulta = $this->con->prepare('SELECT * FROM vcontrato WHERE idcontrato = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do contrato

		$rs = $consulta->fetchAll(); //extraindo a matriz corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("O contrato buscado não foi encontrado");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0]; // Transformando a matriz de uma linha em um vetor comum

		$lista_produtos = $this->listarProdutosDoContrato($id);

		return new Contrato((int) $rs['idcontrato'],
							utf8_encode($rs['dataemissao']),
							utf8_encode($rs['datainicial']),
							utf8_encode($rs['datafinal']),
							utf8_encode($rs['datacancelamento']),
							(int) $rs['idtipoacesso'],
							(int) $rs['idmensageiro'],
							(int) $rs['idFuncionario'],
							utf8_encode($rs['observacao']),
							(bool) $rs['ativo'],
							(float) $rs['valor_total'],
							(float) $rs['valor_utilizado'],
							(float) $rs['saldo_disponivel'],
							(int) $rs['id_categoria'],
							$lista_produtos,
							(int) $rs['idcliente'],
							utf8_encode($rs['nomecliente']),
							$this);
	}

	public function deleteContrato(int $id): bool {
		$deletarProdutos = $this->con->prepare('DELETE FROM contratoproduto WHERE idcontrato = :id;'); // preparando
		$deletarContrato = $this->con->prepare('DELETE FROM contrato WHERE idcontrato = :id;'); // preparando
		$deletarParcelas = $this->con->prepare('DELETE FROM parcelacontrato WHERE idcontrato = :id;'); // preparando

		$r = false;
		$deletarProdutos->execute(array('id' => $id)); // Deletando primeiro os produtos associados aquele contrato
		$deletarParcelas->execute(array('id' => $id)); // Deletando agora os itens associados aquele contrato
		$r = $deletarContrato->execute(array('id' => $id)); // agora deletando o contrato
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar o contrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarContrato->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteFuncionario(int $id): bool {
		$deletarFuncionario = $this->con->prepare('DELETE FROM funcionario WHERE idfuncionario = :id;'); // preparando

		$r = false;
		$r = $deletarFuncionario->execute(array('id' => $id)); // Deletando
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar funcionário");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarFuncionario->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteMensageiro(int $id): bool {
		$deletarMensageiro = $this->con->prepare('DELETE FROM mensageiro WHERE idmensageiro = :id;'); // preparando

		$r = false;
		$r = $deletarMensageiro->execute(array('id' => $id)); // Deletando mensageiro
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar Mensageiro");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarMensageiro->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteCliente(int $id): bool {
		$deletarCliente = $this->con->prepare('DELETE FROM cliente WHERE idcliente = :id;'); // preparando

		$r = false;
		$r = $deletarCliente->execute(array('id' => $id)); // Deletando cliente
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar cliente");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarCliente->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteTipoAcesso(int $id): bool {
		$deletarTipoAcesso = $this->con->prepare('DELETE FROM tipoacesso WHERE idtipoacesso = :id;'); // preparando

		$r = false;
		$r = $deletarTipoAcesso->execute(array('id' => $id)); // Deletando mensageiro
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar tipo de acesso");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarTipoAcesso->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteCategoriaProduto(int $id): bool {
		$deletarCategoriaProduto = $this->con->prepare('DELETE FROM produtocategoria WHERE idprodutocategoria = :id;'); // preparando

		$r = false;
		$r = $deletarCategoriaProduto->execute(array('id' => $id)); // Deletando mensageiro
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar a Categoria de Produto");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarCategoriaProduto->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteProduto(int $id): bool {
		$deletarProduto = $this->con->prepare('DELETE FROM produto WHERE idproduto = :id;'); // preparando

		$r = false;
		$r = $deletarProduto->execute(array('id' => $id)); // Deletando mensageiro
		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar o Produto");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarProduto->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	public function deleteItem(int $id): bool {
		$deletarParcela = $this->con->prepare('DELETE FROM parcelacontrato WHERE idparcelaContrato = :id;'); // preparando

		$r = false;
		$r = $deletarParcela->execute(array('id' => $id)); // Deletando a parcela

		if(!$r) {
			$e = new PersistenciaException("Não foi possível deletar o item");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $deletarParcela->errorInfo() ) );
			throw $e;
		}

		return $r;
	}

	private function listarProdutosDoContrato(int $id): array {
		$consulta_produtos = $this->con->prepare('SELECT * FROM contratoproduto WHERE idcontrato = :id;'); // preparando
		$consulta_produtos->execute(array('id' => $id)); // executando em cima do id do contrato

		$rs = $consulta_produtos->fetchAll(); //extraindo o vetor corespondente ao resultado
		$lista_produtos = [];
		foreach ($rs as $key => $value) {
			array_push($lista_produtos, (int)$value['idproduto']);
		}

		return $lista_produtos;
	}

	public function listarDadosClientesEmAtraso(): Array {
		$consulta = $this->con->prepare('
		SELECT 
	        `v`.`idcontrato` AS `idcontrato`,
	        `c`.`idcliente` AS `idcliente`,
	        `c`.`nomecliente` AS `nomecliente`,
	        `c`.`logradouro` AS `logradouro`,
	        `c`.`bairro` AS `bairro`,
	        `c`.`cidade` AS `cidade`,
	        `c`.`uf` AS `uf`,
	        `c`.`cep` AS `cep`,
	        `c`.`telefone` AS `telefone`,
	        `c`.`fax` AS `fax`,
	        `c`.`email` AS `email`,
	        `c`.`celular` AS `celular`,
	        `c`.`homepage` AS `homepage`,
	        `c`.`titular` AS `titular`,
	        `c`.`contato` AS `contato`,
	        `c`.`cgc_cpf` AS `cgc_cpf`,
	        `c`.`ehativo` AS `ehativo`,
	        `c`.`forense` AS `forense`,
	        `c`.`observacao` AS `observacao`,
	        `c`.`inscestadual` AS `inscestadual`,
	        `c`.`inscmunicipal` AS `inscmunicipal`,
	        `v`.`datavencimento` AS `datavencimento`,
	        `v`.`valorbruto` AS `valorbruto`,
	        `v`.`deducoes` AS `deducoes`,
	        `v`.`valorliquido` AS `valorliquido`,
	        `v`.`notafiscal` AS `notafiscal`
	    FROM
	        (`secnatal`.`vrparcelas` `v`
	        JOIN `secnatal`.`cliente` `c`)
	    WHERE
	        	(ISNULL(`v`.`datepagamento`)
	        	AND (`v`.`notafiscal` IS NOT NULL)
	            AND (`v`.`datavencimento` < CURDATE())
	            AND (`c`.`idcliente` = `v`.`idcliente`))'
        );

		$consulta->execute(); // executando em cima do id do contrato

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado

		$retorno  = [];
		foreach ($rs as $key => $value) {

			$index = -1;
			for($x = 0; $x < count($retorno); $x++)
				if($retorno[$x]['idCliente'] == (int)$value['idcliente']) {
					$index = $x;
					break;
				}

			if($index === -1) { //Esse cliente ainda não estava na lista
				array_push($retorno, [
							        'idCliente' => (int)$value['idcliente'],
							        'nomeCliente' => utf8_encode($value['nomecliente']),
							        'logradouro' => utf8_encode($value['logradouro']),
							        'bairro' => utf8_encode($value['bairro']),
							        'cidade' => utf8_encode($value['cidade']),
							        'uf' => utf8_encode($value['uf']),
							        'cep' => utf8_encode($value['cep']),
							        'telefone' => utf8_encode($value['telefone']),
							        'fax' => utf8_encode($value['fax']),
							        'email' => utf8_encode($value['email']),
							        'celular' => utf8_encode($value['celular']),
							        'homepage' => utf8_encode($value['homepage']),
							        'titular' => utf8_encode($value['titular']),
							        'contato' => utf8_encode($value['contato']),
							        'cgcCpf' => utf8_encode($value['cgc_cpf']),
							        'ehAtivo' => (bool)$value['ehativo'],
							        'forense' => (bool)$value['forense'],
							        'observacao' => utf8_encode($value['observacao']),
							        'inscEstadual' => utf8_encode($value['inscestadual']),
							        'inscMunicipal' => utf8_encode($value['inscmunicipal']),
							        'somaBruto' => (float)$value['valorbruto'], // Inicializando a soma do valor bruto de todos os itens
							        'somaLiquido' => (float)$value['valorliquido'], // Inicializando a soma do valor liquido de todos os itens
							        'contratos' => [[ // inicializando a lista de contratos
							        					'idContrato' => (int)$value['idcontrato'],
							        					'itens' => [[ // Inicializando a lista de itens deste contrato
							        									'dataVencimento' => utf8_encode($value['datavencimento']),
																        'valorBruto' => (float)$value['valorbruto'],
																        'deducoes' => (float)$value['deducoes'],
																        'valorLiquido' => (float)$value['valorliquido'],
																        'notaFiscal' => utf8_encode($value['notafiscal'])
							        					]],
							        					'valorSoma' => (float)$value['valorbruto']
							        ]]
		    					]
		    	);
			}
			else { //o cliente já estava na lista

				$contrato_index = -1;
				for($x = 0; $x < count($retorno[$index]['contratos']); $x++)
					if($retorno[$index]['contratos'][$x]['idContrato'] == (int)$value['idcontrato']) {
						$contrato_index = $x;
						break;
					}

				if($contrato_index === -1) { // Este contrato não estava na lista de contratos ainda
					array_push($retorno[$index]['contratos'],
						[ // Inserindo novo contrato a lista á existente
							'idContrato' => (int)$value['idcontrato'],
							'itens' => [[ // Inicializando a lista de itens deste contrato
							        		'dataVencimento' => utf8_encode($value['datavencimento']),
											'valorBruto' => (float)$value['valorbruto'],
											'deducoes' => (float)$value['deducoes'],
											'valorLiquido' => (float)$value['valorliquido'],
											'notaFiscal' => utf8_encode($value['notafiscal'])
							        	]],
							'valorSoma' => (float)$value['valorbruto']
						]
					);
				}
				else  { // O contrato já estava na lista de contratos
					array_push($retorno[$index]['contratos'][$contrato_index]['itens'], 
							[ // Inserindo um novo iten a lista já existente de itens deste contrato
								'dataVencimento' => utf8_encode($value['datavencimento']),
								'valorBruto' => (float)$value['valorbruto'],
								'deducoes' => (float)$value['deducoes'],
								'valorLiquido' => (float)$value['valorliquido'],
								'notaFiscal' => utf8_encode($value['notafiscal'])
							]
					);
					$retorno[$index]['contratos'][$contrato_index]['valorSoma'] += (float)$value['valorbruto'];
				}
				$retorno[$index]['somaBruto'] += (float)$value['valorbruto'];
				$retorno[$index]['somaLiquido'] += (float)$value['valorliquido'];
			} // <fim da condição "O cliente já estava na lista">
		} // <Fim do for que percorre a lista completa de itens>

		return $retorno;
	}

	/*
		Essa função deleta todos os produtos associados ao contrato cujo id corresponde a $id_contrato
		Em seguida substitui pela lista de produtos correspondente aos ids(int) passados em $ids_produtos
	*/
	public function setProdutosDoContrato(int $id_contrato, array $ids_produto): bool {
		//Deletando anteriores
		$deletado = $this->con->prepare('DELETE FROM contratoproduto WHERE idcontrato=?;')->execute([$id_contrato]);
		if(!$deletado) { // Se por algum motivo não foi posível deletar
				$e = new PersistenciaException("Os produtos desse contrato não puderam ser delatados", 0, $e);
				$e->setEstado(  'Contrato: '.$id_contrato.
								', PDOStatement::errorInfo: '.json_encode( $requisicao->errorInfo() )
							);
				throw $e;
			}

		// Registrando as novas
		$requisicao = $this->con->prepare('INSERT INTO contratoproduto (idcontrato, idproduto) VALUES (:contrato, :produto);'); // preparando

		foreach ($ids_produto as $key => $value) {
			$sucesso = $requisicao->execute(['contrato' => $id_contrato, 'produto' => $value]); // executando em cima do id do contrato e do produto
			if(!$sucesso) { // Se por algum motivo não foi posível inserir
				$e = new PersistenciaException("O produto não pôde ser associado ao contrato", 0, $e);
				$e->setEstado('Contrato: '.$id_contrato.
								', Produto: '.$value.
								', listaProdutos: '.json_encode($ids_produto).
								', PDOStatement::errorInfo: '.json_encode($requisicao->errorInfo()));
				throw $e;
			}
		}

		return true;
	}

	public function getContratosDoCliente(int $id): array {
		$consulta = $this->con->prepare('SELECT * FROM vcontrato WHERE idcliente = :id;'); // preparando
		$sucesso = $consulta->execute(array('id' => $id)); // executando em cima do id do contrato

		if(!$sucesso) {
			$e = new PersistenciaException("Não foi possível consultar os contratos deste cliente");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado

		$retorno = [];
		foreach ($rs as $key => $value) {
			$retorno[$key] = new Contrato(
							(int) $value['idcontrato'],
							utf8_encode($value['dataemissao']),
							utf8_encode($value['datainicial']),
							utf8_encode($value['datafinal']),
							utf8_encode($value['datacancelamento']),
							(int) $value['idtipoacesso'],
							(int) $value['idmensageiro'],
							(int) $value['idFuncionario'],
							utf8_encode($value['observacao']),
							(bool) $value['ativo'],
							(float) $value['valor_total'],
							(float) $value['valor_utilizado'],
							(float) $value['saldo_disponivel'],
							(int) $value['id_categoria'],
							$this->listarProdutosDoContrato((int)$value['idcontrato']),
							(int) $value['idcliente'],
							utf8_encode($value['nomecliente']),
							$this);
		}

		return $retorno;
	}

	public function getCategoriaProduto(int $id): CategoriaProduto {
		$consulta = $this->con->prepare('SELECT * FROM produtocategoria WHERE idprodutocategoria = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do CategoriaProduto

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado

		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhuma categoria de produto encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}

		$rs = $rs[0];

		return new CategoriaProduto(
							(int) $rs['idprodutocategoria'],
							utf8_encode($rs['nomeCategoria']),
							$this);
	}

	public function getMensageiro(int $id): Mensageiro {
		$consulta = $this->con->prepare('SELECT * FROM mensageiro WHERE idmensageiro = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do Mensageiro

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado

		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum mensageiro encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}

		$rs = $rs[0];

		return new Mensageiro(
							(int) $rs['idmensageiro'],
							utf8_encode($rs['nomeMensageiro']),
							$this);
	}

	public function getCliente(int $id): Cliente {
		$consulta = $this->con->prepare('SELECT * FROM cliente WHERE idcliente = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do contrato

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum cliente encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0];

		return new Cliente((int) $rs['idcliente'],
							utf8_encode($rs['nomecliente']),
							utf8_encode($rs['logradouro']),
							utf8_encode($rs['bairro']),
							utf8_encode($rs['cidade']),
							utf8_encode($rs['uf']),
							utf8_encode($rs['cep']),
							utf8_encode($rs['telefone']),
							utf8_encode($rs['fax']),
							utf8_encode($rs['email']),
							utf8_encode($rs['celular']),
							utf8_encode($rs['homepage']),
							utf8_encode($rs['titular']),
							utf8_encode($rs['contato']),
							utf8_encode($rs['cgc_cpf']),
							(bool) $rs['ehativo'],
							(bool) $rs['forense'],
							utf8_encode($rs['observacao']),
							utf8_encode($rs['inscestadual']),
							utf8_encode($rs['inscmunicipal']),
							utf8_encode($rs['complemento']),
							utf8_encode($rs['titular_cpf']),
							$this);
	}

	public function getFuncionario(int $id): Funcionario {
		$consulta = $this->con->prepare('SELECT * FROM funcionario WHERE idfuncionario = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do Funcionario

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum funcionário encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0];

		return new Funcionario((int) $rs['idfuncionario'],
							utf8_encode($rs['nomefuncionario']),
							$this);
	}

	public function getProduto(int $id): Produto {
		$consulta = $this->con->prepare('SELECT * FROM produto WHERE idproduto = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do Produto

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum produto encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0];

		return new Produto((int) $rs['idproduto'],
							(int) $rs['idcategoria'],
							utf8_encode($rs['nomeProduto']),
							$this);
	}

	public function getTipoAcesso(int $id): TipoAcesso {
		$consulta = $this->con->prepare('SELECT * FROM tipoacesso WHERE idtipoacesso = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do TipoAcesso

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum tipo de acesso encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0];

		return new TipoAcesso((int) $rs['idtipoacesso'],
							utf8_encode($rs['nometipoacesso']),
							$this);
	}

	public function getItemContrato(int $id): ItemContrato {
		$consulta = $this->con->prepare('SELECT * FROM parcelacontrato WHERE idparcelacontrato = :id;'); // preparando
		$consulta->execute(array('id' => $id)); // executando em cima do id do contrato

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum item de contrato encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}
		$rs = $rs[0];

		return new ItemContrato((int) $rs['idparcelaContrato'],
								(int) $rs['idcontrato'],
								$rs['idproduto'],
								(float) $rs['valorbruto'],
								utf8_encode($rs['datavencimento']),
								utf8_encode($rs['datepagamento']),
								utf8_encode($rs['dataprestacao']),
								(float) $rs['deducoes'],
								$rs['valorapagar'],
								utf8_encode($rs['notafiscal']),
								utf8_encode($rs['notafiscalapagar']),
								utf8_encode($rs['observacao']),
								utf8_encode($rs['medidas']),
								(bool) $rs['foipaga'],
								(float) $rs['numero'],
								$this);
	}

	public function getItensUltimos12Meses(): Array {
		$consulta = $this->con->prepare('SELECT * FROM parcelacontrato WHERE 
	        	(`datepagamento` IS NOT NULL
	            AND (`datepagamento` >= DATE("'.(date('Y')-1).'-'.(date('m')).'-01"))
	            AND (`datepagamento` < DATE("'.date('Y').'-'.(date('m')).'-01"))
	            )'); // preparando
		$consulta->execute(); // executando em cima do id do contrato

		$rs = $consulta->fetchAll(); //extraindo o vetor corespondente ao resultado
		if(count($rs) <= 0) {
			$e = new PersistenciaException("Nenhum item de contrato encontrato");
			$e->setEstado( 'ID: '.$id.', PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() ) );
			throw $e;
		}

		$retorno = [];
		foreach ($rs as $chave => $valor) {
			array_push($retorno, new ItemContrato((int) $valor['idparcelaContrato'],
								(int) $valor['idcontrato'],
								$valor['idproduto'],
								(float) $valor['valorbruto'],
								utf8_encode($valor['datavencimento']),
								utf8_encode($valor['datepagamento']),
								utf8_encode($valor['dataprestacao']),
								(float) $valor['deducoes'],
								$valor['valorapagar'],
								utf8_encode($valor['notafiscal']),
								utf8_encode($valor['notafiscalapagar']),
								utf8_encode($valor['observacao']),
								utf8_encode($valor['medidas']),
								(bool) $valor['foipaga'],
								(float) $valor['numero'],
								$this)
					);
		}

		return $retorno;
	}

	/*
		Esta função executa uma atualização de um valor desejado em uma determinada coluna de uma determinada tabela
		$tabela Deve conter a tabela a ser atualizada
		$chave É a coluna da tabela que será atualizada
		$valor É o novo valor que ocupará a célula daquela coluna
		$condicao_a É a identificação da linha que será atualizada
		$condicao_b É o valor contido na linha identificada por $condicao_a
	*/
	public function atualizar($tabela, $chave, $valor, $condicao_a, $condicao_b): bool {
		$requisitar; //PDOStatement

		$requisitar = $this->con->prepare("UPDATE $tabela SET $chave=:valor WHERE $condicao_a=:b;");

		if(gettype($valor) === "string")
			$valor = $this::latin1_encode($valor);

		$sucesso = $requisitar->execute(array(
					'valor' => $valor,
					'b' => $condicao_b
				));

		if(!$sucesso) {
			$e = new PersistenciaException("Não foi posível executar o update");
			$e->setEstado(
				'Tabela: '.$tabela.', '.
				'Chave: '.$chave.', '.
				'Valor: '.$valor.', '.
				'Condicao_a: '.$condicao_a.', '.
				'Condicao_b: '.$condicao_b.', '.
				'PDOStatement::errorInfo: '.json_encode( $requisitar->errorInfo() )
			);
			throw $e;
		}

		return true;
	}

	public function inserir(ModeloDados $objeto): bool {
		$vetor = explode("\\", get_class($objeto));
		$classe = end($vetor);
		$tabela = '';
		switch ($classe) {
			case 'Contrato':
				$tabela = 'contrato';
				break;
			case 'Produto':
				$tabela = 'produto';
				break;
			case 'TipoAcesso':
				$tabela = 'tipoacesso';
				break;
			case 'CategoriaProduto':
				$tabela = 'produtocategoria';
				break;
			case 'Cliente':
				$tabela = 'cliente';
				break;
			case 'Funcionario':
				$tabela = 'funcionario';
				break;
			case 'ItemContrato':
				$tabela = 'parcelacontrato';
				break;
			case 'Mensageiro':
				$tabela = 'mensageiro';
				break;

			default:
				throw new PersistenciaException("Error: A classe ".get_class($objeto)." não é uma classe de modelo de dados.\n Na função persistencia->insert");
				break;
		}

		$r = $this->_inserir($tabela, $objeto->toColunasBanco());
		if($r) {
			if($tabela === 'contrato') { // caso especial
				$objeto->setIdContrato($this->con->lastInsertId()); // registrando o id deste contrato
			}
			if($tabela === 'parcelacontrato') { // caso especial
				$objeto->setIdItem($this->con->lastInsertId()); // registrando o id deste item
			}
		}
		return $r;
	}

	private function _inserir($tabela, $vetor): bool {
		// Preparando as chaves e valores
		$elementos = $this::serializarParaInserir($vetor);

		$registro = $this->con->prepare("INSERT INTO $tabela \n ({$elementos['chaves']}) \n VALUES ({$elementos['interrogacoes']});"); // preparando

		//executando
		$sucesso = $registro->execute($elementos['elementos']); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não foi posível inserir o novo elemento");
			$e->setEstado(
				'Tabela: '.$tabela.', '.
				'Vetor: '.json_encode($vetor).', '.
				'Elementos: '.json_encode($elementos).', '.
				'PDOStatement::errorInfo: '.json_encode( $registro->errorInfo() )
			);
			throw $e;
		}

		return $sucesso;
	}

	static private function serializarParaInserir($vetor): array {
		$elementos = [];
		$interrogacoes = '';
		$chaves = '';

		foreach ($vetor as $chave => $valor) { //preenchendo e separando por virgulas
			if(is_null($valor))	continue; // caso seja nulo não setar esse valor

			$chaves .= $chave.',';
			$interrogacoes .= "?,";
			if(gettype($valor) === 'string') // Deve formatar com a codificação correta
				array_push($elementos, Persistencia::latin1_encode($valor));
			elseif(gettype($valor) === 'boolean') // Se for booleano deve ser tradizido pata INT. false=0; true=1
				array_push($elementos, (int)$valor);
			elseif(is_null($valor)) // Se for nulo deve ser traduzido para uma string "NULL"
				array_push($elementos, 'NULL');
			else
				array_push($elementos, $valor);
		}

		//removendo a ultima virgula
		$interrogacoes = substr($interrogacoes, 0, -1);
		$chaves = substr($chaves, 0, -1);

		return [
			'interrogacoes' => $interrogacoes,
			'chaves' => $chaves,
			'elementos' => $elementos
		];
	}

	public function getNomeClientes(): array {
		$consulta = $this->con->prepare('SELECT nomecliente, idcliente FROM cliente;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os nomes dos clientes");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nomecliente']), 'id' => (int)$value['idcliente']]);
		}

		return $retorno;
	}

	public function getFuncionarios(): array {
		$consulta = $this->con->prepare('SELECT * FROM funcionario;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os funcionário do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nomefuncionario']), 'id' => (int)$value['idfuncionario']]);
		}

		return $retorno;
	}

	public function getMensageiros(): array {
		$consulta = $this->con->prepare('SELECT * FROM mensageiro;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os mensageiros do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nomeMensageiro']), 'id' => (int)$value['idmensageiro']]);
		}

		return $retorno;
	}

	public function getTiposAcesso(): array {
		$consulta = $this->con->prepare('SELECT * FROM tipoacesso;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os tipos de acesso do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nometipoacesso']), 'id' => (int)$value['idtipoacesso']]);
		}

		return $retorno;
	}

	public function getUFs(): array {
		$consulta = $this->con->prepare('SELECT * FROM estado;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter as UFs do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nome']), 'UF' => $value['uf']]);
		}

		return $retorno;
	}

	public function getProdutos(): array {
		$consulta = $this->con->prepare('SELECT * FROM produto;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os produtos do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nomeProduto']), 'id' => (int)$value['idproduto'], 'idCategoria' => (int)$value['idcategoria']]);
		}

		return $retorno;
	}

	public function getCategoriasProduto(): array {
		$consulta = $this->con->prepare('SELECT * FROM produtocategoria;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter as categorias de produtos do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$retorno = [];
		foreach ($rs as $key => $value) {
			array_push($retorno, ['nome' => utf8_encode($value['nomeCategoria']), 'id' => (int)$value['idprodutocategoria']]);
		}

		return $retorno;
	}

	public function getItensdeContrato($id): array {
		$consulta = $this->con->prepare('SELECT * FROM parcelacontrato WHERE idcontrato=:id;'); // preparando

		$sucesso = $consulta->execute(['id'=>$id]); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os itens do contrato");
			$e->setEstado(
				'ID: '.$id.', '.
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$vetor_de_itens = [];
		foreach ($rs as $chave => $valor) {
			array_push($vetor_de_itens, 
				new ItemContrato((int) $valor['idparcelaContrato'],
								(int) $valor['idcontrato'],
								$valor['idproduto'],
								(float) $valor['valorbruto'],
								utf8_encode($valor['datavencimento']),
								utf8_encode($valor['datepagamento']),
								utf8_encode($valor['dataprestacao']),
								(float) $valor['deducoes'],
								$valor['valorapagar'],
								utf8_encode($valor['notafiscal']),
								utf8_encode($valor['notafiscalapagar']),
								utf8_encode($valor['observacao']),
								utf8_encode($valor['medidas']),
								(bool) $valor['foipaga'],
								(float) $valor['numero'],
								$this)
			);
		}

		return $vetor_de_itens;
	}

	public function getUFNome(int $id): string {
		$consulta = $this->con->prepare('SELECT nome FROM estado WHERE uf=:id;'); // preparando

		$sucesso = $consulta->execute(['id' => $id]); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os nomes das UFs do banco de dados");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll()[0]; //extraindo a matriz de resultados

		return utf8_encode($rs['nome']);
	}


	public function getRelatorio(int $relatorio, $opcional = null) {
		switch ($relatorio) {
			case (Relatorio::EMAIL):
				return $this->listarEmails();
			break;
			case (Relatorio::INADIMPLENTES):
				return $this->listarDadosClientesEmAtraso();
			break;
			case (Relatorio::itensPagosNosUltimos12Meses):
				return $this->getItensUltimos12Meses();
			break;
			case (Relatorio::NOTAFISCAL):
				return $this->getItensDaNotaFiscal($opcional);
			break;

			default:
				$e = new PersistenciaException("Código de relatório desconhecido");
				$e->setEstado('Código do relatório: '.$relatorio);
				throw $e;
			break;
		}
	}

	public function getItensDaNotaFiscal(int $numero): array {
		$consulta = $this->con->prepare('SELECT * FROM parcelacontrato WHERE notafiscal=:n;'); // preparando

		$sucesso = $consulta->execute(['n'=>$numero]); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os itens da nota fiscal");
			$e->setEstado(
				'Numero: '.$numero.', '.
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$vetor_de_itens = [];
		foreach ($rs as $chave => $valor) {
			array_push($vetor_de_itens, 
				new ItemContrato((int) $valor['idparcelaContrato'],
								(int) $valor['idcontrato'],
								$valor['idproduto'],
								(float) $valor['valorbruto'],
								utf8_encode($valor['datavencimento']),
								utf8_encode($valor['datepagamento']),
								utf8_encode($valor['dataprestacao']),
								(float) $valor['deducoes'],
								$valor['valorapagar'],
								utf8_encode($valor['notafiscal']),
								utf8_encode($valor['notafiscalapagar']),
								utf8_encode($valor['observacao']),
								utf8_encode($valor['medidas']),
								(bool) $valor['foipaga'],
								(float) $valor['numero'],
								$this)
			);
		}

		return $vetor_de_itens;
	}

	public function listarEmails(): array {
		$consulta = $this->con->prepare('SELECT nomecliente, email FROM cliente;'); // preparando

		$sucesso = $consulta->execute(); // executando

		if(!$sucesso) {
			$e = new PersistenciaException("Não obter os E-Mails da tabela clientes do banco");
			$e->setEstado(
				'PDOStatement::errorInfo: '.json_encode( $consulta->errorInfo() )
			);
			throw $e;
		}

		$rs = $consulta->fetchAll(); //extraindo a matriz de resultados

		$rs = array_map(function($elemento) { // formatando
			return ['nome'=>utf8_encode($elemento['nomecliente']), 'email'=>utf8_encode($elemento['email'])];
		}, $rs);

		$retorno = [];
		foreach ($rs as $key => $value) { // removendo elmais inválidos
			if(strlen($value['email']) > 3) array_push($retorno, $value);
		}

		return $retorno;
	}

	public static function latin1_encode($str) {
		if (mb_detect_encoding($str, 'UTF-8', true) !== false) {
		    return mb_convert_encoding($str, 'ISO-8859-1');
		}
		return $str;
	}
}
?>