<?php
namespace Persistencia;

use Exception;

class PersistenciaException extends Exception {
	protected $estado;

	public function setEstado(string $e) {$this->estado = $e;}
	public function getEstado(): string {
		if(is_null($this->estado))
			return 'Estado não definido';
		return $this->estado;
	}

}