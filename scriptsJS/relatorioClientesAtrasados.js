app.controller("relatorioClientesAtrasadosCtrl", function($scope, requisitarAPI, relatorio) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache
*/
$scope.listaClientes = [];
$scope.baixarRelatorio = function() {
    var comando = {
      'comando': 'relatorio',
      'alvo': 'ClientesInadimplentes'
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
          	$scope.exibirErro('mensagemErroConexao');
            return false;
          }
          if(dados.data.status === 'falha') {
          	$scope.exibirErro('mensagemErroDesconhecido');
          	return false;
          }
          $scope.listaClientes = dados.data.sort(function(a,b){return a.nomeCliente.localeCompare(b.nomeCliente);});
        },
        function (dados) { // callback, falha na conexão do servido
        	$scope.exibirErro('mensagemErroDesconhecido');
        	return false;
        }
    );

};

$scope.baixarRelatorio();

$scope.exibirErro = function(elemento) {
	document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

$scope.imprimir = function() {relatorio.imprimir('plotDados');
}

}); // FIM DO CÓDIGO ANGULAR
