<?php
require_once('../config.php');

session_start();

if($_SESSION['logado'] === 'S' || !Config::REQUISITAR_LOGIN) { // Se já estiver logado ou não for nescesário logar
  header('location: index.php'); // Redirecione para o sistema
  exit(0);
}

if(isset($_POST['senha'])) { // Se uma senha foi passada
  efetuarLogin($_POST['senha']); // Execute uma tentativa de login
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

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  ';
  } else {
    echo '
  <link rel="stylesheet" href="Terceiros/CSS/bootstrap.min.css">

  <script src="Terceiros/JS/jquery-3.3.1.min.js"></script>
  <script src="Terceiros/JS/popper.min.js"></script>
  <script src="Terceiros/JS/bootstrap.min.js"></script>
  ';
}
?>
</head>

<body>

<nav class="navbar navbar-expand-sm navbar-toggleable-md bg-dark navbar-dark">

  <a class="navbar-brand" href="#" style="margin: 0;">
    <img src="imgs/logo.png" id="img_logo" alt="logo" style="width:40px; float: left;">
  </a>
  <h4 style="color: white;">Sistema SEC <small class="text-muted" style="font-size: 7pt;"><?php echo Config::VERCAO;?></small></h4>

</nav>

<br>

<div class="container">
	<div class="row">

		<div class="col-md-4 offset-md-4">
      <?php 
        if($_GET['loginErro']) echo '<div class="alert alert-danger"><strong>Erro!</strong> A senha está incorreta</div>';
      ?>
      <hr>
      <div class="card text-white bg-dark">
        <div class="card-header">
          <img src="imgs/logo.png" style="width: 10%;" />
          <b style="color: rgb(173,14,10);">SEC Publicidade</b>
        </div>
        <div class="card-body">
          <h5 class="card-title">Insira a senha:</h5>
          <form class="form-inline" action="login.php" method="post">
            <div class="form-group mx-sm-3 mb-2">
              <input type="password" name="senha" class="form-control form-control-lg" placeholder="Senha">
            </div>
            <button type="submit" class="btn btn-primary btn-lg ml-auto" style="cursor: pointer;">Entrar</button>
          </form>
        </div>
      </div>
      <hr>
    </div>

	</div>
</div>

</body>
</html>

<?php

function efetuarLogin($senha) {
  $senhaInserida = strtoupper(hash('sha256', $senha)); // Criptografando a senha e colocando em upercase
  $senhaCorreta  = strtoupper(Config::SENHA);           // Pegando a senha nas configurações e colocando em upercase

  if($senhaInserida === $senhaCorreta) { // Se a senha estiver correta
    $_SESSION['logado'] = 'S'; // Registre na sessão que o usuário está logado
    header('location: index.php'); // Redirecione para o sistema
  }
  else // Se a senha estiver incorreta
    header('location: login.php?loginErro=1'); // Redirecione para a tela de login informando um erro
}

?>