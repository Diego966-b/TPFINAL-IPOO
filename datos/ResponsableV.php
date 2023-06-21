<?php
include_once ("BaseDatos.php");
class ResponsableV 
{
    // Atributos

    private $nroEmpleado, $nroLicencia, $nombre, $apellido, $mensajeOperacion;

    // Métodos

    /**
     * Constructor de la clase
     */
    public function __construct ()
    {
        $this -> nroEmpleado = 0;
        $this -> nroLicencia = "";
        $this -> nombre = "";
        $this -> apellido = "";
    }

    /**
     * Carga los atributos de la clase con datos.
     * 
     * @param int $nroEmpleado, $nroLicencia
     * @param string $nombre, $apellido
     */
    public function cargar ($nroEmpleado, $nroLicnecia, $nombre, $apellido)
    {
        $this -> setNroEmpleado ($nroEmpleado);
        $this -> setNroLicencia ($nroLicnecia);
        $this -> setNombre ($nombre);
        $this -> setApellido ($apellido);
    }

    /**
	 * Busca un responsable por su numero de empleado. Retorna un booleano segun su exito. 
     * 
	 * @param int $nroEmpleado
	 * @return boolean 
     */	
    public function buscar($nroEmpleado)
    {
        // Variables Internas
        // boolean $resp
        // string $consultaResponsable
        // BaseDatos $base
		$base = new BaseDatos();
		$consultaResponsable = "Select * from responsable where rnumeroempleado=".$nroEmpleado;
		$resp = false;
		if ($base->Iniciar())
        {
			if ($base->Ejecutar($consultaResponsable))
            {
				if ($row2=$base->Registro())
                {					
				    $this -> setNroEmpleado ($nroEmpleado);
					$this -> setNroLicencia ($row2['rnumerolicencia']);
					$this -> setNombre ($row2['rnombre']);
                    $this -> setApellido ($row2['rapellido']);
					$resp = true;
				}				
		 	}	
            else 
            {
		 		$this->setMensajeOperacion($base->getError());
			}
		}	
        else 
        {
		 	$this->setMensajeOperacion($base->getError());
		}		
		return $resp;
	}

    /**
     * Modifica un responsable. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */	
    public function modificar()
    {
        // Variables Internas
        // Boolean $resp
        // string $consultaModificar
	    $resp = false; 
	    $base = new BaseDatos();
		$consultaModificar = "UPDATE responsable SET rnumerolicencia='".$this->getNroLicencia()."',rnombre='".$this->getNombre()."',rapellido='".$this->getApellido()."' WHERE rnumeroempleado=". $this->getNroEmpleado();
		if ($base -> Iniciar())
        {
			if ($base -> Ejecutar($consultaModificar))
            {
			    $resp=  true;
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
     * Elimina un responsable. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function eliminar()
    {
        // Variables Internas
        // Boolean $resp
        // string $consultaBorrar
        // BaseDatos $base
		$base = new BaseDatos();
		$resp = false;
		if ($base->Iniciar())
        {
			$consultaBorrar = "DELETE FROM responsable WHERE rnumeroempleado=".$this->getNroEmpleado();
			if ($base -> Ejecutar($consultaBorrar))
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
     * Inserta un responsable en la BD con los datos que tenia cargados.
     * Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function insertar ()
    {
        // Variables Internas
        // int $numEmpleado
        // Boolean $resp
        // string $consultaInsertar
        // BaseDatos $base
        $base = new BaseDatos();
		$resp = false;
		$consultaInsertar = "INSERT INTO responsable(rnumerolicencia, rnombre, rapellido) 
		VALUES ('".$this->getNroLicencia()."','".$this->getNombre()."','".$this->getApellido()."')";
		if ($base->Iniciar())
        {
            if ($numEmpleado = $base->devuelveIDInsercion($consultaInsertar))
            {
                $this->setNroEmpleado($numEmpleado);
                $resp = true;
            }	
            else 
            {
                $this->setmensajeoperacion($base->getError());
            }
		} 
        else 
        {
			$this->setMensajeOperacion($base->getError());
		}
		return $resp;
	}

    /**
     * Crea un arreglo con los responsables. Puede tener una condicion o no. 
     * 
     * @return array
     */
    public static function listar($condicion="")
    {
        // Variables Internas
        // boolean $inicio
        // array $arrelgoResponsable
        // string $consultaResponsables
        // BaseDatos $base
	    $arregloResponsable = null;
		$base = new BaseDatos();
		$consultaResponsables = "Select * from responsable ";
		if ($condicion!="")
        {
		    $consultaResponsables=$consultaResponsables.' where '.$condicion;
		}
		$consultaResponsables.=" order by rapellido ";
        $inicio = $base->Iniciar();
		if ($inicio)
        {
			if ($base -> Ejecutar ($consultaResponsables))
            {				
				$arregloResponsable = array();
				while ($row2 = $base -> Registro())
                {
                    $nroEmpleado = $row2['rnumeroempleado'];
                    $nroLicencia = $row2['rnumerolicencia'];
                    $nombre = $row2['rnombre'];
                    $apellido = $row2['rapellido'];

					$responsable = new ResponsableV();
					$responsable -> cargar ($nroEmpleado, $nroLicencia, $nombre, $apellido);
					array_push ($arregloResponsable,$responsable);
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
		return $arregloResponsable;
	}

    // Métodos get

    /**
     * Get de nroEmpleado
     * @return int 
     */
    public function getNroEmpleado ()
    {
        return $this -> nroEmpleado;
    }

    /**
     * Get de nroLicencia
     * @return int 
     */
    public function getNroLicencia ()
    {
        return $this -> nroLicencia;
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
     * Get de apellido
     * @return string
     */
    public function getApellido ()
    {
        return $this -> apellido;
    }    

    /**
     * Get de mensajeOperacion
     * @return string
     */
    public function getMensajeOperacion ()
    {
        return $this -> mensajeOperacion;
    }

    // Métodos set

    /**
     * Set de nroEmpleado
     * @param int $nroEmpleadoNuevo
     */
    public function setNroEmpleado ($nroEmpleadoNuevo)
    {
        $this -> nroEmpleado = $nroEmpleadoNuevo;
    }

    /**
     * Set de nroLicencia
     * @param int $nroLicenciaNuevo
     */
    public function setNroLicencia ($nroLicenciaNuevo)
    {
        $this -> nroLicencia = $nroLicenciaNuevo;
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
     * Set de apellido
     * @param string $apellidoNuevo
     */
    public function setApellido ($apellidoNuevo)
    {
        $this -> apellido = $apellidoNuevo;    
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
     * Retorna la información de los atributos de las clases en forma de string
     * @return string
     */
    public function __toString()
    {
        $frase = 
        "- - N° de empleado: ".$this -> getnroEmpleado ().
        ".\n- - Nombre del responsable: ".$this -> getNombre ().
        ".\n- - Apellido del responsable: ".$this -> getApellido ().
        ".\n- - N° de licencia: ".$this -> getNroLicencia ();
        return $frase;
    }
}