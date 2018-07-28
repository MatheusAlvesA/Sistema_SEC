<?php
include_once('../config.php');
require_once('vendor/autoload.php');
use Sistema\Logger;

session_start();

if($_SESSION['logado'] !== 'S' && Config::REQUISITAR_LOGIN) { // Se não estiver logado e for nescessário estar
  header('location: login.php'); // Redirecione para a tela de login
  exit(0);
}

if($_GET['limparLog'])
  Logger::limpar();

if($_GET['baixarBanco'])
  baixarBanco();


if($_GET['uparBanco'])
  uparBanco();


if($_POST['senha'])
  atualizarSenha($_POST['senha']);
elseif($_POST['server'])
  atualizarGeral($_POST['server'], $_POST['dbName'], $_POST['user'], $_POST['dbPass'], $_POST['CORS'], $_POST['Erros'], $_POST['CDN'], $_POST['RLogin']);


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

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  ';
  } else {
    echo '
  <link rel="stylesheet" href="Terceiros/CSS/bootstrap.min.css">
  <link rel="stylesheet" href="Terceiros/CSS/font-awesome.min.css">

  <script src="Terceiros/JS/jquery-3.3.1.min.js"></script>
  <script src="Terceiros/JS/popper.min.js"></script>
  <script src="Terceiros/JS/bootstrap.min.js"></script>
  ';
}
?>
</head>

<body ng-app="App">

<nav class="navbar navbar-expand-sm navbar-toggleable-md bg-dark navbar-dark">

  <a class="navbar-brand" href="index.php" style="margin: 0;">
    <img src="imgs/logo.png" id="img_logo" alt="logo" style="width:40px; float: left;">
    <h4 style="color: white;">Sistema SEC <small class="text-muted" style="font-size: 7pt;"><?php echo Config::VERCAO;?></small></h4>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a href="#" class="nav-link">Configurações</a>
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

		<div class="col-md-10 offset-md-1">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active" href="#" onclick="alterarForm('geral')" id="linkGeral">Configurações</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="alterarForm('senha')" id="linkSenha">Senha</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="alterarForm('log')" id="linkLog">Log de Erros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" onclick="alterarForm('importExport')" id="linkImportExport">Importar/Exportar Banco</a>
        </li>
      </ul>
      <br>
      <form action="configuracao.php" method="post" id="formGeral" style="display: block;">
        <div class="form-group row">
          <label for="telaCriacao_nome_cliente" class="col-3 col-form-label">Servidor do banco:</label>
          <div class="col-5">
            <input type="text" class="form-control" placeholder="IP ou nome do servidor" name="server" value="<?php echo Config::SQL_host;?>">
          </div>
          <div class="col-4">
            <input type="text" class="form-control" placeholder="Nome do banco" name="dbName" value="<?php echo Config::SQL_db;?>">
          </div>
        </div>
        <div class="form-group row">
          <label for="telaCriacao_nome_cliente" class="col-3 col-form-label">Usuário e senha do banco:</label>
          <div class="col-5">
            <input type="text" class="form-control" placeholder="Usuário" name="user" value="<?php echo Config::SQL_user;?>">
          </div>
          <div class="col-4">
            <input type="password" class="form-control" placeholder="Senha" name="dbPass" value="<?php echo Config::SQL_pass;?>">
          </div>
        </div>
        <div class="form-group row">
          <div class="col-sm-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="customCheck1" name="CORS" <?php if(Config::CORS) echo 'checked';?>>
              <label class="custom-control-label" for="customCheck1">Permitir CORS</label>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="customCheck2" name="CDN" <?php if(Config::USAR_CDN) echo 'checked';?>>
              <label class="custom-control-label" for="customCheck2">Usar CDN</label>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="customCheck3" name="Erros" <?php if(Config::EXIBIR_ERROS) echo 'checked';?>>
              <label class="custom-control-label" for="customCheck3">Exibir mensagens de erros</label>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="customCheck4" name="RLogin" <?php if(Config::REQUISITAR_LOGIN) echo 'checked';?>>
              <label class="custom-control-label" for="customCheck4">Exigir senha para entrar</label>
            </div>
          </div>
        </div>

        <hr>

        <div class="form-group row">
          <div class="col-sm-2 offset-sm-10">
            <button type="submit" class="btn btn-primary" style="cursor: pointer;">Atualizar</button>
          </div>
        </div>
      </form>

      <form action="configuracao.php" method="post" id="formSenha" style="display: none;">
        <div class="form-group row">
          <label class="col-3 col-form-label">Nova senha do sistema:</label>
          <div class="col-5">
            <input type="password" class="form-control" placeholder="Senha" name="senha">
          </div>
        </div>

        <hr>

        <div class="form-group row">
          <div class="col-sm-2 offset-sm-10">
            <button type="submit" class="btn btn-primary" style="cursor: pointer;">Atualizar</button>
          </div>
        </div>
      </form>

      <form action="configuracao.php?limparLog=1" method="post" id="formLog" style="display: none;">
        <div class="form-group">
          <label for="comment">Log de erros:</label>
          <textarea class="form-control" rows="20" readonly>
<?php
$erros = @file_get_contents(Logger::arquivoLog);
if($erros) echo $erros;
else echo 'Nenhum erro registrado';
?>
          </textarea>
        </div>

        <hr>

        <div class="form-group row">
          <div class="col-sm-2 offset-sm-10">
            <button type="submit" class="btn btn-primary" style="cursor: pointer;">Limpar</button>
          </div>
        </div>
      </form>

      <div id="importExport" style="display: none;">
      <?php 
        if($_GET['upBanco'] == 1) echo '<div class="alert alert-success"><strong>Sucesso!</strong> Banco restaurado</div>';
        if($_GET['upBanco'] == 2) echo '<div class="alert alert-danger"><strong>Erro!</strong> Falha ao importar</div>';
        if(!ehLinux()) echo '<div class="alert alert-danger"><strong>Erro!</strong> Esta função só é compatível com servidores que usem Linux</div>';
      ?>
      <form action="configuracao.php?uparBanco=1" method="post" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="arquivoBanco">Restaurar banco de dados</label>
            <input type="file" class="form-control-file" name="arquivoBanco" />
          </div>

          <hr>

          <div class="form-group row">
            <div class="col-sm-2 offset-sm-10">
              <button type="submit" class="btn btn-primary" style="cursor: pointer;">Restaurar</button>
            </div>
          </div>
      </form>
      <div class="row">
          <div class="col-sm-2">
              <a href="configuracao.php?baixarBanco=1"><button class="btn btn-success">Baixar</button></a>
          </div>
      </div>
    </div>

    </div>

	</div>
</div>

</body>
<script type="text/javascript">
  function alterarForm(qual) {
    switch (qual) {
      case 'geral':
        formGeral();
        break;
      case 'senha':
        formSenha();
        break;
      case 'log':
        formLog();
        break;
      case 'importExport':
        formImportExport();
        break;
    }
  }

  function formGeral() {
    $('#formGeral').css("display", "block");
    $('#formSenha').css("display", "none");
    $('#importExport').css("display", "none");
    $('#formLog').css("display", "none");

    $('#linkGeral').addClass('active');
    $('#linkSenha').removeClass('active');
    $('#linkImportExport').removeClass('active');
    $('#linkLog').removeClass('active');
  }

  function formSenha() {
    $('#formGeral').css("display", "none");
    $('#formSenha').css("display", "block");
    $('#importExport').css("display", "none");
    $('#formLog').css("display", "none");

    $('#linkGeral').removeClass('active');
    $('#linkSenha').addClass('active');
    $('#linkImportExport').removeClass('active');
    $('#linkLog').removeClass('active');
  }

  function formLog() {
    $('#formGeral').css("display", "none");
    $('#formSenha').css("display", "none");
    $('#importExport').css("display", "none");
    $('#formLog').css("display", "block");

    $('#linkGeral').removeClass('active');
    $('#linkSenha').removeClass('active');
    $('#linkImportExport').removeClass('active');
    $('#linkLog').addClass('active');
  }

  function formImportExport() {
    $('#formGeral').css("display", "none");
    $('#formSenha').css("display", "none");
    $('#importExport').css("display", "block");
    $('#formLog').css("display", "none");    

    $('#linkGeral').removeClass('active');
    $('#linkSenha').removeClass('active');
    $('#linkImportExport').addClass('active');
    $('#linkLog').removeClass('active');
  }
</script>
</html>
<?php

  function baixarBanco() {
    if(!ehLinux()) { // Essa função não funciona em sistemas que não executem mysqldump
      header('location: configuracao.php');
      exit(0);
    }
    system('mysqldump -h '.Config::SQL_host.' -u '.Config::SQL_user.' -p'.Config::SQL_pass.' --lock-tables '.Config::SQL_db.' > banco.tmp');

    $string = @file_get_contents('banco.tmp');

    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename=SECPUB_".date('Y-m-d').'.sql');
    header('Content-Length: '.strlen($string));
    header("Pragma: no-cache");
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header("Expires: 0");

    echo $string;

    @unlink('banco.tmp');
    exit(0);
  }

function uparBanco() {
  if($_FILES['arquivoBanco']['error']) {header("location: configuracao.php?upBanco=2&erroCod=".$_FILES['arquivoBanco']['error']);exit();}

  if(move_uploaded_file($_FILES['arquivoBanco']['tmp_name'], 'banco.tmp')) {
    system('mysql -h '.Config::SQL_host.' -u '.Config::SQL_user.' -p'.Config::SQL_pass.' '.Config::SQL_db.' < banco.tmp');
    @unlink('banco.tmp');
    header("location: configuracao.php?upBanco=1");
    exit();
  }
  else {
    header("location: configuracao.php?upBanco=2");
    exit();
  }
}

  function atualizarSenha($senha) {
    file_put_contents('../config.php',
                      bindConfig(
                                  $senha, Config::SQL_host, Config::SQL_db, Config::SQL_user, Config::SQL_pass,
                                  Config::CORS, Config::EXIBIR_ERROS, Config::USAR_CDN, Config::REQUISITAR_LOGIN
                                )
                    );
    header('location: configuracao.php');
    exit(0);
  }

  function atualizarGeral($servidor, $nomeBanco, $usuario, $senha, $CORS, $ERROS, $CDN, $RLogin) {
    if($CORS) $CORS = true;
    else      $CORS = false;

    if($ERROS) $ERROS = true;
    else       $ERROS = false;

    if($CDN) $CDN = true;
    else     $CDN = false;

    if($RLogin) $RLogin = true;
    else        $RLogin = false;

    file_put_contents('../config.php', bindConfig('', $servidor, $nomeBanco, $usuario, $senha, $CORS, $ERROS, $CDN, $RLogin));
    header('location: configuracao.php');
    exit(0);
  }

  function bindConfig(string $senhaSistema, string $servidor, string $nomeBanco,string  $usuario, string $senhaBanco, bool $CORS, bool $ERROS, bool $CDN, bool $RLogin) {
    if($CORS) $CORS = 'true';
    else      $CORS = 'false';

    if($ERROS) $ERROS = 'true';
    else       $ERROS = 'false';

    if($CDN) $CDN = 'true';
    else     $CDN = 'false';

    if($RLogin) $RLogin = 'true';
    else        $RLogin = 'false';

    $novaSenha = Config::SENHA;
    if($senhaSistema !== '')
      $novaSenha = hash('sha256', $senhaSistema);

    return '<?php

  class Config {
    const SQL_host = "'.$servidor.'"; // IP ou domínio do servidor do banco de dados
    const SQL_user = "'.$usuario.'";    // Usuário do bando de dados
    const SQL_pass = "'.$senhaBanco.'";      // Senha de acesso ao banco
    const SQL_db = "'.$nomeBanco.'";    // Banco contendo os dados

    const VERCAO = "'.Config::VERCAO.'";      // Versão do código atual
    const CORS = '.$CORS.';       // Permitir ou não o uso de cross origin domain
    const EXIBIR_ERROS = '.$ERROS.';    // Exibir erros do PHP. Recomendável: true para devenvolvimento e false para produção
    const USAR_CDN = '.$CDN.'; // Se deve ou não usar CDN para acesso aos css, js e fonts

    const REQUISITAR_LOGIN = '.$RLogin.';  // Se deve ou não exigir que o usuário insira a senha para acessar o sistema
    const SENHA = "'.$novaSenha.'";// A senha de acesso ao sistema encriptada com SHA256
  }

?>';
  }

  function ehLinux () {
    if(strtoupper(PHP_OS) === 'LINUX') return true;
    return false;
  }

?>