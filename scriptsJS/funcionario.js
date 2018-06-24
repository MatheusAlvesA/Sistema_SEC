app.controller("FuncionarioCtrl", function($scope, requisitarAPI) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache de forma singleton
*/
$scope.listaFuncionarios = {};
$scope.funcaoInserirDisponivel = true;
$scope.listarFuncionarios = function() {
    var comando = {
      'comando': 'listar',
      'alvo': 'funcionarios'
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.listaFuncionarios = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
        },
        function (dados){  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );

};
$scope.listarFuncionarios();

$scope.apagarFuncionario = function(id) {

  if(!confirm("Tem certeza que quer deletar esse funcionario?")) return false;

  var comando = {
      'comando': 'deletar',
      'alvo': 'funcionario',
      'id': id
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
        	$scope.exibirErro('mensagemErroDesconhecido');
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // Provavelmente o funcionario é responsável por um contrato e não pode ser deletado por isso
          $scope.exibirErro('mensagemErroConhecido');
          return false;
        }
        else { // Sucesso ao deletar o funcionário
          $scope.listarFuncionarios();
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
      'alvo': 'funcionario',
      'parametros': {'nome': nome}
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
        	$scope.exibirErro('mensagemErroDesconhecido');
        	$scope.funcaoInserirDisponivel = true;
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // Provavelmente o funcionario é responsável por um contrato e não pode ser deletado por isso
          $scope.exibirErro('mensagemErroConhecido');
          $scope.funcaoInserirDisponivel = true;
          return false;
        }
        else { // Sucesso ao deletar o funcionário
          $scope.listarFuncionarios();
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

 $scope.erroFatal = function(msg = '') {
  $('#telaErroFatal').css('display', 'block');
  $('#telaErroFatalMensagem').text(msg);
 };
}); // FIM DO CÓDIGO ANGULAR
