<?php
namespace Sistema;
use Persistencia\PersistenciaException;
	class Logger {

		const arquivoLog = 'erros.txt';

		public static function logar(PersistenciaException $e) {
			$anterior = $e->getPrevious();
			if($anterior !== null)
				$anterior = 'Anterior: '.$anterior->getMessage()."\n";
			else
				$anterior = '';

			$mensagem = "\n------------- ".date('Y/m/d - H:i')." --------------\n\n".
						'Erro:     '.$e->getMessage()."\n".
						utf8_encode($anterior).
						'Estado:   '.$e->getEstado()."\n".
						$e->getTraceAsString().
						"\n----------------------------------------------\n\n";

			if(file_exists(Logger::arquivoLog))
				$mensagem .= file_get_contents(Logger::arquivoLog);

			@file_put_contents(Logger::arquivoLog, $mensagem);
		}

		public static function limpar() {
			if(file_exists(Logger::arquivoLog))
				@unlink(Logger::arquivoLog);
		}
	}
?>