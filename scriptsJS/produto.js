app.controller("ProdutoCtrl", function($scope, requisitarAPI) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache de forma singleton
*/
$scope.listaProdutos = {};
$scope.listaSubProdutos = null;
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
          $scope.listarSubProdutos();
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
            return false;
          }
          $scope.listaSubProdutos = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
        },
        function (dados) {return false;} // callback, falha na conexão do servido
    );

};
$scope.listarProdutos();

$scope.apagarProduto = function(id) {

  if(!confirm("Tem certeza que quer deletar esse produto?")) return false;

  var comando = {
      'comando': 'deletar',
      'alvo': 'categoriaProduto',
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
          $scope.listarProdutos();
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


$scope.criarNovo = function(nome) {
	if(nome == '' || nome == null || nome == undefined) return false;

	$scope.funcaoInserirDisponivel = false;
  var comando = {
      'comando': 'criar',
      'alvo': 'categoriaProduto',
      'parametros': {'nome': nome}
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
          $scope.listarProdutos();
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


$scope.getSubprodutosOnString = function(id) {
  // Caso a lista de subprodutos ainda não tenha sido carregada
  if($scope.listaSubProdutos === undefined || $scope.listaSubProdutos === null) return false;

  return $scope.listaSubProdutos.reduce(function (acumulador, valor) {
    if(typeof acumulador !== 'string') acumulador = '';

      if(valor.idCategoria == id)
        return acumulador+valor.nome+'; ';
      else
        return acumulador;

  });
};

 $scope.erroFatal = function(msg = '') {
  $('#telaErroFatal').css('display', 'block');
  $('#telaErroFatalMensagem').text(msg);
 };
}); // FIM DO CÓDIGO ANGULAR
