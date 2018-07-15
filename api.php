<?php
require_once('vendor/autoload.php');
require_once('../config.php');

header("Content-type: application/json; charset=utf-8");

if(Config::REQUISITAR_LOGIN) // Se for nescesário estar logado para usar o sistema
	testarLogin(); //Impedindo acesso de usuários não logados

if(Config::CORS) //Checando se deve ou não habilitar Cross Origin
	header("Access-Control-Allow-Origin: *");

if(!Config::EXIBIR_ERROS) {// Impedindo ou não a exibição de erros
	error_reporting(0);
	ini_set('display_errors', 0);
}

use Sistema\Sistema;

try {
	$sistema = new Sistema;
} catch(Exception $e) {
	echo '{"status": "falha", "erro": "'.$e->getMessage().'"}';
	exit(0); // A API não pode funcionar sem o sistema
}

$request = parsear(file_get_contents('php://input')); // Recebendo e parceando o corpo da requisição
if($request === false) // Checando se a requisição é válida
	exit(0);


switch ($request['comando']) { // Delegando o comando solicitado a função correspondente
	case 'buscar':
		buscar($request['parametros']);
		break;

	case 'listar':
		listar($request['alvo']);
		break;

	case 'atualizar':
		atualizar($request);
		break;

	case 'deletar':
		deletar($request);
		break;

	case 'criar':
		criar($request);
		break;

	case 'relatorio':
		relatorio($request);
		break;

	default:
		echo '{"erro": "Comando ('.$request['comando'].') não reconhecido"}';
		break;
}


/**********************************************************************
				ZONA DE FUNÇÕES PARA TRATAR A REQUISIÇÃO
***********************************************************************/

function listar($alvo) { // Listar alguma informação do banco
	global $sistema;
	switch ($alvo) {
		case 'clientes':
			echo json_encode($sistema->listarNomesCliente());
			break;
		case 'funcionarios':
			echo json_encode($sistema->listarFuncionarios());
			break;
		case 'tipoAcesso':
			echo json_encode($sistema->listarTiposAcesso());
			break;
		case 'produtos':
			echo json_encode($sistema->listarProdutos());
			break;
		case 'mensageiros':
			echo json_encode($sistema->listarMensageiros());
			break;
		case 'categoriaProduto':
			echo json_encode($sistema->listarCategoriasProduto());
			break;
		case 'UF':
			echo json_encode($sistema->listarUFs());
			break;

		default:
			echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando listar"}';
			break;
	}
}


function buscar($dados) {
	if($dados['alvo'] === 'contrato') { // se estiver buscando um contrato
		echo buscaContrato($dados['parametros']);
	}
	elseif($dados['alvo'] === 'item') { // se estiver buscando os items de um determinado contrato
		echo buscaItem($dados['idContrato']);
	}
	elseif($dados['alvo'] === 'cliente') { // se estiver buscando os items de um determinado contrato
		echo buscaCliente($dados['nome_cliente']);
	}
	else {
		echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando buscar"}';
	}
}

function buscaContrato($dados) {
	global $sistema;

	if(isset($dados['nome_cliente'])) // Se a busca for pelo nome do cliente
		$lista = $sistema->buscarNomeCliente($dados['nome_cliente']); // Recebendo uma lista de nomes parecidos com o informado
	else // Se a busca for pelo ID
		$lista = [['id'=>$dados['id_cliente']]];

	if(count($lista) === 0) // caso não existam clientes com esse nome
		return '{"erro": 404, "status": "falha"}';
	if(count($lista) === 1) { // caso só existe um cliente com esse nome
		try {
			$lista_contratos = $sistema->listarContratosCliente($lista[0]['id']); // listando contratos deste cliente
		}
		catch (Exception $e) { // Não foi possível encontrar nenhum contrato por uma falha interna
			return '{
			"resultado": "conclusivo",
			"dados": [],
			"status": "falha"
			}'; // retornando uma lista vazia de contratos
		}

		$lista_contratos_filtrada = filtrar($lista_contratos, $dados); // filtrando de acordo com os parâmetros passados
		if(count($lista_contratos_filtrada) <= 0) // não sobrou contratos após a filtragem
			return '{"erro": 404, "status": "falha"}';
		// retornando a lista filtrada
		return '{
			"resultado": "conclusivo",
			"status": "sucesso",
			"dados": '.json_encode($lista_contratos_filtrada).'
		}';
	}
	else { //caso existam mais de um cliente com esse nome
		$resposta = [
			'resultado' => 'inconclusivo',
			"status"=> "sucesso",
			'dados' => $lista
		];
		return json_encode($resposta);
	}
}

function buscaItem($idContrato) {
	global $sistema;

	$lista = $sistema->listarItensContrato((int)$idContrato);
	
	return json_encode($lista);
}

function buscaCliente($nome) {
	global $sistema;	
	if($nome == null || $nome == '')
		return '{"status": "falha", "mensagem": "Nome do cliente não informado"}';

	$lista = $sistema->buscarCliente($nome);
	return '{"status": "sucesso", "dados": '.json_encode($lista).'}';
}

function atualizar($request) {
	global $sistema;

	if($request['alvo'] === 'item') {
		$r = $sistema->atualizarItem((int)$request['id'], $request['parametros']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	else if($request['alvo'] === 'contrato') {
		$r = $sistema->atualizarContrato((int)$request['parametros']['id'], $request['parametros']['parametros']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	else if($request['alvo'] === 'cliente') {
		$r = $sistema->atualizarCliente((int)$request['parametros']['id'], $request['parametros']['parametros']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	else{echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando atualizar"}';}
}

function deletar($request) {
	global $sistema;

	if($request['alvo'] === 'contrato') {
		$r = $sistema->deletarContrato((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'item') {
		$r = $sistema->deletarItem((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'funcionario') {
		$r = $sistema->deletarFuncionario((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "O funcionario posívelmente está associado a um contrato"}';
	}
	elseif($request['alvo'] === 'mensageiro') {
		$r = $sistema->deletarMensageiro((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "O mensageiro posívelmente está associado a um contrato"}';
	}
	elseif($request['alvo'] === 'tipoAcesso') {
		$r = $sistema->deletarTipoAcesso((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "O tipo de acesso posívelmente está associado a um contrato"}';
	}
	elseif($request['alvo'] === 'categoriaProduto') {
		$r = $sistema->deletarCategoriaProduto((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "A categoria posívelmente está associado a um contrato e/ou possue subprodutos"}';
	}
	elseif($request['alvo'] === 'produto') {
		$r = $sistema->deletarProduto((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "O produto possivelmente está associado a um contrato"}';
	}
	elseif($request['alvo'] === 'cliente') {
		$r = $sistema->deletarCliente((int) $request['id']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha", "mensagem": "O cliente possivelmente está associado a um contrato"}';
	}
	elseif($request['alvo'] === 'logErros') {
		Sistema::limparLogErros();
		echo '{"status": "sucesso"}';
	}
	else {echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando deletar"}';}
}

function criar($request) {
	global $sistema;

	if($request['alvo'] === 'contrato') {
		if(!checarParametrosContrato($request['parametros'])) {
			echo '{"status": "falha", "mensagem": "Parâmetros mal formados"}';
			return false;
		}
		$r = $sistema->criarContrato(
				(bool) $request['parametros']['ativo'],
				$request['parametros']['dataCancelamento'],
				(string) $request['parametros']['dataEmissao'],
				(string) $request['parametros']['dataFinal'],
				(string) $request['parametros']['dataInicial'],
				(int) $request['parametros']['idCategoria'],
				(int) $request['parametros']['idCliente'],
				(int) $request['parametros']['idFuncionario'], 
				(int) $request['parametros']['idMensageiro'],
				(int) $request['parametros']['idTipoAcesso'],
				$request['parametros']['idsProdutos'],
				(string) $request['parametros']['observacao'],
				(float) $request['parametros']['valorTotal']
			);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'cliente') {
		$r = $sistema->criarCliente(
			(string) $request['parametros']['nomeCliente'],
			(string) $request['parametros']['logradouro'],
			(string) $request['parametros']['bairro'],
			(string) $request['parametros']['cidade'],
			(string) $request['parametros']['UF'],
			(string) $request['parametros']['cep'],
			(string) $request['parametros']['telefone'],
			(string) $request['parametros']['fax'],
			(string) $request['parametros']['email'],
			(string) $request['parametros']['celular'],
			(string) $request['parametros']['homepage'],
			(string) $request['parametros']['titular'],
			(string) $request['parametros']['contato'],
			(string) $request['parametros']['cgcCpf'],
			(bool) $request['parametros']['ativo'],
			true,
			(string) $request['parametros']['observacao'],
			(string) $request['parametros']['inscEstadual'],
			(string) $request['parametros']['inscMunicipal'],
			(string) $request['parametros']['complemento'],
			(string) $request['parametros']['titularCpf']
			);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'item') {
		if(!checarParametrosItem($request['parametros'])) {
			echo '{"status": "falha", "mensagem": "Parâmetro invalido passado"}';
			return false;
		}
		$r = $sistema->criarItem(
				(int)	$request['parametros']['idContrato'],
						$request['parametros']['idProduto'],
				(float) $request['parametros']['valorBruto'],
				(string)$request['parametros']['dataVencimento'],
						$request['parametros']['dataPagamento'],
						$request['parametros']['dataPrestacao'],
				(float) $request['parametros']['deducoes'],
						$request['parametros']['valorAPagar'],
				(string)$request['parametros']['notaFiscal'],
						$request['parametros']['notaFiscalAPagar'],
				(string)$request['parametros']['observacao'],
						$request['parametros']['medidas'],
						false, 
				(int) 	$request['parametros']['numero']
			);
		if($r !== false)
			echo '{"status": "sucesso", "idItem": '.$r.'}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'funcionario') {
		$r = $sistema->criarFuncionario((string) $request['parametros']['nome']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'mensageiro') {
		$r = $sistema->criarMensageiro((string) $request['parametros']['nome']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'tipoAcesso') {
		$r = $sistema->criarTipoAcesso((string) $request['parametros']['nome']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'categoriaProduto') {
		$r = $sistema->criarCategoriaProduto((string) $request['parametros']['nome']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	elseif($request['alvo'] === 'produto') {
		$r = $sistema->criarProduto((string) $request['parametros']['nome'], (int) $request['parametros']['idCategoria']);
		if($r)
			echo '{"status": "sucesso"}';
		else
			echo '{"status": "falha"}';
	}
	else{echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando criar"}';}
}

function relatorio($request) {
	global $sistema;

	switch ($request['alvo']) {
		case 'Email':
			echo json_encode($sistema->listarEmails());
			break;
		case 'Clientes':
			echo json_encode($sistema->listarClientes());
			break;

		case 'ClientesAtivos':
			echo json_encode($sistema->listarClientesAtivos());
			break;

		case 'ClientesInativos':
			echo json_encode($sistema->listarClientesInativos());
			break;

		case 'ClientesInadimplentes':
			echo json_encode($sistema->listarClientesEmAtraso());
			break;

		case 'servicosFinalizadosUltimos12Meses':
			echo json_encode($sistema->listarServicosFinalizadosUltimos12Meses());
			break;

		case 'NotaFiscal':
			echo json_encode($sistema->listarItensNotaFiscal((int)$request['numero']));
			break;

		default:
			echo '{"status": "falha", "mensagem": "Alvo desconhecido para o comando relatorio"}';
			break;
	}
		
}

/**********************************************************************************
				SUBFUNÇÕES PARA SANITIZAR A ENTRADA DA API
**********************************************************************************/

function checarParametrosContrato($p): bool {
	if(!isset($p['ativo'])) return false;
	if(!isset($p['dataEmissao'])) return false;
	if(!isset($p['dataFinal'])) return false;
	if(!isset($p['dataInicial'])) return false;
	if(!isset($p['idCategoria'])) return false;
	if(!isset($p['idCliente'])) return false;
	if(!isset($p['idFuncionario'])) return false;
	if(!isset($p['idMensageiro'])) return false;
	if(!isset($p['idTipoAcesso'])) return false;
	if(!isset($p['idsProdutos'])) return false;
	if(!isset($p['observacao'])) return false;
	if(!isset($p['valorTotal'])) return false;
	return true;
}
function checarParametrosItem($p): bool {
	if(!isset($p['idContrato'])) return false;
	if(!array_key_exists('idProduto', $p)) return false;
	if(!isset($p['valorBruto'])) return false;
	if(!isset($p['dataVencimento'])) return false;
	if(!array_key_exists('dataPrestacao', $p)) return false;
	if(!array_key_exists('deducoes', $p)) return false;
	if(!array_key_exists('notaFiscalAPagar', $p)) return false;
	if(!array_key_exists('valorAPagar', $p)) return false;
	if(!isset($p['observacao'])) return false;
	if(!array_key_exists('medidas', $p)) return false;
	if(!isset($p['numero'])) return false;
	return true;
}

/**********************************************************************************
				SUBFUNÇÕES PARA APLICAR FILTRO NA LISTA DE CONTRATOS
**********************************************************************************/

function filtrar($contratos, $filtro): Array {
	$contratos_filtrados = [];

	foreach ($contratos as $key => $value) {
		if(checar_filtro($value, $filtro))
			array_push($contratos_filtrados, $value);
	}

	return $contratos_filtrados;
}

function checar_filtro($contrato, $filtro): bool {
	if($filtro['tipo_contrato'] === 'ativo' && $contrato['ativo'] === false) return false;
	if($filtro['tipo_contrato'] === 'cancelado' && $contrato['ativo'] === true) return false;

	if($filtro['vencimento_contrato_fim'] !== null) { // o usuário solicitou comparar a data final do contrato
		$data_tratada = implode('-', array_reverse(explode('-', $filtro['vencimento_contrato_fim'])));
		$resultado = comparar_datas($contrato['dataFinal'], $data_tratada);
		if($resultado > 0) // a data de vencimento do contrato é maior que a data limite passada pelo usuário
			return false;
	}

	if($filtro['vencimento_contrato_inicio'] !== null) { // o usuário solicitou comparar a data de inicio do contrato
		$data_tratada = implode('-', array_reverse(explode('-', $filtro['vencimento_contrato_inicio'])));
		$resultado = comparar_datas($contrato['dataFinal'], $data_tratada);
		if($resultado < 0) // a data de vencimento do contrato é menor que o limite mínimo solicitado
			return false;
	}

	return true;
}

/*
	Esta função calcula se uma data é anterior, igual ou posterior a outra
	$a e $b devem ser datas no formato YYY-MM-DD
	Essa função retorna 3 valores posíveis:
	(int) -1: caso $a seja uma data anterior a $b
	(int) 0: caso $a seja uma data igual a $b
	(int) 1: caso $a seja uma data posterior a $b
*/
function comparar_datas(string $a, string $b): int {
	$a = dataString2dataArray($a);
	$b = dataString2dataArray($b);

	if($a['Y'] < $b['Y']) return -1;
	if($a['Y'] > $b['Y']) return 1;

	if($a['M'] < $b['M']) return -1;
	if($a['M'] > $b['M']) return 1;

	if($a['D'] < $b['D']) return -1;
	if($a['D'] > $b['D']) return 1;

	return 0;
}

/*
	Essa função converte uma data no formato YYYY-MM-DD para um array:
	[
		'Y' = (int) ano
		'M' = (int) mês
		'D' = (int) dia
	]
*/
function dataString2dataArray(string $data_string): Array {
	$retorno = explode('-', $data_string);
	return [
		'Y' => (int)$retorno[0],
		'M' => (int)$retorno[1],
		'D' => (int)$retorno[2]
	];
}
/**********************************************************************************
					FIM DO GRUPO DE SUBFUNÇÕES DE FILTRAGEM
**********************************************************************************/

function parsear($dados) {
	try {
		$requisicao = json_decode($dados, true);
	} catch (Exception $e) {
		echo '{"erro": "Erro ao parsear o corpo da requisição", "status": "falha"}';
		return false;
	}
	if($requisicao === null) {
		echo '{"erro": "Erro ao parsear o corpo da requisição", "status": "falha"}';
		return false;
	}

	return $requisicao;
}

function testarLogin() {
	session_start();
	if($_SESSION['logado'] !== 'S') {
		echo '{"erro": " O usuário não está logado no sistema", "status": "falha"}';
		exit(0);
	}
}

?>