app.service('mensagemERRO', function($http) {
  this.conexaoServidor = function () {
    document.getElementById('mensagemErroConexao').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroConexao').style.display = 'none';}, 5000);
  };
  this.editarItem = function () {
    document.getElementById('mensagemErroEditarItem').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroEditarItem').style.display = 'none';}, 5000);
  };
  this.editarItemConexao = function () {
    document.getElementById('mensagemErroEditarItemConexao').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroEditarItemConexao').style.display = 'none';}, 5000);
  };
  this.edicaoConexaoServidor = function () {
    document.getElementById('telaEdicao_mensagemConexao').style.display = 'block';
    setTimeout(function(){document.getElementById('telaEdicao_mensagemConexao').style.display = 'none';}, 5000);
  };
  this.criacaoConexaoServidor = function () {
    document.getElementById('mensagemErroCriar_con').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroCriar_con').style.display = 'none';}, 5000);
  };
  this.criarContrato = function () {
    document.getElementById('mensagemErroCriar').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroCriar').style.display = 'none';}, 5000);
  };
  this.atualizarContrato = function () {
    document.getElementById('telaEdicao_mensagemAtualizar').style.display = 'block';
    setTimeout(function(){document.getElementById('telaEdicao_mensagemAtualizar').style.display = 'none';}, 5000);
  };
  this.consultarServidor = function () {
    document.getElementById('mensagemErroConsultar').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErroConsultar').style.display = 'none';}, 5000);
  };
  this.contratoNotFound = function () {
    document.getElementById('mensagemErro404').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErro404').style.display = 'none';}, 5000);
  };
  this.itemNotFound = function () {
    document.getElementById('mensagemErro404Item').style.display = 'block';
    setTimeout(function(){document.getElementById('mensagemErro404Item').style.display = 'none';}, 5000);
  };
});


var controle_tempo_busca = 0; // guarda quando a ultima consulta foi realizada em millisegundos
app.controller("ContratoCtrl", function($scope, requisitarAPI, mensagemERRO) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache de forma singleton
*/
$scope.cache = {};
$scope.inicializarCache = function() {
  if($scope.cache.listaClientes === undefined) {
    var comando = {
      'comando': 'listar',
      'alvo': 'clientes'
    };
    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.cache.listaClientes = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }

  if($scope.cache.listaMensageiros === undefined) {
    var comando = {
      'comando': 'listar',
      'alvo': 'mensageiros'
    };
    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.cache.listaMensageiros = dados.data;
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }

  if($scope.cache.listaFuncionarios === undefined) {
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
          $scope.cache.listaFuncionarios = dados.data;
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }

  if($scope.cache.listaTipoAcesso === undefined) {
    var comando = {
      'comando': 'listar',
      'alvo': 'tipoAcesso'
    };
    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.cache.listaTipoAcesso = dados.data;
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }

  if($scope.cache.listaProdutos === undefined) {
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
          $scope.cache.listaProdutos = dados.data.sort(function(a,b){return a.nome.localeCompare(b.nome);});
          // Caso as categorias já estejam carregadas
          if($scope.cache.listaCategoriasProduto != undefined && $scope.cache.listaCategoriasProduto != null && $scope.cache.listaCategoriasProduto.length >= 1) {
            // Executando essa função de novo caso tenha falhado quando foi chamada antes
            $scope.setCategoriaProdutoCriacao($scope.cache.listaCategoriasProduto[0].id);
            $scope.setCategoriaProdutoEdicao($scope.cache.listaCategoriasProduto[0].id);
          }
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }

  if($scope.cache.listaCategoriasProduto === undefined) {
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
          $scope.cache.listaCategoriasProduto = dados.data;
          if($scope.cache.listaCategoriasProduto.length >= 1) { // Existem uma ou mais categorias

            $scope.setCategoriaProdutoCriacao($scope.cache.listaCategoriasProduto[0].id);
            let select = document.getElementById("telaCriacao_categoria");
            select.onchange = function (event) {
              $scope.setCategoriaProdutoCriacao( event.target.value );
              if(!$scope.$$phase) $scope.$digest();
            };

            $scope.setCategoriaProdutoEdicao($scope.cache.listaCategoriasProduto[0].id);
            select = document.getElementById("telaEdicao_categoria");
            select.onchange = function (event) {
              $scope.setCategoriaProdutoEdicao( event.target.value );
              if(!$scope.$$phase) $scope.$digest();
            };

          }
        },
        function (dados) {  // callback, falha na conexão do servido
          $scope.erroFatal();
          return false;
        }
    );
  }
};
$scope.inicializarCache();

/*________________________________________________________
  #########SUBSISTEMA BUSCA DE CONTRATOS#############
__________________________________________________________*/
  $scope.listaContratos = []; // sempre guarda a lista de contratos retornados pela API na ultima requisição
  $scope.contratoSelecionado = {}; // guarda o contrato que foi escolhido para ser editado pelo usuário
  $scope.contratoSelecionadoBACKUP = {}; // Um back up servirá para detectar se o contratoSelecionado foi editado pelo usuário ao realizar update
  $scope.estadoBuscando = false; //Guarda se o sistema está executando uma busca
  $scope.listaProdutosString = []; // Guarda a lista de subprodutos de um contrato, mas na forma de um vetor de strings. cada string é o nome do subproduto
  $scope.limparCamposBusca = function() {
    $scope.busca_nome_cliente = null;
    $scope.busca_contrato_inicio = null;
    $scope.busca_contrato_fim = null;
    $scope.busca_item_inicio = null;
    $scope.busca_item_fim = null;
    $scope.busca_filtro_check = null;
  };

	$scope.submeterBusca = function(id) {
    if($scope.estadoBuscando) return false;
    var d = new Date();
    if((d.getTime()-controle_tempo_busca) < 1000 || $scope.busca_nome_cliente === null || $scope.busca_nome_cliente === undefined || $scope.busca_nome_cliente === '') return false;
    controle_tempo_busca = d.getTime();
    $scope.avisarBuscando();

    var contrato_inicio = toData($scope.busca_contrato_inicio);
    var contrato_fim = toData($scope.busca_contrato_fim);
    
    var filtro_check = null;
    if($scope.busca_filtro_check != 'nenhum' && $scope.busca_filtro_check !== undefined)
      filtro_check = $scope.busca_filtro_check;

    if(id === undefined) {
      var dados = {
        'nome_cliente': $scope.busca_nome_cliente,
        'tipo_contrato': filtro_check,
        'vencimento_contrato_inicio': contrato_inicio,
        'vencimento_contrato_fim': contrato_fim
  		};
    }
    else {
      var dados = {
        'id_cliente': id,
        'tipo_contrato': filtro_check,
        'vencimento_contrato_inicio': contrato_inicio,
        'vencimento_contrato_fim': contrato_fim
      };
    }

    var comando = {
      'comando': 'buscar',
      'parametros': {
        'alvo': 'contrato',
        'parametros': dados
      }
    };

    var retorno = null;

		requisitarAPI.post(comando,
      function (dados) { // callback, sucesso na conexão com o servidor
        $scope.avisarBuscando(false);
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            mensagemERRO.consultarServidor(); // informando erro no servidor
            return false;
          }
          if(dados.data.erro !== undefined) { // algum erro conhecido detectado pelo servidor
            if(dados.data.erro == 404) // erro comum: não encontrou o contrato
              mensagemERRO.contratoNotFound();
            else // algum outro erro
              mensagemERRO.consultarServidor(); // informando erro no servidor
            return false;
          }    
          $scope.processarBusca(dados.data);
      },
      function (dados) { // callback, falha na conexão do servidor
        mensagemERRO.conexaoServidor(); // informando erro na internet
        $scope.avisarBuscando(false);
        return false;
      }
    );

    return true;
	};


  $scope.tratarDadosContratos = function(contratos) {

    contratos.sort(function (a, b) {
      a = new Date(a.dataInicial);
      b = new Date(b.dataInicial);
      if(a > b) return -1;
      if(a == b) return 0;
      if(a < b) return 1;
    });

    return contratos.map(function (e) {
      var ativo = {};
      if(e.ativo) {
        ativo.cor = 'green';
        ativo.ico = 'check'
      }
      else {
        ativo.cor = 'red';
        ativo.ico = 'times';
      }
      novo = {
        "dataInicial": e.dataInicial,
        "dataFinal": e.dataFinal,
        "ativo": ativo,
        "id": e.idContrato
      }

      return novo;
    });
  };

  $scope.processarBusca = function(dados) {
    if(dados.resultado === 'conclusivo') { // Só existe um cliente com o nome informado
      $scope.limparCamposBusca();
      dados = dados.dados; // descartando informações desnescesárias do retorno
      $scope.listaContratos = dados; // guardando no escopo global para uso posterior
      $('#plotBusca').css("display", "block");
      $('#plotBuscaClientes').css("display", "none");

      if(dados.length)
        $scope.nomeClienteSelecionado = dados[0].nomeCliente;
      else 
        $scope.nomeClienteSelecionado = 'Nenhum contrato encontrado';
      $scope.contratosBuscados = $scope.tratarDadosContratos(dados);
      $(document).ready(function(){$('[data-toggle="tooltip"]').tooltip();}); // ativando os tooltips
    }
    else { // Existem mais de um cliente com as caracteristicas informadas
      dados = dados.dados; // descartando informações desnescesárias do retorno
      $('#plotBusca').css("display", "none");
      $('#plotBuscaClientes').css("display", "block");

      $scope.clientesBuscados = dados;
    }
    return true;
  };

  /*
    Avisa ao usuário que o sistema está realizando a busca
    estado representa se a busca está ocorrendo(true) ou não(false)
  */
  $scope.avisarBuscando = function(estado = true) {
    $scope.estadoBuscando = estado;
    if(estado) {
      $('#bntBuscarCliente').html("Buscando... <span class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></span>");
      $('#plotItens').css({"display": 'none'}); // removendo caso esteja na tela
    }
    else
      $('#bntBuscarCliente').html("Buscar <span class=\"fa fa-search\" aria-hidden=\"true\"></span>");
  }

/*_______________________________________________________________
  ######### SUBSISTEMA EDIÇÃO DE CONTRATOS #############
_________________________________________________________________*/
 $scope.editarContrato = function(id) {
   var contrato = $scope.buscarContratoID(id);
   if(contrato === false) return false;

   $('#tela_edicao').modal({keyboard: false});
   $scope.setContrato(contrato);
 };

$scope.deletarContrato = function() {

  if(!confirm("ATENÇÃO !!!\nTem certeza que quer deletar esse contrato?\n\nEsta ação é irreversível")) return false;

  var comando = {
      'comando': 'deletar',
      'alvo': 'contrato',
      'id': $scope.contratoSelecionado.idContrato
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        $scope.avisarCriando(false);
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
          return false;
        }
        else { // sucesso ao deletar o contrato
          $('#tela_edicao').modal('hide');
          // apagar o contrato
          var temp = [];
          for(x = 0; x < $scope.listaContratos.length; x++) // buscando qual foi o contrato editado na lista de contratos
            if($scope.listaContratos[x].idContrato !== $scope.contratoSelecionado.idContrato)
              temp.push($scope.listaContratos[x]);
          $scope.listaContratos = temp;
          $scope.contratosBuscados = $scope.tratarDadosContratos($scope.listaContratos);
          return true;
        }
      },
      function(dados) { // callback de falha
        return false;
      }
  );
};

$scope.atualizarContrato = function() {
  if($scope.estadoAtualizando) return false;
  $scope.avisarEditando(true);

  $listaDeModificacoes = $scope.listarModificacoesContrato();

  var comando = {
    'comando': 'atualizar',
    'alvo': 'contrato',
    'parametros': {
      'id': $scope.contratoSelecionado.idContrato,
      'parametros': $listaDeModificacoes
    }
  };

  requisitarAPI.post(comando,
  function(dados) { //callback de sucesso
    $scope.avisarEditando(false);
    if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
      mensagemERRO.edicaoConexaoServidor(); // informando erro no servidor
      return false;
    }
    if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
      mensagemERRO.atualizarContrato();
      return false;
    }
    else { // sucesso ao aplicar as modificações
      document.getElementById('telaEdicao_mensagemSucesso').style.display = 'block'; // mostrando mensagem de sucesso
      setTimeout(function(){document.getElementById('telaEdicao_mensagemSucesso').style.display = 'none';}, 5000); // remover mensagem após 5 segundos

      var index = 0;
      for(x = 0; x < $scope.listaContratos.length; x++) // buscando qual foi o contrato editado na lista de contratos
        if($scope.listaContratos[x].idContrato === $scope.contratoSelecionado.idContrato)
          index = x;

      $scope.listaContratos[index] = copiar($scope.contratoSelecionado); // Atualizando o contrato na lista
      $scope.listaContratos[index] = $scope.desFormatarContrato($scope.listaContratos[index]); // formatando da forma correta

      $scope.contratosBuscados = $scope.tratarDadosContratos($scope.listaContratos);

      $scope.contratoSelecionadoBACKUP = copiar($scope.contratoSelecionado); // refazendo o backup para detectar novas modificações

      return true;
    }
  },
  function(dados) { // callback de falha
    mensagemERRO.edicaoConexaoServidor(); // informando erro no servidor
    $scope.avisarEditando(false);
    return false;
  }
  );
};

/*
  Essa função detecta todas as modificações feitas pelo uduário no contrato e as lista em em vetor
*/
$scope.listarModificacoesContrato = function() {
  var modificacoes = [];

  $scope.contratoSelecionado.idCliente = Number($("#telaEdicao_nome_cliente").val());
  $scope.contratoSelecionado.idsProdutos = $("#telaEdicao_servico").val().map(function(x) {return Number(x);});
  $scope.contratoSelecionado.idCategoria = Number($("#telaEdicao_categoria").val());
  $scope.contratoSelecionado.idFuncionario = Number($("#telaEdicao_funcionario").val());
  $scope.contratoSelecionado.idMensageiro = Number($("#telaEdicao_mensageiro").val());
  $scope.contratoSelecionado.idTipoAcesso = Number($("#telaEdicao_tipoAcesso").val());

  if($scope.contratoSelecionado.idCliente !== $scope.contratoSelecionadoBACKUP.idCliente)
    modificacoes.push({'idCliente': $scope.contratoSelecionado.idCliente});
  if(JSON.stringify($scope.contratoSelecionado.idsProdutos) !== JSON.stringify($scope.contratoSelecionadoBACKUP.idsProdutos))
    modificacoes.push({'idsProdutos': copiar($scope.contratoSelecionado.idsProdutos)});
  if($scope.contratoSelecionado.idCategoria !== $scope.contratoSelecionadoBACKUP.idCategoria)
    modificacoes.push({'idCategoria': $scope.contratoSelecionado.idCategoria});
  if($scope.contratoSelecionado.idFuncionario !== $scope.contratoSelecionadoBACKUP.idFuncionario)
    modificacoes.push({'idFuncionario': $scope.contratoSelecionado.idFuncionario});
  if($scope.contratoSelecionado.idMensageiro !== $scope.contratoSelecionadoBACKUP.idMensageiro)
    modificacoes.push({'idMensageiro': $scope.contratoSelecionado.idMensageiro});
  if($scope.contratoSelecionado.idTipoAcesso !== $scope.contratoSelecionadoBACKUP.idTipoAcesso)
    modificacoes.push({'idTipoAcesso': $scope.contratoSelecionado.idTipoAcesso});
  if($scope.contratoSelecionado.ativo !== $scope.contratoSelecionadoBACKUP.ativo)
    modificacoes.push({'ativo': $scope.contratoSelecionado.ativo});

/*
  Testar se as datas do contrato foram modificadas se tornou uma operação de lógica excessivamente complexa pois:
  se a data foi alterada para null ou já era null mesmo ela vai causar um erro ao tentar fazer data.localeCompare
  se a data não for nula é nescesário passar ela por operações para transformala em uma string aplicavel a localeCompare
  simplificar essa operação...
*/
  if((toData($scope.contratoSelecionado.dataEmissao) === null && $scope.contratoSelecionadoBACKUP.dataEmissao !== null) ||
     (toData($scope.contratoSelecionado.dataEmissao) !== null && $scope.contratoSelecionadoBACKUP.dataEmissao === null)) {
    if(toData($scope.contratoSelecionado.dataEmissao) !== null)
      modificacoes.push({'dataEmissao': toData($scope.contratoSelecionado.dataEmissao).split('-').reverse().join('-')});
    else
      modificacoes.push({'dataEmissao': null});
  }
  else {
    if(toData($scope.contratoSelecionado.dataEmissao) !== null && $scope.contratoSelecionadoBACKUP.dataEmissao !== null) {
      if(toData($scope.contratoSelecionado.dataEmissao).localeCompare(toData(new Date($scope.contratoSelecionadoBACKUP.dataEmissao))) !== 0)
        modificacoes.push({'dataEmissao': toData($scope.contratoSelecionado.dataEmissao).split('-').reverse().join('-')});
    }
  }

  if((toData($scope.contratoSelecionado.dataFinal) === null && $scope.contratoSelecionadoBACKUP.dataFinal !== null) ||
     (toData($scope.contratoSelecionado.dataFinal) !== null && $scope.contratoSelecionadoBACKUP.dataFinal === null)) {
    if(toData($scope.contratoSelecionado.dataFinal) !== null)
     modificacoes.push({'dataFinal': toData($scope.contratoSelecionado.dataFinal).split('-').reverse().join('-')});
    else
      modificacoes.push({'dataFinal': null});
  }
  else {
    if(toData($scope.contratoSelecionado.dataFinal) !== null && $scope.contratoSelecionadoBACKUP.dataFinal !== null) {
      if(toData($scope.contratoSelecionado.dataFinal).localeCompare(toData(new Date($scope.contratoSelecionadoBACKUP.dataFinal))) !== 0)
        modificacoes.push({'dataFinal': toData($scope.contratoSelecionado.dataFinal).split('-').reverse().join('-')});
    }
  }

  if((toData($scope.contratoSelecionado.dataCancelamento) === null && $scope.contratoSelecionadoBACKUP.dataCancelamento !== null) ||
     (toData($scope.contratoSelecionado.dataCancelamento) !== null && $scope.contratoSelecionadoBACKUP.dataCancelamento === null)) {
    if(toData($scope.contratoSelecionado.dataCancelamento) !== null)
      modificacoes.push({'dataCancelamento': toData($scope.contratoSelecionado.dataCancelamento).split('-').reverse().join('-')});
    else
      modificacoes.push({'dataCancelamento': null});
  }
  else {
    if(toData($scope.contratoSelecionado.dataCancelamento) !== null && $scope.contratoSelecionadoBACKUP.dataCancelamento !== null) {
      if(toData($scope.contratoSelecionado.dataCancelamento).localeCompare(toData(new Date($scope.contratoSelecionadoBACKUP.dataCancelamento))) !== 0)
        modificacoes.push({'dataCancelamento': toData($scope.contratoSelecionado.dataCancelamento).split('-').reverse().join('-')});
    }
  }

  if((toData($scope.contratoSelecionado.dataInicial) === null && $scope.contratoSelecionadoBACKUP.dataInicial !== null) ||
     (toData($scope.contratoSelecionado.dataInicial) !== null && $scope.contratoSelecionadoBACKUP.dataInicial === null)) {
    if(toData($scope.contratoSelecionado.dataCancelamento) !== null)
      modificacoes.push({'dataInicial': toData($scope.contratoSelecionado.dataInicial).split('-').reverse().join('-')});
    else
      modificacoes.push({'dataInicial': null});
  }
  else {
    if(toData($scope.contratoSelecionado.dataInicial) !== null && $scope.contratoSelecionadoBACKUP.dataInicial !== null) {
      if(toData($scope.contratoSelecionado.dataInicial).localeCompare(toData(new Date($scope.contratoSelecionadoBACKUP.dataInicial))) !== 0)
        modificacoes.push({'dataInicial': toData($scope.contratoSelecionado.dataInicial).split('-').reverse().join('-')});
    }
  }

  if($scope.contratoSelecionado.observacao.localeCompare($scope.contratoSelecionadoBACKUP.observacao) !== 0) {
    modificacoes.push({'observacao': $scope.contratoSelecionado.observacao});
  }

  if($scope.contratoSelecionado.valorTotal !== $scope.contratoSelecionadoBACKUP.valorTotal)
    modificacoes.push({'valorTotal': $scope.desformatarReal($scope.contratoSelecionado.valorTotal)});

  return modificacoes;
};

 $scope.buscarContratoID = function(id) {
  for(x = 0; x < $scope.listaContratos.length; x++)
    if($scope.listaContratos[x].idContrato === id)
      return $scope.listaContratos[x];
  return false;
 };

 $scope.setContrato = function(contrato) {
  $scope.contratoSelecionado = copiar(contrato);
  $scope.formatarContrato($scope.contratoSelecionado);

  $("#telaEdicao_nome_cliente").val($scope.contratoSelecionado.idCliente);
  $("#telaEdicao_servico").val($scope.contratoSelecionado.idsProdutos);
  $("#telaEdicao_categoria").val($scope.contratoSelecionado.idCategoria);
  $scope.setCategoriaProdutoEdicao($scope.contratoSelecionado.idCategoria);
  if(!$scope.$$phase) $scope.$digest();
  $("#telaEdicao_funcionario").val($scope.contratoSelecionado.idFuncionario);
  $("#telaEdicao_mensageiro").val($scope.contratoSelecionado.idMensageiro);
  $("#telaEdicao_tipoAcesso").val($scope.contratoSelecionado.idTipoAcesso);

  $scope.listaProdutosString = $scope.cache.listaProdutos.filter(function (x) {
    for(let loop = 0; loop < $scope.contratoSelecionado.idsProdutos.length; loop++) {
      if($scope.contratoSelecionado.idsProdutos[loop] == x.id)
        return true;
    }
    return false;
  });
  $scope.listaProdutosString = $scope.listaProdutosString.map(function (x) {return x.nome;});

  $scope.contratoSelecionadoBACKUP = copiar($scope.contratoSelecionado);
 };

 $scope.formatarContrato = function (contrato) {
  contrato.dataEmissao = fromData(contrato.dataEmissao);
  contrato.dataInicial = fromData(contrato.dataInicial);
  contrato.dataFinal = fromData(contrato.dataFinal);
  contrato.dataCancelamento = fromData(contrato.dataCancelamento);
  contrato.valorTotal = $scope.formatarReal(contrato.valorTotal);
 };
 
 $scope.desFormatarContrato = function (contrato) {
  contrato.dataEmissao = toData(contrato.dataEmissao);
  if(contrato.dataEmissao !== null) contrato.dataEmissao = contrato.dataEmissao.split('-').reverse().join('-');

  contrato.dataInicial = toData(contrato.dataInicial);
  if(contrato.dataInicial !== null) contrato.dataInicial = contrato.dataInicial.split('-').reverse().join('-');

  contrato.dataFinal = toData(contrato.dataFinal);
  if(contrato.dataFinal !== null) contrato.dataFinal = contrato.dataFinal.split('-').reverse().join('-');

  contrato.dataCancelamento = toData(contrato.dataCancelamento);
  if(contrato.dataCancelamento !== null) contrato.dataCancelamento = contrato.dataCancelamento.split('-').reverse().join('-');

  contrato.valorTotal = $scope.desformatarReal(contrato.valorTotal);

  return contrato;
 };
  /*
    Avisa ao usuário que o sistema está realizando a busca
    estado representa se a busca está ocorrendo(true) ou não(false)
  */
  $scope.estadoAtualizando = false;
  $scope.avisarEditando = function(estado = true) {
    $scope.estadoAtualizando = estado;
    if(estado)
      $('#bnt_atualizar_contrato').html("Atualizando... <span class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></span>");
    else
      $('#bnt_atualizar_contrato').html("Atualizar");
  };

/*
  Essa função recebe como parâmetro um 'number' e retorna esse número formatado para Real Brasileiro
  Ex:
    654654.11 => R$654.654,11
    17 => R$17,00
    -99.07 => R$-99,07
*/
 $scope.formatarReal = function (valor) {
  vetor = valor.toString().split('.');
  var ehNegativo = false;
  if(vetor[0].split('')[0] == '-') {
    ehNegativo = true;
    vetor[0] = vetor[0].split('-')[1];
  }
  var antesVirgula = vetor[0];
  var dpsVirgula = vetor[1]; // posívelmente vai ser undefined
  antesVirgula = antesVirgula.split('');

  var final = '';
  for(var loop = antesVirgula.length-1; loop >= 0; loop--) {
    final += antesVirgula[loop];
    if(((antesVirgula.length - loop) % 3) == 0 && loop != (antesVirgula.length-1) && loop != 0)
      final += '.';
  }

  final = final.split('').reverse().join('');

  if(dpsVirgula !== undefined) {
    if(dpsVirgula.length == 1)
      dpsVirgula += '0';
    final += ',' + dpsVirgula;
  }
  else final += ',00';
  
  if(ehNegativo)
    return 'R$-'+final;
  return 'R$'+final;
 };

// Reverte a função anterior
 $scope.desformatarReal = function (valor) {
  if(typeof valor === 'number') return valor; // Já está no formato correto
  var tratado = valor.replace(/\./g, '') // remover os pontos
                  .replace(/[R$]/g, '') // remover o R$
                  .replace(/,/g, '.'); // trocar a virgula por um ponto para seguir o padrão
  return Number(tratado);
 }

/*
_________________________________________________________________
  ######### SUBSISTEMA CRIAÇÃO DE CONTRATOS #############
_________________________________________________________________
*/

$scope.contratoCriando = {'ativo': true}; // valor default para os dados do contrato sendo criado
$scope.criarContrato = function() {

  var contrato = copiar($scope.contratoCriando);

  contrato.idCliente = Number($("#telaCriacao_nome_cliente").val());
  contrato.idsProdutos = $("#telaCriacao_servico").val().map(function(x) {return Number(x);});
  contrato.idCategoria = Number($("#telaCriacao_categoria").val());
  contrato.idFuncionario = Number($("#telaCriacao_funcionario").val());
  contrato.idMensageiro = Number($("#telaCriacao_mensageiro").val());
  contrato.idTipoAcesso = Number($("#telaCriacao_tipoAcesso").val());

  contrato.dataInicial = toData(contrato.dataInicial);
  if(contrato.dataInicial !== null) contrato.dataInicial = contrato.dataInicial.split('-').reverse().join('-');

  contrato.dataFinal = toData(contrato.dataFinal);
  if(contrato.dataFinal !== null) contrato.dataFinal = contrato.dataFinal.split('-').reverse().join('-');

  contrato.dataEmissao = toData(contrato.dataEmissao);
  if(contrato.dataEmissao !== null) contrato.dataEmissao = contrato.dataEmissao.split('-').reverse().join('-');

  contrato.dataCancelamento = toData(contrato.dataCancelamento);
  if(contrato.dataCancelamento !== null) contrato.dataCancelamento = contrato.dataCancelamento.split('-').reverse().join('-');

  var comando = {
      'comando': 'criar',
      'alvo': 'contrato',
      'parametros': contrato
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        $scope.avisarCriando(false);
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
          mensagemERRO.criacaoConexaoServidor(); // informando erro no servidor
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
          mensagemERRO.criarContrato();
          return false;
        }
        else { // sucesso ao criar o contrato
          document.getElementById('mensagemSucessoCriar').style.display = 'block'; // mostrando mensagem de sucesso
          setTimeout(function(){document.getElementById('mensagemSucessoCriar').style.display = 'none';}, 5000); // remover mensagem após 5 segundos

          $("#telaCriacao_servico").val([]);
          $scope.contratoCriando = {'ativo': true};

          return true;
        }
      },
      function(dados) { // callback de falha
        mensagemERRO.edicaoConexaoServidor(); // informando erro no servidor
        $scope.avisarCriando(false);
        return false;
      }
  );
};

$scope.listaProdutosFiltradaCriacao = [];
$scope.setCategoriaProdutoCriacao = function(id) {
  if($scope.cache.listaProdutos == null || $scope.cache.listaProdutos == undefined) // A lista de produtos não está carregada ainda
    return false;

  $scope.listaProdutosFiltradaCriacao = $scope.cache.listaProdutos.filter(function (produto) {
    if(produto.idCategoria == id) return true;
    else                          return false;
  });

  return true;
};
$scope.listaProdutosFiltradaEdicao = [];
$scope.setCategoriaProdutoEdicao = function(id) {
  if($scope.cache.listaProdutos == null || $scope.cache.listaProdutos == undefined) // A lista de produtos não está carregada ainda
    return false;

  $scope.listaProdutosFiltradaEdicao = $scope.cache.listaProdutos.filter(function (produto) {
    if(produto.idCategoria == id) return true;
    else                          return false;
  });

  return true;
};

  /*
    Avisa ao usuário que o sistema está realizando a busca
    estado representa se a busca está ocorrendo(true) ou não(false)
  */
  $scope.estadoCriando = false;
  $scope.avisarCriando = function(estado = true) {
    $scope.estadoCriando = estado;
    if(estado)
      $('#bnt_criar_contrato').html("Criando... <span class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></span>");
    else
      $('#bnt_criar_contrato').html("Criar Contrato");
  };

/*_________________________________________________________________
  ####### SUBSISTEMA CRIAÇÃO E EDIÇÃO DE ITENS DE CONTRATO #######
_________________________________________________________________*/
  $scope.itemEditando = {}; // O item que estiver sendo editado será guardado aqui temporariamente.
  $scope.eventListenerInserirItem = false; // O gatilho da tecla 'enter' ainda não foi criado
  $scope.mostrarItens = function(idContrato) {
    $scope.setContrato($scope.buscarContratoID(idContrato)); // Armazenando informações do contrato referente a estes itens
    /*
      Adicionando listener de evento para inserir apertando enter
    */
    if(!$scope.eventListenerInserirItem) {
      $('#ItemEditObservacoes').bind('keyup', (e) => {
        if(e.key == 'Enter')
          $scope.consolidarItemEditando();
      });
      $scope.eventListenerInserirItem = true; //Garantindo que não executará esse trecho mais de uma vez
    }

    $scope.notaFiscalPreConfigurada = false; // removendo botão desnescesário da tela

    $scope.idContratoDosItens = idContrato;

    var comando = {
      'comando': 'buscar',
      'parametros': {
        'alvo': 'item',
        'idContrato': idContrato
      }
    };

    requisitarAPI.post(comando,
      function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            mensagemERRO.consultarServidor(); // informando erro no servidor
            return false;
          }
          if(dados.data.erro !== undefined) { // algum erro conhecido detectado pelo servidor
            if(dados.data.erro == 404) // erro comum: não encontrou itens neste contrato
              mensagemERRO.itemNotFound();
            else // algum outro erro
              mensagemERRO.consultarServidor(); // informando erro no servidor
            return false;
          }
          $('#plotBusca').css({"display": 'none'}); // removendo a lista de contratos da tela
          $('#plotItens').css({"display": 'block'}); // preparando para plotar os itens do contrato  
          $scope.itensBuscados = $scope.tratarItens(dados.data);

          $(document).ready(function(){$('[data-toggle="tooltip"]').tooltip();}); // ativando os tooltips
      },
      function (dados) { // callback, falha na conexão do servidor
        mensagemERRO.conexaoServidor(); // informando erro na internet
        return false;
      }
    );

    return true;
  };

  $scope.voltarItens2Contratos = function() {
    $('#plotBusca').css({"display": 'block'}); // colocando a lista de contratos da tela
    $('#plotItens').css({"display": 'none'}); // removendo lista de itens
  };

  $scope.rolarParaBaixo = function() {$("html, body").animate({ scrollTop: $(document).height() });};
  $scope.rolarParaCima = function() {$("html, body").animate({ scrollTop: "0" });};

  $scope.tratarItens = function(itens) {
    itens = copiar(itens);

    $scope.resetarPainelEditarItem(itens);

    $scope.itensTotal = itens.length;
    $scope.itensSomaValor = Number(itens.reduce(function(valorAnterior, valorAtual) {return valorAnterior+(valorAtual.valorBruto-valorAtual.deducoes);}, 0).toFixed(2));

    $scope.itensSomaDivida = Number(itens.reduce(
      function(valorAnterior, valorAtual) {
        var d = new Date();
        var vencimento = new Date(valorAtual.dataVencimento);

        if(d >= vencimento && (valorAtual.dataPagamento == "" || valorAtual.dataPagamento == null))
          return valorAnterior+(valorAtual.valorBruto-valorAtual.deducoes);
        else
          return valorAnterior;
      }, 0).toFixed(2));

    $scope.itensVencidos = itens.reduce(
      function(valorAnterior, valorAtual) {
        var d = new Date();
        var vencimento = new Date(valorAtual.dataVencimento);
        if(d >= vencimento && (valorAtual.dataPagamento == "" || valorAtual.dataPagamento == null)) {
          valorAtual.corVencimento = 'red';
          return valorAnterior+1;
        }
        else {
          valorAtual.corVencimento = 'white';
          return valorAnterior;
        }
      }, 0);

    return itens;
  };

  $scope.resetarPainelEditarItem = function (itens) {
    if(itens === undefined) // Caso não seja passada a lista de itens
      itens = $scope.itensBuscados; // tente pegar da lista já existente

    $scope.itemEditando = {};
    $scope.itemEditando.numero = 0;
    itens.forEach(function (x) {if(x.numero > $scope.itemEditando.numero) $scope.itemEditando.numero = x.numero;});
    $scope.itemEditando.numero++;

    $scope.itemEditando.dataVencimento = new Date();
    $scope.itemEditando.dataPrestacao = new Date();
    $scope.itemEditando.func = 'Criar';
    $scope.itemEditando.Processando = false;
  }

  $scope.editarParcela = function(id) {
    for(var index = 0; index < $scope.itensBuscados.length; index++)
      if($scope.itensBuscados[index].idParcelaContrato == id)
        break;
    if(index >= $scope.itensBuscados.length)
      return false;

    let temp = copiar($scope.itensBuscados[index]);
    temp.dataVencimento = fromData(temp.dataVencimento);
    temp.dataPagamento = fromData(temp.dataPagamento);
    temp.dataPrestacao = fromData(temp.dataPrestacao);

    if(temp.notaFiscal !== null && temp.notaFiscal !== undefined && temp.notaFiscal !== '')// Caso a nota não tenha número definido, então não precisa preencher
      temp.notaFiscal = Number(temp.notaFiscal);
    else // Se a nota fiscal não possuir um valor válido...
      temp.notaFiscal = null; // ...Mantenha o valor vazio(null)

    if(temp.produto.id !== null)
      temp.produto.id = temp.produto.id.toString();

    $scope.itemEditando = temp;
    $scope.itemEditando.func = 'Salvar';

    $scope.rolarParaBaixo();

    return true;
  };

  $scope.consolidarItemEditando = function () {
    if($scope.itemEditando.Processando) return;

    let dataPagamento = toData($scope.itemEditando.dataPagamento);
    if(dataPagamento != null) dataPagamento = dataPagamento.split('-').reverse().join('-');
    else dataPagamento = '';
    let dataPrestacao = toData($scope.itemEditando.dataPrestacao);
    if(dataPrestacao != null) dataPrestacao = dataPrestacao.split('-').reverse().join('-');
    else dataPrestacao = '';

    let dataVencimento = toData($scope.itemEditando.dataVencimento);
    if(dataVencimento != null) dataVencimento = dataVencimento.split('-').reverse().join('-');

    let medidas = '';
    if($scope.itemEditando.medidas !== null && $scope.itemEditando.medidas !== undefined)
      medidas = $scope.itemEditando.medidas;

    let valorAPagar = null;
    if($scope.itemEditando.valorAPagar !== null && $scope.itemEditando.valorAPagar !== undefined)
      valorAPagar = Number($scope.itemEditando.valorAPagar);

    let notaFiscalAPagar = '';
    if($scope.itemEditando.notaFiscalAPagar !== null && $scope.itemEditando.notaFiscalAPagar !== undefined)
      notaFiscalAPagar = $scope.itemEditando.notaFiscalAPagar;

    //O servidor não vai entender um parâmetro nulo, ao invés disso passando como string vazia
    if($scope.itemEditando.notaFiscal == null) $scope.itemEditando.notaFiscal = '';
    let idProduto = null;
    if($scope.itemEditando.produto.id !== null) {
      idProduto = Number($scope.itemEditando.produto.id);
      $scope.cache.listaProdutos.forEach(function(x) {
        if(x.id == idProduto) {
          $scope.itemEditando.produto.nome = x.nome;
        }
      });
    }

    let item = {
      'idContrato': $scope.idContratoDosItens,
      'idProduto': idProduto,
      'produto': $scope.itemEditando.produto,
      'valorBruto': $scope.itemEditando.valorBruto,
      'dataVencimento': dataVencimento,
      'dataPagamento': dataPagamento,
      'dataPrestacao': dataPrestacao,
      'deducoes': $scope.itemEditando.deducoes,
      'valorAPagar': valorAPagar,
      'notaFiscal': $scope.itemEditando.notaFiscal,
      'notaFiscalAPagar': notaFiscalAPagar,
      'observacao': $scope.itemEditando.observacao,
      'medidas': medidas,
      'numero': $scope.itemEditando.numero
    },
    comando = {};
    if($scope.itemEditando.func == 'Criar') {// criar um novo item de contrato
      comando = {
        'comando': 'criar',
        'alvo': 'item',
        'parametros': item
      };
    }
    else { // Atualizar um item existente de contrato
      comando = {
        'comando': 'atualizar',
        'alvo': 'item',
        'id': $scope.itemEditando.idParcelaContrato,
        'parametros': item
      };
      item.idParcelaContrato = $scope.itemEditando.idParcelaContrato;
    }

    $scope.itemEditando.Processando = true;
    $('#ItemEditValorBruto').focus();
    requisitarAPI.post(comando,
        function(dados) { //callback de sucesso
          $scope.resetarPainelEditarItem();
          $scope.rolarParaBaixo();

          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            mensagemERRO.editarItem();
            return false;
          }
          if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
            mensagemERRO.editarItem();
            return false;
          }
          else { // sucesso

            if(comando.comando === 'criar') { // Se o objetivo era inserir um novo item na tabela

              item.idParcelaContrato = dados.data.idItem;
              if(item.dataPagamento !== null && item.dataPagamento !== '') // a data de pagamente não é nula, significa que foi pago
                item.corVencimento = 'white'; // Remover o vermelho de aviso de vencimento, caso exista
              else { // Entrando aqui, significa que o item não está pago
                item.dataPagamento = '';
                //Testando se a data de vencimento já passou
                let dataVencimento = fromData(item.dataVencimento);
                let dataHoje = new Date();
                if(dataHoje >= dataVencimento) // Se o item já venceu
                  item.corVencimento = 'red'; // Indicando o vencimento
                else
                  item.corVencimento = 'white';
              }

              if(item.notaFiscal === undefined || item.notaFiscal === null) item.notaFiscal = '';
              if(item.notaFiscalAPagar === undefined || item.notaFiscalAPagar === null) item.notaFiscalAPagar = '';

              $scope.itensBuscados.push(item);
              $scope.resetarPainelEditarItem();

            }
            else { // Se o objetivo era apenas salvar alterações em um item na tabela

              for(var index = 0; index < $scope.itensBuscados.length; index++)
                if($scope.itensBuscados[index].idParcelaContrato == item.idParcelaContrato)
                  break;
              if(index >= $scope.itensBuscados.length)
                return false;

              if(item.dataPagamento !== null && item.dataPagamento !== '') // a data de pagamente não é nula, significa que foi pago
                item.corVencimento = 'white'; // Remover o vermelho de aviso de vencimento, caso exista
              else { // Entrando aqui, significa que o item não está pago
                item.dataPagamento = '';
                //Testando se a data de vencimento já passou
                let dataVencimento = fromData(item.dataVencimento);
                let dataHoje = new Date();
                if(dataHoje >= dataVencimento) // Se o item já venceu
                  item.corVencimento = 'red'; // Indicando o vencimento
                else
                  item.corVencimento = 'white';
              }

              if(item.notaFiscal === undefined || item.notaFiscal === null) item.notaFiscal = '';
              if(item.notaFiscalAPagar === undefined || item.notaFiscalAPagar === null) item.notaFiscalAPagar = '';

              $scope.itensBuscados[index] = item;

            }
            return true;

          }
        },
        function(dados) { // callback de falha
          $scope.resetarPainelEditarItem();

          mensagemERRO.editarItemConexao();
          return false;
        }
    );
  };

  $scope.itemFoiPagoHoje = function(id) {$scope.itemFoiPagoEm(id, new Date());};
  $scope.itemFoiPagoEm = function(id, data) {
    for(var index = 0; index < $scope.itensBuscados.length; index++)
      if($scope.itensBuscados[index].idParcelaContrato == id)
        break;
    if(index >= $scope.itensBuscados.length)
      return false;

    let itemPago = $scope.itensBuscados[index];

    let dataFormatada = '';
    if(data != null)
      dataFormatada = toData(data).split('-').reverse().join('-'); // Formatando a data passada com o formato aceito pelo servidor //YYYY-MM-DD

    let comando = {
      'comando': 'atualizar',
      'alvo': 'item',
      'id': itemPago.idParcelaContrato,
      'parametros': {'dataPagamento': dataFormatada}
    };

    requisitarAPI.post(comando,
        function(dados) { //callback de sucesso
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            return false;
          }
          if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
            return false;
          }
          else { // sucesso

            itemPago.dataPagamento = dataFormatada; // a atualização funcionou, então aplicando na tabela
            if(dataFormatada !== '') // a data de pagamente não é nula, significa que foi pago
              itemPago.corVencimento = 'white'; // Remover o vermelho de aviso de vencimento, caso exista
            else { // Entrando aqui, significa que o item não está pago
              //Testando se a data de vencimento já passou
              let dataVencimento = fromData(itemPago.dataVencimento);
              let dataHoje = new Date();
              if(dataHoje >= dataVencimento) // Se o item já venceu
                itemPago.corVencimento = 'red'; // Indicando o vencimento
            }
            return true;

          }
        },
        function(dados) { // callback de falha
          return false;
        }
    );

  };

  $scope.editarDataPagamentoItem = function(id) {
    $('#tela_setar_dataPagamento').modal({keyboard: false});
    $scope.idFromTelaSetarDataPagamento = id; // Guardando o id do item que deve ter sua data de pagamento alterada
  };

  $scope.painelNotaFiscal = function() {$('#tela_setar_notaFical').modal({keyboard: false});};

  $scope.notaFiscalPreConfigurada = false;
  $scope.registrarNotaFical = function() {
    $scope.notaFiscalConfigurada = {};
    $scope.notaFiscalConfigurada.vencimento = toData($scope.dataFromTelaSetarNotaFiscal);
    $scope.notaFiscalConfigurada.numero = $scope.numeroFromTelaSetarNotaFiscal;
    $scope.notaFiscalPreConfigurada = true;
  };

  $scope.setNotaFicalConfigurada = function(id) {
    if(!$scope.notaFiscalPreConfigurada) return false;

    for(var index = 0; index < $scope.itensBuscados.length; index++)
      if($scope.itensBuscados[index].idParcelaContrato == id)
        break;
    if(index >= $scope.itensBuscados.length)
      return false;

    let item = $scope.itensBuscados[index];

    let dataFormatada = null;
    if($scope.notaFiscalConfigurada.vencimento != null)
      dataFormatada = $scope.notaFiscalConfigurada.vencimento.split('-').reverse().join('-'); // Formatando a data passada com o formato aceito pelo servidor //YYYY-MM-DD

    if($scope.notaFiscalConfigurada.numero === undefined)
      $scope.notaFiscalConfigurada.numero = '';

    let comando = {
      'comando': 'atualizar',
      'alvo': 'item',
      'id': item.idParcelaContrato,
      'parametros': {
        'notaFiscal': $scope.notaFiscalConfigurada.numero,
        'dataVencimento': dataFormatada
      }
    };

    requisitarAPI.post(comando,
        function(dados) { //callback de sucesso
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            return false;
          }
          if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
            return false;
          }
          else { // sucesso

            if(item.dataPagamento !== null && item.dataPagamento !== '') // a data de pagamente não é nula, significa que foi pago
              item.corVencimento = 'white'; // Remover o vermelho de aviso de vencimento, caso exista
            else if(dataFormatada != null){ // Entrando aqui, significa que o item não está pago
              //Testando se a data de vencimento já passou
              let dataVencimento = fromData(dataFormatada);
              let dataHoje = new Date();
              if(dataHoje >= dataVencimento) // Se o item já venceu
                item.corVencimento = 'red'; // Indicando o vencimento
              else
                item.corVencimento = 'white'; // Indicando o vencimento
            }
            item.notaFiscal = $scope.notaFiscalConfigurada.numero;
            if(dataFormatada != null)
              item.dataVencimento = dataFormatada;
            return true;

          }
        },
        function(dados) { // callback de falha
          return false;
        }
    );
  };

  $scope.deletandoItem = false;
  $scope.deletarParcela = function(id) {
    if($scope.deletandoItem) return false;
    $scope.deletandoItem = true;

    $('[data-toggle="tooltip"]').tooltip('hide'); // Escondendo o tooltip. Se o elemento for deletado vai bugar se o tooltip estiver na tela
    if(!confirm("Tem certeza que quer deletar essa parcela?")) return false;

    let backup = copiar($scope.itensBuscados);//caso falhe
    let novo = [];
    let i = 0, j = 0;

    for(;j < backup.length; j++) {
      if(backup[j].idParcelaContrato != id) {
        novo[i] = copiar(backup[j]);
      }
      else {
        j++;
        if(j < backup.length) novo[i] = copiar(backup[j]);
      }
      i++;
    }

    var comando = {
      'comando': 'deletar',
      'alvo': 'item',
      'id': id
    };

    requisitarAPI.post(comando,
        function(dados) { //callback de sucesso
          $scope.deletandoItem = false;
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            return false;
          }
          if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
            return false;
          }
          else {$scope.itensBuscados = copiar(novo);return true;} // sucesso ao deletar o item
        },
        function(dados) { // callback de falha
          $scope.deletandoItem = false;
          return false;
        }
    );
  };

 $scope.erroFatal = function(msg = '') {
  $('#telaErroFatal').css('display', 'block');
  $('#telaErroFatalMensagem').text(msg);
 };

}); // FIM DO CÓDIGO ANGULAR

function copiar(objeto) {return JSON.parse(JSON.stringify(objeto));}

function fromData(data) {
  if(data === null || data === undefined || data === '' || typeof data !== 'string') return null;

  data = data.split('-');
  if(data.length < 3) return null;
  return new Date(data[0], data[1] - 1, data[2]);
}
function toData(data) {
  if(data === undefined ||  data === null || data === '') return null;

  if(typeof data === 'string') { // significa que é um objeto Date na forma string
    data = data.split('T')[0];
    data = fromData(data);
  }

  return data.getDate().toString()+
        '-'+(data.getMonth()+1).toString()+
        '-'+data.getFullYear().toString();
}

/*
  Essa função quando recebe 1 mostra a subtela de busca de contratos
  quando recebe 2 mostra a subtela de criação de contratos
*/
function trocarTelaContratos(qual) {
  if(qual === 1) {
    $("#bnt_tela_criacao").removeClass("active");
    $("#bnt_tela_busca").addClass("active");

    $('#tela_busca').css('display', 'block');
    $('#tela_criacao').css('display', 'none');
  } else if(qual === 2){
    $("#bnt_tela_busca").removeClass("active");
    $("#bnt_tela_criacao").addClass("active");

    $('#tela_criacao').css('display', 'block');
    $('#tela_busca').css('display', 'none');
  }
}
