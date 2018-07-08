app.controller("relatorioClientesAtrasadosCtrl", function($scope, requisitarAPI, relatorio) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache
*/
$scope.listaClientes = [];
$scope.backUpListaClientes = [];
$scope.baixarRelatorio = function() {
    var comando = {
      'comando': 'relatorio',
      'alvo': 'ClientesInadimplentes'
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
          $scope.listaClientes = dados.data.sort(function(a,b){return a.nomeCliente.localeCompare(b.nomeCliente);});
          $scope.backUpListaClientes = $scope.listaClientes.map(function(x) {return x;});
        },
        function (dados) { // callback, falha na conexão do servido
        	$scope.exibirErro('mensagemErroConexao');
        	return false;
        }
    );

};

$scope.baixarRelatorio();

$scope.filtrarResultado = function() {
  $scope.listaClientes = $scope.filtroNome($scope.backUpListaClientes);

  if($scope.dataDesde != null && $scope.dataDesde != undefined )
    $scope.listaClientes = $scope.filtroDataDesde($scope.listaClientes);

  if($scope.dataAte != null && $scope.dataAte != undefined )
    $scope.listaClientes = $scope.filtroDataAte($scope.listaClientes);
};

$scope.filtroNome = function(lista) {
  let nome = $scope.buscaNomeCliente.toUpperCase();
  let filtrado = lista.filter(function (x) {
    let atual = x.nomeCliente.toUpperCase();
    if(atual.indexOf(nome) !== -1)
      return true;
    return false;
  });
  return filtrado;
};

$scope.filtroDataDesde = function(lista) {
  let novaLista = copiar(lista);

  novaLista.forEach(function (cliente) {
    cliente.contratos.forEach(function (contrato) {
      contrato.valorSoma = 0;
      contrato.itens = contrato.itens.filter(function (item) {
        let vencimento = item.dataVencimento;
        let atual = new Date( Number(vencimento.split('-')[0]), Number(vencimento.split('-')[1])-1, Number(vencimento.split('-')[2]) );
        if($scope.dataDesde <= atual) {
          contrato.valorSoma += item.valorBruto;
          return true;
        }
        return false;
      });
    });
  });

  novaLista.forEach(function (cliente) {
    cliente.somaBruto = 0;
    cliente.contratos = cliente.contratos.filter(function (contrato) {
      if(contrato.itens.length > 0) {
        cliente.somaBruto += contrato.valorSoma;
        return true
      }
      return false;
    });
  });

  novaLista = novaLista.filter(function (cliente) {
      return cliente.contratos.length > 0;
  });

  return novaLista;
};

$scope.filtroDataAte = function(lista) {
  let novaLista = copiar(lista);

  novaLista.forEach(function (cliente) {
    cliente.contratos.forEach(function (contrato) {
      contrato.valorSoma = 0;
      contrato.itens = contrato.itens.filter(function (item) {
        let vencimento = item.dataVencimento;
        let atual = new Date( Number(vencimento.split('-')[0]), Number(vencimento.split('-')[1])-1, Number(vencimento.split('-')[2]) );
        if($scope.dataAte >= atual) {
          contrato.valorSoma += item.valorBruto;
          return true;
        }
        return false;
      });
    });
  });

  novaLista.forEach(function (cliente) {
    cliente.somaBruto = 0;
    cliente.contratos = cliente.contratos.filter(function (contrato) {
      if(contrato.itens.length > 0) {
        cliente.somaBruto += contrato.valorSoma;
        return true
      }
      return false;
    });
  });

  novaLista = novaLista.filter(function (cliente) {
      return cliente.contratos.length > 0;
  });

  return novaLista;
};

$scope.exibirErro = function(elemento) {
	document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

$scope.imprimir = function() {relatorio.imprimir('plotDados');
}

}); // FIM DO CÓDIGO ANGULAR

function copiar(objeto) {return JSON.parse(JSON.stringify(objeto));}
