app.controller("ClienteCtrl", function($scope, requisitarAPI) {

/*
  Essa função tem a unica utilidade de armazenar informações que vieram do servidor em cache
*/
$scope.controle_tempo_busca = 0; // guarda quando a ultima consulta foi realizada em millisegundos
$scope.cache = {};
$scope.clientesBuscados = [];
$scope.inicializarCache = function() {
  if($scope.cache.listaUFs === undefined) {
    var comando = {
      'comando': 'listar',
      'alvo': 'UF'
    };
    requisitarAPI.post(comando,
        function (dados) { // callback, sucesso na conexão com o servidor
          if(dados.status !== 200 || typeof dados.data === 'string' || dados.data.erro !== undefined) { // erro desconhecido do servidor
            if(dados.data.erro != undefined)
              $scope.erroFatal(dados.data.erro);
            return false;
          }
          $scope.cache.listaUFs = dados.data;
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
  #########SUBSISTEMA BUSCA DE CLIENTES#############
__________________________________________________________*/
  $scope.clienteSelecionado = {};
  $scope.estadoBuscando = false;
  $scope.limparCamposBusca = function() {
    $scope.busca_nome_cliente = null;
  };

	$scope.submeterBusca = function() {
    if($scope.estadoBuscando) return false;
    var d = new Date();
    if((d.getTime()-$scope.controle_tempo_busca) < 1000 || $scope.busca_nome_cliente === null || $scope.busca_nome_cliente === undefined || $scope.busca_nome_cliente === '') return false;
    $scope.controle_tempo_busca = d.getTime();
    $scope.avisarBuscando();

    var comando = {
      'comando': 'buscar',
      'parametros': {
        'alvo': 'cliente',
        'nome_cliente': $scope.busca_nome_cliente
      }
    };

    var retorno = null;

		requisitarAPI.post(comando,
      function (dados) { // callback, sucesso na conexão com o servidor
        $scope.avisarBuscando(false);
          if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
            $scope.exibirErro('mensagemErroConsultar'); // informando erro no servidor
            return false;
          }
          if(dados.data.erro !== undefined) { // algum erro conhecido detectado pelo servidor
            if(dados.data.erro == 404) // erro comum: não encontrou o contrato
              $scope.exibirErro('mensagemErro404');
            else // algum outro erro
              $scope.exibirErro('mensagemErroConsultar'); // informando erro no servidor
            return false;
          }    
          $scope.processarBusca(dados.data);
      },
      function (dados) { // callback, falha na conexão do servidor
        $scope.exibirErro('mensagemErroConexao'); // informando erro no servidor
        $scope.avisarBuscando(false);
        return false;
      }
    );

    return true;
	};

  $scope.processarBusca = function(dados) {
    if(dados.status !== 'sucesso') {
      $scope.exibirErro('mensagemErroConsultar'); // informando erro no servidor
      return false;
    }
    if(dados.dados.length <= 0) {
      $scope.exibirErro('mensagemErro404'); // informando erro no servidor
      return false;
    }

    $('#plotBuscaClientes').css("display", "block");
    $scope.clientesBuscados = dados.dados;
    $scope.limparCamposBusca();
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
    }
    else
      $('#bntBuscarCliente').html("Buscar <span class=\"fa fa-search\" aria-hidden=\"true\"></span>");
  }

$scope.exibirErro = function(elemento) {
  document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

/*_______________________________________________________________
  ######### SUBSISTEMA EDIÇÃO DE CONTRATOS #############
_________________________________________________________________*/
 $scope.editarCliente = function(id) {
   var cliente = $scope.buscarClienteID(id);
   if(cliente === false) return false;

   $('#tela_edicao').modal({keyboard: false});
   $scope.setCliente(cliente);
 };

$scope.deletarCliente = function() {

  if(!confirm("Tem certeza que quer deletar esse cliente?")) return false;

  var comando = {
      'comando': 'deletar',
      'alvo': 'cliente',
      'id': $scope.clienteSelecionado.idCliente
    };

  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
          $scope.exibirErro('telaEdicao_mensagemErroDesconhecido');
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
          $scope.exibirErro('telaEdicao_mensagemDeletar');
          return false;
        }
        else { // sucesso ao deletar o contrato
          $('#tela_edicao').modal('hide');

          var temp = [];
          for(x = 0; x < $scope.clientesBuscados.length; x++) // buscando qual foi o contrato editado na lista de contratos
            if($scope.clientesBuscados[x].idCliente !== $scope.clienteSelecionado.idCliente)
              temp.push($scope.clientesBuscados[x]);
          $scope.clientesBuscados = temp;

          return true;
        }
      },
      function(dados) { // callback de falha
        $scope.exibirErro('telaEdicao_mensagemConexao');
        return false;
      }
  );
};

$scope.atualizarCliente = function() {
  if($scope.estadoAtualizando) return false;
  $scope.avisarEditando(true);

  $listaDeModificacoes = $scope.listarModificacoesCliente();

  var comando = {
    'comando': 'atualizar',
    'alvo': 'cliente',
    'parametros': {
      'id': $scope.clienteSelecionado.idCliente,
      'parametros': $listaDeModificacoes
    }
  };

  requisitarAPI.post(comando,
  function(dados) { //callback de sucesso
    $scope.avisarEditando(false);
    if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
      $scope.exibirErro('telaEdicao_mensagemErroDesconhecido');
      return false;
    }
    if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
      $scope.exibirErro('telaEdicao_mensagemAtualizar');
      return false;
    }
    else { // sucesso ao aplicar as modificações
      $scope.exibirErro('telaEdicao_mensagemSucesso');

      var index = 0;
      for(x = 0; x < $scope.clientesBuscados.length; x++) // buscando qual foi o contrato editado na lista de contratos
        if($scope.clientesBuscados[x].idCliente === $scope.clienteSelecionado.idCliente)
          index = x;

      $scope.clientesBuscados[index] = copiar($scope.clienteSelecionado); // Atualizando o contrato na lista

      $scope.clienteSelecionadoBACKUP = copiar($scope.clienteSelecionado); // refazendo o backup para detectar novas modificações

      return true;
    }
  },
  function(dados) { // callback de falha
    $scope.exibirErro('telaEdicao_mensagemConexao');
    $scope.avisarEditando(false);
    return false;
  }
  );
};

/*
  Essa função detecta todas as modificações feitas pelo uduário no contrato e as lista em em vetor
*/
$scope.listarModificacoesCliente = function() {
  var modificacoes = [];

  $scope.clienteSelecionado.UF = $("#telaEdicao_UF").val();
  if($scope.clienteSelecionado.UF !== $scope.clienteSelecionadoBACKUP.UF)
    modificacoes.push({'UF': $scope.clienteSelecionado.UF});

  if($scope.clienteSelecionado.ativo !== $scope.clienteSelecionadoBACKUP.ativo)
    modificacoes.push({'ativo': $scope.clienteSelecionado.ativo});

  if($scope.clienteSelecionado.nomeCliente !== $scope.clienteSelecionadoBACKUP.nomeCliente)
    modificacoes.push({'nomeCliente': $scope.clienteSelecionado.nomeCliente});

  if($scope.clienteSelecionado.logradouro !== $scope.clienteSelecionadoBACKUP.logradouro)
    modificacoes.push({'logradouro': $scope.clienteSelecionado.logradouro});

  if($scope.clienteSelecionado.bairro !== $scope.clienteSelecionadoBACKUP.bairro)
    modificacoes.push({'bairro': $scope.clienteSelecionado.bairro});

  if($scope.clienteSelecionado.cep !== $scope.clienteSelecionadoBACKUP.cep)
    modificacoes.push({'cep': $scope.clienteSelecionado.cep});

  if($scope.clienteSelecionado.cidade !== $scope.clienteSelecionadoBACKUP.cidade)
    modificacoes.push({'cidade': $scope.clienteSelecionado.cidade});

  if($scope.clienteSelecionado.celular !== $scope.clienteSelecionadoBACKUP.celular)
    modificacoes.push({'celular': $scope.clienteSelecionado.celular});

  if($scope.clienteSelecionado.telefone !== $scope.clienteSelecionadoBACKUP.telefone)
    modificacoes.push({'telefone': $scope.clienteSelecionado.telefone});

  if($scope.clienteSelecionado.fax !== $scope.clienteSelecionadoBACKUP.fax)
    modificacoes.push({'fax': $scope.clienteSelecionado.fax});

  if($scope.clienteSelecionado.email !== $scope.clienteSelecionadoBACKUP.email)
    modificacoes.push({'email': $scope.clienteSelecionado.email});

  if($scope.clienteSelecionado.homepage !== $scope.clienteSelecionadoBACKUP.homepage)
    modificacoes.push({'homepage': $scope.clienteSelecionado.homepage});

  if($scope.clienteSelecionado.titular !== $scope.clienteSelecionadoBACKUP.titular)
    modificacoes.push({'titular': $scope.clienteSelecionado.titular});

  if($scope.clienteSelecionado.contato !== $scope.clienteSelecionadoBACKUP.contato)
    modificacoes.push({'contato': $scope.clienteSelecionado.contato});

  if($scope.clienteSelecionado.cgcCpf !== $scope.clienteSelecionadoBACKUP.cgcCpf)
    modificacoes.push({'cgcCpf': $scope.clienteSelecionado.cgcCpf});

  if($scope.clienteSelecionado.inscEstadual !== $scope.clienteSelecionadoBACKUP.inscEstadual)
    modificacoes.push({'inscEstadual': $scope.clienteSelecionado.inscEstadual});

  if($scope.clienteSelecionado.inscMunicipal !== $scope.clienteSelecionadoBACKUP.inscMunicipal)
    modificacoes.push({'inscMunicipal': $scope.clienteSelecionado.inscMunicipal});

  if($scope.clienteSelecionado.titularCpf !== $scope.clienteSelecionadoBACKUP.titularCpf)
    modificacoes.push({'titularCpf': $scope.clienteSelecionado.titularCpf});

  if($scope.clienteSelecionado.complemento !== $scope.clienteSelecionadoBACKUP.complemento)
    modificacoes.push({'complemento': $scope.clienteSelecionado.complemento});

  if($scope.clienteSelecionado.observacao !== $scope.clienteSelecionadoBACKUP.observacao)
    modificacoes.push({'observacao': $scope.clienteSelecionado.observacao});

  return modificacoes;
};

 $scope.buscarClienteID = function(id) {
  for(x = 0; x < $scope.clientesBuscados.length; x++)
    if($scope.clientesBuscados[x].idCliente === id)
      return $scope.clientesBuscados[x];
  return false;
 };

 $scope.setCliente = function(cliente) {
  $scope.clienteSelecionado = copiar(cliente);
  $scope.clienteSelecionadoBACKUP = copiar(cliente);
  $("#telaEdicao_UF").val($scope.clienteSelecionado.UF);
 };

  /*
    Avisa ao usuário que o sistema está realizando a busca
    estado representa se a busca está ocorrendo(true) ou não(false)
  */
  $scope.estadoAtualizando = false;
  $scope.avisarEditando = function(estado = true) {
    $scope.estadoAtualizando = estado;
    if(estado)
      $('#bnt_atualizar_cliente').html("Atualizando... <span class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></span>");
    else
      $('#bnt_atualizar_cliente').html("Atualizar");
  };

/*
_________________________________________________________________
  ######### SUBSISTEMA CRIAÇÃO DE CLIENTES #############
_________________________________________________________________
*/

$scope.clienteCriando = {'ativo': true}; // valor default para os dados do cliente sendo criado
$scope.criarCliente = function() {

  $scope.clienteCriando.UF = $("#telaCriacao_UF").val();

  var comando = {
      'comando': 'criar',
      'alvo': 'cliente',
      'parametros': $scope.clienteCriando
    };

  $scope.avisarCriando(true);
  requisitarAPI.post(comando,
      function(dados) { //callback de sucesso
        $scope.avisarCriando(false);
        if(dados.status !== 200 || typeof dados.data === 'string') { // erro desconhecido do servidor
          $scope.exibirErro('telaCriacao_mensagemErroDesconhecido'); // informando erro no servidor
          return false;
        }
        if(dados.data.erro !== undefined || dados.data.status === 'falha') { // algum erro conhecido detectado pelo servidor
           $scope.exibirErro('telaCriacao_mensagemCriar');
          return false;
        }
        else { // sucesso ao criar o contrato
          document.getElementById('telaCriacao_mensagemSucesso').style.display = 'block'; // mostrando mensagem de sucesso
          setTimeout(function(){document.getElementById('telaCriacao_mensagemSucesso').style.display = 'none';}, 5000); // remover mensagem após 5 segundos

          $scope.clienteCriando = {'ativo': true};

          return true;
        }
      },
      function(dados) { // callback de falha
        $scope.exibirErro('telaCriacao_mensagemConexao'); // informando erro no servidor
        $scope.avisarCriando(false);
        return false;
      }
  );
};

  /*
    Avisa ao usuário que o sistema está realizando a busca
    estado representa se a busca está ocorrendo(true) ou não(false)
  */
  $scope.estadoCriando = false;
  $scope.avisarCriando = function(estado = true) {
    $scope.estadoCriando = estado;
    if(estado)
      $('#bnt_criar_cliente').html("Criando... <span class=\"fa fa-hourglass-half\" aria-hidden=\"true\"></span>");
    else
      $('#bnt_criar_cliente').html("Criar");
  };

 $scope.erroFatal = function(msg = '') {
  $('#telaErroFatal').css('display', 'block');
  $('#telaErroFatalMensagem').text(msg);
 };

}); // FIM DO CÓDIGO ANGULAR

function copiar(objeto) {return JSON.parse(JSON.stringify(objeto));}

/*
  Essa função quando recebe 1 mostra a subtela de busca de contratos
  quando recebe 2 mostra a subtela de criação de contratos
*/
function trocarTelaClientes(qual) {
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
