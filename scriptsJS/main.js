  var app = angular.module("App", ["ngRoute"]);

  app.config(function($routeProvider) {
      $routeProvider
      .when("/", {
          templateUrl : "rotas/vazio.html"
      })
      .when("/contrato", {
        templateUrl: 'rotas/contrato.html'
      })
      .when("/cliente", {
          templateUrl : "rotas/cliente.html"
      })
      .when("/produto", {
          templateUrl : "rotas/produto.html"
      })
      .when("/subproduto", {
          templateUrl : "rotas/subproduto.html"
      })
      .when("/funcionario", {
          templateUrl : "rotas/funcionario.html"
      })
      .when("/mensageiro", {
          templateUrl : "rotas/mensageiro.html"
      })
      .when("/tipoacesso", {
          templateUrl : "rotas/tipoacesso.html"
      })
      .when("/relatorioEmails", {
          templateUrl : "rotas/relatorioEmails.html"
      })
      .when("/relatorioClientes", {
          templateUrl : "rotas/relatorioClientes.html"
      })
      .when("/relatorioClientesAtrasados", {
          templateUrl : "rotas/relatorioClientesAtrasados.html"
      })
      .when("/relatorioClientesAtivos", {
          templateUrl : "rotas/relatorioClientesAtivos.html"
      })
      .when("/relatorioClientesInativos", {
          templateUrl : "rotas/relatorioClientesInativos.html"
      })
      .when("/relatorioGraficos", {
          templateUrl : "rotas/relatorioGraficos.html"
      });
  });

app.service('requisitarAPI', function($http) {
  this.url = 'api.php';

  this.post = function (dados, sucesso, falha) {
    $http.post(this.url, JSON.stringify(dados), {'headers': {'Content-Type': 'application/json;charset=utf-8;'}})
      .then(
          function (response) {
            sucesso(response);
          },
          function (response) {
            falha(response);
          });
  };

});
app.service('relatorio', function() {
  this.imprimir = function (i) {
   var cssEstilos = '';
   var imp = window.open('', 'div', 'width='+window.innerWidth+',height='+window.innerWidth);

   var cSs = document.querySelectorAll("link[rel='stylesheet']");
   for(x=0;x<cSs.length;x++){
      cssEstilos += '<link rel="stylesheet" href="'+cSs[x].href+'">';
   }

   imp.document.write('<html><head><title>SEC Publicidade</title>');
   imp.document.write(cssEstilos+'</head><body>');
   imp.document.write(document.getElementById(i).innerHTML);
   imp.document.write('</body></html>');

   setTimeout(function(){
      imp.print();
      imp.close();
   },500);
  };

});

app.filter('dinheiro', function() {
    return function(valor) {
        if(valor === undefined || valor === null) return '';
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
          else if(dpsVirgula.length > 2) // Não faz sentido a casa dos centavos ter tês ou mais dígitos
            dpsVirgula = dpsVirgula[0] + dpsVirgula[1];
          final += ',' + dpsVirgula;
        }
        else final += ',00';
        
        if(ehNegativo)
          return 'R$-'+final;
        return 'R$'+final;
    };
});

app.filter('dataBR', function() {
    return function(valor) {
        if(valor === undefined || valor === null || typeof valor != 'string') return '';
        let vetor  = valor.split('-'); // separando os elementos
        let doisDigitos = vetor.map(function (x) { // aplicando um zero a esquerda dos elementos que só tiverem um digito
          if(x.length == 1)
            return '0'+x;
          else return x;
        });
        return doisDigitos.reverse().join('/');
    };
});