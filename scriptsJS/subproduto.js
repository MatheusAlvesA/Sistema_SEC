app.controller("SubProdutoCtrl", function($scope, requisitarAPI) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache de forma singleton
*/
$scope.listaProdutos = null;
$scope.listaSubProdutos = {};
$scope.funcaoInserirDisponivel = true;
$scope.listarProdutos = function() {
    var comando = {
      'comando': 'listar',
      'alvo': 'categoriaProduto'
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.listaProdutos = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );

};
$scope.listarSubProdutos = function() {
    var comando = {
      'comando': 'listar',
      'alvo': 'produtos'
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.listaSubProdutos = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
          $scope.listarProdutos();
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );

};
$scope.listarSubProdutos();

$scope.apagarSubProduto = function(id) {

  if(!confirm("Tem certeza que quer deletar esse Sub-Produto?")) return false;

  var comando = {
      'comando': 'deletar',
      'alvo': 'produto',
      'id': id
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
        	$scope.exibirErro('mensagemErroDesconhecido');
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // Provavelmente o mensageiro é responsável por um contrato e não pode ser deletado por isso
          $scope.exibirErro('mensagemErroConhecido');
          return false;
        }
        else { // Sucesso ao deletar o mensageiro
          $scope.listarSubProdutos();
          return true;
        }
      },
      function(dados) { // callback de falha
      	if(dados.status === -1)
      		$scope.exibirErro('mensagemErroConexao');
        else 
        	$scope.exibirErro('mensagemErroDesconhecido');
        return false;
      }
  );
};

$scope.exibirErro = function(elemento) {
	document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}


$scope.criarNovo = function(nome, idCategoria) {
	if(nome == '' || nome == null || nome == undefined) return false;

	$scope.funcaoInserirDisponivel = false;
  var comando = {
      'comando': 'criar',
      'alvo': 'produto',
      'parametros': {'nome': nome, 'idCategoria': idCategoria}
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
        	$scope.exibirErro('mensagemErroDesconhecido');
        	$scope.funcaoInserirDisponivel = true;
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') {
          $scope.exibirErro('mensagemErroDesconhecido');
          $scope.funcaoInserirDisponivel = true;
          return false;
        }
        else { // Sucesso ao deletar o mensageiro
          $scope.listarSubProdutos();
          $scope.nome = '';
          	document.getElementById("mensagemSucessoInserir").style.display = 'block';
    		    setTimeout(function(){document.getElementById("mensagemSucessoInserir").style.display = 'none';}, 5000);
    	    $scope.funcaoInserirDisponivel = true;
          return true;
        }
      },
      function(dados) { // callback de falha
      	if(dados.status === -1)
      		$scope.exibirErro('mensagemErroConexao');
        else 
        	$scope.exibirErro('mensagemErroDesconhecido');
        $scope.funcaoInserirDisponivel = true;
        return false;
      }
  );
};


$scope.getNomeProduto = function(id) {
  // Caso a lista de subprodutos ainda não tenha sido carregada
  if($scope.listaProdutos === undefined || $scope.listaProdutos === null) return '';

  let retorno = 'Desconhecido';
  for(let x = 0; x < $scope.listaProdutos.length; x++)
    if($scope.listaProdutos[x].id == id)
      retorno = $scope.listaProdutos[x].nome;

  return retorno;
};

 $scope.erroFatal = function(msg = '') {
  $('#telaErroFatal').css('display', 'block');
  $('#telaErroFatalMensagem').text(msg);
 };
}); // FIM DO CÓDIGO ANGULAR
