app.controller("relatorioNotaFiscalCtrl", function($scope, requisitarAPI, relatorio) {


$scope.itensBuscados = [];
$scope.somaTotal = 0.00;
$scope.baixarRelatorio = function() {
  if($scope.NumeroNota === null || $scope.NumeroNota === '' || $scope.NumeroNota === undefined) return false;

    var comando = {
      'comando': 'relatorio',
      'alvo': 'NotaFiscal',
      'numero': $scope.NumeroNota
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
          	$scope.exibirErro('mensagemErroDesconhecido');
            return false;
          }
          if(dados.data.status === 'falha') {
          	$scope.exibirErro('mensagemErroConhecido');
          	return false;
          }
          //Sucesso
          $scope.itensBuscados = dados.data.itens;
          $scope.nomeCliente = dados.data.cliente;
          $scope.aplicarCores();
          $scope.calcularSoma();
          $('#plotDados').css({'display': 'block'});
          return true
        },
        function (dados) { // callback, falha na conexão do servido
        	$scope.exibirErro('mensagemErroConexao');
        	return false;
        }
    );

    return true;
};

$scope.aplicarCores = function() {
  $scope.itensBuscados.forEach(function(item) {
    let dataVencimento = fromData(item.dataVencimento);
    let hoje = new Date();
    if(dataVencimento <= hoje && (item.dataPagamento == null || item.dataPagamento == '')) {
      item.corVencimento = 'red';
    }
  });
};

$scope.exibirErro = function(elemento) {
	document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

$scope.calcularSoma = function () {
  $scope.somaTotal = $scope.itensBuscados.reduce(function (acumulador, item) {
    let valor  = Number(item.valorBruto) - Number(item.deducoes);
    return acumulador + valor;
  }, 0);
};

function fromData(data) {
  if(typeof data !== 'string' || data === '') return null;

  data = data.split('-');
  if(data.length < 3) return null;
  return new Date(data[0], data[1] - 1, data[2]);
}


$scope.imprimir = function() {relatorio.imprimir('plotDados');
}

}); // FIM DO CÓDIGO ANGULAR
