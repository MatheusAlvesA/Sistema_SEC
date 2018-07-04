<?php

  class Config {
    const SQL_host = "127.0.0.1"; // IP ou domínio do servidor do banco de dados
    const SQL_user = "root";    // Usuário do bando de dados
    const SQL_pass = "";      // Senha de acesso ao banco
    const SQL_db = "secnatal";    // Banco contendo os dados

    const VERCAO = "v0.8";      // Versão do código atual
    const CORS = false;       // Permitir ou não o uso de cross origin domain
    const EXIBIR_ERROS = true;    // Exibir erros do PHP. Recomendável: true para devenvolvimento e false para produção
    const USAR_CDN = false; // Se deve ou não usar CDN para acesso aos css, js e fonts

    const REQUISITAR_LOGIN = true;  // Se deve ou não exigir que o usuário insira a senha para acessar o sistema
    const SENHA = "03AC674216F3E15C761EE1A5E255F067953623C8B388B4459E13F978D7C846F4";// A senha(1234) de acesso ao sistema encriptada com SHA256
  }

?>