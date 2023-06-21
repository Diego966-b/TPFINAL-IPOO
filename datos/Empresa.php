<?php
class Empresa
{
    private $idEmpresa, $nombre, $direccion, $mensajeOperacion;    

    /**
     * Constructor de la clase
     */
    public function __construct()
    {   
        $this -> idEmpresa = 0;
        $this -> nombre = "";
        $this -> direccion = "";
    }
    
    // Métodos

    /**
     * Carga los atributos de la clase con datos.
     * 
     * @param int $idEmpresa
     * @param string $nombre, $direcicon
     */	
    public function cargar ($idEmpresa, $nombre ,$direccion)
    {		
		$this -> setIdEmpresa ($idEmpresa);
		$this -> setNombre ($nombre);
		$this -> setDireccion ($direccion);
    }

    /**
	 * Busca una empresa por su ID de empresa. Retorna un booleano segun su exito. 
     * 
	 * @param int $idEmpresa
	 * @return boolean 
     */	
    public function buscar ($idEmpresa)
    {
        // Variables Internas
        // boolean $resp
        // string $consultaEmpresa
        // BaseDatos $base
		$base = new BaseDatos();
		$consultaEmpresa = "Select * from empresa where idempresa=".$idEmpresa;
		$resp = false;
		if ($base -> Iniciar())
        {
			if ($base -> Ejecutar ($consultaEmpresa))
            {
				if ($row2 = $base -> Registro())
                {					
				    $this -> setIdEmpresa ($idEmpresa);
					$this -> setNombre ($row2['enombre']);
					$this -> setDireccion ($row2['edireccion']);
					$resp = true;
				}				
		 	}	
            else 
            {
		 		$this -> setMensajeOperacion ($base->getError());
			}
		}	
        else 
        {
		 	$this -> setMensajeOperacion ($base->getError());	
		}		
		return $resp;
	}

    /**
     * Modifica una empresa. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */	
    public function modificar ()
    {
        // Variables Internas
        // Boolean $resp
        // string $consultaModificar
        // BaseDatos $base
	    $resp =false; 
	    $base = new BaseDatos();
		$consultaModificar = "UPDATE empresa SET enombre='".$this->getNombre()."',edireccion='".$this->getDireccion()."' WHERE idempresa=". $this->getIdEmpresa();
		if ($base -> Iniciar())
        {
			if ($base -> Ejecutar($consultaModificar))
            {
			    $resp = true;
			}
            else
            {
				$this -> setMensajeOperacion ($base->getError());
			}
		}
        else
        {
			$this -> setMensajeOperacion ($base->getError());
		}
		return $resp;
	} 

	/**
     * Elimina una empresa. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function eliminar()
    {
        // Variables Internas
        // string $consultaBorrar
        // Boolean $resp
        // BaseDatos $base
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar())
        {
			$consultaBorrar = "DELETE FROM empresa WHERE idempresa=".$this->getIdEmpresa();
			if($base -> Ejecutar($consultaBorrar))
            {
				$resp = true;
			}
            else
            {
				$this -> setMensajeOperacion ($base->getError());		
			}
		}
        else
        {
			$this -> setMensajeOperacion ($base->getError());
		}
		return $resp; 
	}

    /**
     * Inserta una empresa en la BD con los datos que tenia cargados.
     * Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function insertar ()
    {
        // Variables Internas
        // string $consultaInsertar
        // int $id
        // Boolean $resp
        // BaseDatos $base
        $base = new BaseDatos();
		$resp = false;
		$consultaInsertar = "INSERT INTO empresa(enombre, edireccion) 
	    VALUES ('".$this->getNombre()."','".$this->getDireccion()."')";
		if ($base->Iniciar())
        {
            if ($id = $base -> devuelveIDInsercion ($consultaInsertar))
            {
                $this -> setIdEmpresa($id);
                $resp = true;
            }	
            else 
            {
                $this -> setmensajeoperacion($base->getError());
            }
		} 
        else 
        {
			$this -> setMensajeOperacion($base->getError());
			
		}
		return $resp;
	}

    /**
     * Crea un arreglo con las empresas. Puede tener una condicion o no. 
     * 
     * @return array
     */
    public static function listar($condicion="")
    {
        // Variables Internas
        // array $arregloEmpresas
        // string $consultaEmpresas
        // boolean $inicio
        // BaseDatos $base
	    $arregloEmpresas = null;
		$base = new BaseDatos();
		$consultaEmpresas = "Select * from empresa ";
		if ($condicion!="")
        {
		    $consultaEmpresas=$consultaEmpresas.' where '.$condicion;
		}
		$consultaEmpresas.=" order by idempresa ";
        $inicio = $base->Iniciar();
		if ($inicio)
        {
			if ($base -> Ejecutar($consultaEmpresas))
            {				
				$arregloEmpresas = array();
				while ($row2 = $base -> Registro())
                {
                    $idEmpresa = $row2['idempresa'];
                    $nombre = $row2['enombre'];
                    $direccion = $row2['edireccion'];

					$empresa = new Empresa();
					$empresa -> cargar ($idEmpresa, $nombre, $direccion);
					array_push ($arregloEmpresas,$empresa);
				}
		 	}	
            else 
            {
		 		$this -> setMensajeOperacion ($base->getError()); 
			}
		}	
        else 
        {
		 	$this -> setMensajeOperacion ($base->getError()); 
		}	
		return $arregloEmpresas;
	}	

    // Métodos Get

    /**
     * Get de idEmpresa
     * @return int
     */
    public function getIdEmpresa ()
    {
        return $this -> idEmpresa;
    }

    /**
     * Get de nombre
     * @return string
     */
    public function getNombre ()
    {
        return $this -> nombre;
    }

    /**
     * Get de direccion
     * @return string
     */
    public function getDireccion ()
    {
        return $this -> direccion;
    }

    /**
     * Get de mensajeOperacion
     * @return string
     */
    public function getMensajeOperacion ()
    {
        return $this -> mensajeOperacion;
    }

    // Métodos Set

    /**
     * Set de idEmpresa
     * @param int $idEmpresaNuevo 
     */
    public function setIdEmpresa ($idEmpresaNuevo)
    {
        $this -> idEmpresa = $idEmpresaNuevo;
    }

    /**
     * Set de nombre
     * @param string $nombreNuevo
     */
    public function setNombre ($nombreNuevo)
    {
        $this -> nombre = $nombreNuevo;
    }

    /**
     * Set de direccion
     * @param string $direccionNueva
     */
    public function setDireccion ($direccionNueva)
    {
        $this -> direccion = $direccionNueva;
    }

    /**
     * Set de mensajeOperacion
     * @param string $mensajeOperacionNuevo
     */
	public function setMensajeOperacion ($mensajeOperacionNuevo)
    {
		$this -> mensajeOperacion = $mensajeOperacionNuevo;
	}

    // Método __toString

    /**
     * Devuelve los valores de los atributos en forma de string
     */
    public function __toString()
    {
        // Variables Internas
        // string $frase
        $frase = 
        "El ID de la empresa es: ".$this -> getIdEmpresa().
        ".\nEl nombre de la empresa es: ".$this -> getNombre().
        ".\nLa dirección de la empresa es: ".$this -> getDireccion().".";
        return $frase;
    }
}