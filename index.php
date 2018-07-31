<?php
include_once('../config.php');
session_start();

if($_SESSION['logado'] !== 'S' && Config::REQUISITAR_LOGIN) { // Se não estiver logado e for nesscesário estar
  header('location: login.php'); // Redirecione para a tela de login
  exit(0);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Sistema SEC Publicidade</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
  if(Config::USAR_CDN) {
    echo '
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-route.js"></script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  ';
  } else {
    echo '
  <link rel="stylesheet" href="Terceiros/CSS/bootstrap.min.css">
  <link rel="stylesheet" href="Terceiros/CSS/font-awesome.min.css">

  <script src="Terceiros/JS/angular.min.js"></script>
  <script src="Terceiros/JS/angular-route.js"></script>

  <script src="Terceiros/JS/jquery-3.3.1.min.js"></script>
  <script src="Terceiros/JS/popper.min.js"></script>
  <script src="Terceiros/JS/bootstrap.min.js"></script>
  ';
}
?>
  <!-- Este Script deve vir do CDN do Google -->
  <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body ng-app="App">

<nav class="navbar navbar-expand-sm navbar-toggleable-md bg-dark navbar-dark">

  <a class="navbar-brand" href="#" style="margin: 0;">
    <img src="imgs/logo.png" id="img_logo" alt="logo" style="width:40px; float: left;">
  </a>
  <h4 style="color: white;">Sistema SEC <small class="text-muted" style="font-size: 7pt;"><?php echo Config::VERCAO;?></small></h4>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a href="configuracao.php" class="nav-link">Configurações</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Sair <span class="fa fa-sign-out" aria-hidden="true"></span></a>
        </li>
    </ul>
  </div>  
</nav>

<br>

<div class="container-fluid" style="max-width: 1700px;">
	<div class="row">

		<div class="col-md-2">
      <div class="list-group">
       <b class="list-group-item list-group-item-light" data-toggle="collapse" data-target="#painelOperacoes">Operações</b>
        <div id="painelOperacoes">
          <a href="#!contrato" class="list-group-item list-group-item-dark">Contrato</a>
          <a href="#!cliente" class="list-group-item list-group-item-dark">Cliente</a>
          <a href="#!APagar" class="list-group-item list-group-item-dark">Publicações a pagar</a>
          <a href="#!produto" class="list-group-item list-group-item-dark">Serviço</a>
          <a href="#!subproduto" class="list-group-item list-group-item-dark">Sub-Serviço</a>
          <a href="#!funcionario" class="list-group-item list-group-item-dark">Funcionário</a>
          <a href="#!mensageiro" class="list-group-item list-group-item-dark">Mensageiro</a>
          <a href="#!tipoacesso" class="list-group-item list-group-item-dark">Tipo de Acesso</a>
        </div>
      </div>
      <hr>
      <div class="list-group">
        <b class="list-group-item list-group-item-light" data-toggle="collapse" data-target="#painelRelatorios">Relatórios</b>
        <div id="painelRelatorios">
          <a href="#!relatorioAPagar" class="list-group-item list-group-item-dark">A Pagar</a>
          <a href="#!relatorioGraficos" class="list-group-item list-group-item-dark">Gráficos</a>
          <a href="#!relatorioClientes" class="list-group-item list-group-item-dark">Clientes</a>
          <a href="#!relatorioNotaFiscal" class="list-group-item list-group-item-dark">Nota Fiscal</a>
          <a href="#!relatorioClientesAtrasados" class="list-group-item list-group-item-dark">Clientes atrasados</a>
          <a href="#!relatorioClientesAtivos" class="list-group-item list-group-item-dark">Clientes ativos</a>
          <a href="#!relatorioClientesInativos" class="list-group-item list-group-item-dark">Clientes inativos</a>
          <a href="#!relatorioEmails" class="list-group-item list-group-item-dark">Emails</a>
        </div>
      </div>
      <hr>
    </div>

		<div class="col-md-10">
      <div ng-view></div>
    </div>

	</div>
</div>

<script type="text/javascript">
  //Este script contrala o collapse do menu lateral
  function inicializarMenuLateral() {
    if($(window).width() < 768) {
      $("#painelOperacoes").addClass("collapse");
      $("#painelRelatorios").addClass("collapse");
    }
  }
  inicializarMenuLateral();
</script>

<script type="text/javascript" src="scriptsJS/main.js"></script>
<script type="text/javascript" src="scriptsJS/contrato.js"></script>
<script type="text/javascript" src="scriptsJS/funcionario.js"></script>
<script type="text/javascript" src="scriptsJS/mensageiro.js"></script>
<script type="text/javascript" src="scriptsJS/tipoacesso.js"></script>
<script type="text/javascript" src="scriptsJS/produto.js"></script>
<script type="text/javascript" src="scriptsJS/subproduto.js"></script>
<script type="text/javascript" src="scriptsJS/cliente.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioEmails.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioClientes.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioClientesAtrasados.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioClientesAtivos.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioClientesInativos.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioGraficos.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioNotaFiscal.js"></script>
<script type="text/javascript" src="scriptsJS/relatorioAPagar.js"></script>

</body>
</html>