app.controller("GraficosCtrl", function($scope, requisitarAPI, relatorio) {

 // Carregando a APIdo Google
  google.charts.load('upcoming', {'packages':['corechart', 'line']});

  // QUando a API estiver carregada executar o callback
  google.charts.setOnLoadCallback(plotarGraficos);


  function plotarGraficos() {
    /*
      * Nota: todas as funções abaixo são assincronas
      * Primeiro parâmetro é qual os dados devem ser solicitados a API (string)
      * Segundo parâmetro é o callback que recebe os dados retornados da API (function)
    */
    requisitarDados('servicosFinalizadosUltimos12Meses', plotarServicos);

  };

  /*
    Funções para executar o plot de cada grafico
  */

  function plotarServicos(dadosBrutos) {

    var dados = new google.visualization.DataTable();
    dados.addColumn('string', 'Mês');
    dados.addColumn('number', 'Número de serviços prestados');

    let chaves = Object.keys(dadosBrutos.nServicos);
    let valores = Object.values(dadosBrutos.nServicos);

    for(let x = 1; x <= chaves.length; x++)
      dados.addRows([[chaves[x-1], valores[x-1]]]);

    var options = {
      title: 'Serviços finalizados nos últimos 12 meses',
      width: 900,
      height: 400,
      colors: ['blue']
    };

    plotarGraficoLinhas('plotServicos', dados, options);
    plotarServicosValores(dadosBrutos);

  };

  function plotarServicosValores(dadosBrutos) {

    var dados = new google.visualization.DataTable();
    dados.addColumn('string', 'Mês');
    dados.addColumn('number', 'Valores recebidos pelos serviços');

    let chaves = Object.keys(dadosBrutos.valorServicos);
    let valores = Object.values(dadosBrutos.valorServicos);

    for(let x = 1; x <= chaves.length; x++)
      dados.addRows([[chaves[x-1], valores[x-1]]]);

    var options = {
      title: 'Valores recebidos nos últimos 12 meses',
      width: 900,
      height: 400,
      colors: ['red']
    };

    plotarGraficoLinhas('plotServicosValores', dados, options);

  };

  /*
    Outras funções
  */

  function requisitarDados(qual, callback) {
    var comando = {
      'comando': 'relatorio',
      'alvo': qual
    };

      requisitarAPI.post(comando,
        function (retorno) { // callback, sucesso na conexão com o servidor
          if(retorno.status !== 200 || typeof retorno.data === 'string' || retorno.data.erro !== undefined) { // erro desconhecido do servidor
            $scope.exibirErro('mensagemErroConexao');
          }
          if(retorno.data.status === 'falha') {
            $scope.exibirErro('mensagemErroDesconhecido');
          }
          callback(retorno.data);
        },
        function (dados) { // callback, falha na conexão do servido
          $scope.exibirErro('mensagemErroDesconhecido');
        }
    );
  };

  function plotarGraficoLinhas(idDIV, dados, options) {
      var chart = new google.charts.Line(document.getElementById(idDIV));

      chart.draw(dados, google.charts.Line.convertOptions(options));
  };


$scope.exibirErro = function(elemento) {
  document.getElementById(elemento).style.display = 'block';
    setTimeout(function(){document.getElementById(elemento).style.display = 'none';}, 5000);
}

$scope.imprimir = function() {relatorio.imprimir('plotDados');};

}); // FIM DO CÓDIGO ANGULAR
