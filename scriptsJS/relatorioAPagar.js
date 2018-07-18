app.controller("relatorioAPagarCtrl", function($scope, requisitarAPI, relatorio) {


$scope.lista = [];
$scope.listaBKP = [];
$scope.listaProdutosFiltrada = [];
$scope.mesBKP = null;
$scope.anoBKP = null;
$scope.nNotas = 0;
$scope.somaValor = 0;
$scope.baixarRelatorio = function() {
  if($scope.mes === $scope.mesBKP && $scope.ano === $scope.anoBKP) { // Não alterada a data desde a ultima requisição
    $scope.aplicarFiltroProduto();
    return true;
  }

    var comando = {
      'comando': 'relatorio',
      'alvo': 'APagar',
      'parametros': {
          'mes': Number($scope.mes),
          'ano': Number($scope.ano)
      }
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            $scope.exibirErro('mensagemErroDesconhecido');
            return false;
          }
          if(dados.data.status === 'falha') {
            $scope.exibirErro('mensagemErroDesconhecido');
            return false;
          }

          $scope.lista = dados.data.sort(
            function(a,b) {
              a = fromData(a.dataPrestacao);
              b = fromData(b.dataPrestacao);
              if(a>b) return 1;
              if(a<b) return -1;
              return 0;
            }
          );

          $scope.listaBKP = copiar($scope.lista);

          $scope.aplicarFiltroProduto();

          $scope.anoBKP = $scope.ano;
          $scope.mesBKP = $scope.mes;
        },
        function (dados) { // callback, falha na conexão do servido
          $scope.exibirErro('mensagemErroConexao');
          return false;
        }
    );

};

$scope.aplicarFiltroProduto = function() {
  $scope.lista = $scope.listaBKP.filter(function(x) {
    if($scope.listaProdutosFiltrada.length < 1) return true;

    let idProduto = x.produto.id;
    for(let loop = 0; loop < $scope.listaProdutosFiltrada.length; loop++){
      if(idProduto === null)
        return true;
      if(idProduto == $scope.listaProdutosFiltrada[loop])
        return true;
    }

    return false;
  });
};

$scope.listaSubProdutos = [];
$scope.listarSubProdutos = function() {
    var comando = {
      'comando': 'listar',
      'alvo': 'produtos'
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              return false;
          }
          $scope.listaSubProdutos = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
          $scope.listaProdutosFiltrada = $scope.listaSubProdutos.map(function(x){return x.id.toString();});
        },
        function (dados) {  // callback, falha na conexão do servidor
          return false;
        }
    );

};


$scope.inicializar = function() {
  $scope.ano = (new Date()).getFullYear();
  $scope.mes = ((new Date()).getMonth() + 1).toString();

  $scope.listarSubProdutos();
  $scope.baixarRelatorio();
};

$scope.inicializar();

$scope.submeterNota = function(id) {
  let item = null;
  for(let x = 0; x < $scope.listaBKP.length; x++) {
    if($scope.listaBKP[x].idParcelaContrato == id) {
      item = $scope.listaBKP[x];
      break;
    }
  }

    var comando = {
      'comando': 'atualizar',
      'alvo': 'item',
      'id': id,
      'parametros': {
        'notaFiscalAPagar': item.notaFiscalAPagar
      }
    };

    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined || dados.data.status !== 'sucesso') { // erro desconhecido do servidor
            item.notaFiscalAPagar = '';
            item.tableClasse = 'table-danger';
            setTimeout(function(){item.tableClasse = '';}, 700);
            return false;
          }
          $scope.nNotas++;
          $scope.somaValor += Number(item.valorAPagar);
          item.tableClasse = 'table-success';
        },
        function (dados) {  // callback, falha na conexão do servidor
          item.notaFiscalAPagar = '';
        }
    );
};

$scope.mesEscolhidoOnString = function() {
  let num = Number($scope.mes);

  switch(num) {
    case 1:
      return 'JANEIRO';
    break;
    case 2:
      return 'FEVEREIRO';
    break;
    case 3:
      return 'MARÇO';
    break;
    case 4:
      return 'ABRIL';
    break;
    case 5:
      return 'MAIO';
    break;
    case 6:
      return 'JUNHO';
    break;
    case 7:
      return 'JULHO';
    break;
    case 8:
      return 'AGOSTO';
    break;
    case 9:
      return 'SETEMBRO';
    break;
    case 10:
      return 'OUTUBRO';
    break;
    case 11:
      return 'NOVEMBRO';
    break;
    case 12:
      return 'DEZEMBRO';
    break;

    default:
      return ''; // Mês inválido
  }
};

/*
    Funções secundárias
*/

$scope.exibirErro = function(elemento) {
  document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

$scope.imprimir = function() {relatorio.imprimir('plotDados');}

}); // FIM DO CÓDIGO ANGULAR

function copiar(objeto) {return JSON.parse(JSON.stringify(objeto));}
