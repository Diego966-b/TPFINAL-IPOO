<?php
include_once ("BaseDatos.php");
class Pasajero 
{
    // Atributos

    private $nombre, $apellido, $dni, $telefono, $objViaje, $mensajeOperacion;

    // Métodos

    /**
     * Constructor de la clase
     */
    public function __construct ()
    {
        $this -> nombre = "";
        $this -> apellido = "";
        $this -> dni = "";
        $this -> telefono = "";
        $this -> objViaje = new Viaje();
    }

    /**
     * Carga los atributos de la clase con datos.
     * 
     * @param int $pdocumento, $pTelefono
     * @param string $pNombre, $pApellido
     * @param Viaje $objViaje
     */
    public function cargar ($pDocumento, $pNombre, $pApellido, $pTelefono, $objViaje)
    {
        $this -> setDni ($pDocumento);
        $this -> setNombre ($pNombre);
        $this -> setApellido ($pApellido);
        $this -> setTelefono ($pTelefono);
        $this -> setObjViaje ($objViaje);
    }
    
    /** 
	 * Busca un pasajero por su numero de documento. Retorna un booleano segun su exito. 
     * 
	 * @param int $dni
	 * @return boolean 
	 */		
    public function buscar ($dni)
    {
        // Variables Internas
        // BaseDatos $base
        // string $consultaPasajero
        // Viaje $objViaje
        // boolean $resp
		$base=new BaseDatos();
		$consultaPasajero ="Select * from pasajero where pdocumento=".$dni;
		$resp= false;
		if($base->Iniciar())
        {
			if($base->Ejecutar($consultaPasajero))
            {
				if($row2=$base->Registro())
                {					
				    $this -> setDni ($dni);
					$this -> setNombre ($row2['pnombre']);
					$this -> setApellido ($row2['papellido']);
					$this -> setTelefono ($row2['ptelefono']);
                    // obj viaje. Busco obj viaje con 'idviaje' de la BD y lo cargo
                    $objViaje = new Viaje();
					$objViaje -> buscar ($row2['idviaje']);
               		$this -> setObjViaje ($objViaje);
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
     * Modifica un pasajero. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
    public function modificar()
    {
        // Variables Internas
        // string $consultaModificar
        // boolean $resp
        // BaseDatos $base
	    $resp = false; 
	    $base = new BaseDatos();
		$consultaModificar = "UPDATE pasajero SET papellido='".$this->getApellido()."',pnombre='".$this->getNombre()."'
        ,ptelefono='".$this->getTelefono()."',idviaje='".$this->getObjViaje() -> getIdviaje ()."' WHERE pdocumento=". $this->getDni();
		if($base->Iniciar())
        {
			if($base->Ejecutar($consultaModificar))
            {
			    $resp=  true;
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
     * Elimina un pasajero. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function eliminar()
    {
        // Variables Internas
        // string $consultaBorrar
        // boolean $resp
        // BaseDatos $base
		$base = new BaseDatos();
		$resp = false;
		if($base -> Iniciar())
        {
			$consultaBorrar = "DELETE FROM pasajero WHERE pdocumento=".$this->getDni();
			if($base->Ejecutar($consultaBorrar))
            {
			    $resp = true;
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
     * Inserta un pasajero en la BD con los datos que tenia cargados.
     * Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function insertar ()
    {
        // Variables Internas
        // string $consultaInsertar
        // boolean $resp
        // BaseDatos $base
        $base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO pasajero(pdocumento, pnombre, papellido, ptelefono, idviaje) 
		VALUES (".$this->getDni().",'".$this->getNombre()."','".$this->getApellido()."','".$this->getTelefono()."','".$this->getObjViaje()->getIdViaje()."')";
		if($base->Iniciar())
        {
			if($base->Ejecutar($consultaInsertar))
            {
                $resp=  true;
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
     * Crea un arreglo con los pasajeros. Puede tener una condicion o no. 
     * 
     * @return array
     */
    public static function listar($condicion="")
    {
        // Variables Internas
        // boolean $inicio
        // array $arregloPasajero
        // string $consultaPasajeros, $pNombre, $pApellido
        // int $pDocumento, $pTelefono
        // Viaje $objViaje
        // Pasajero $pasajero
        // BaseDatos $base
	    $arregloPasajero = null;
		$base = new BaseDatos();
		$consultaPasajeros = "Select * from pasajero";
		if ($condicion != "")
        {
		    $consultaPasajeros=$consultaPasajeros.' where '.$condicion;
		}
		$consultaPasajeros.=" order by pdocumento ";
        $inicio = $base->Iniciar();
		if ($inicio)
        {
			if($base->Ejecutar($consultaPasajeros))
            {				
				$arregloPasajero= array();
				while($row2=$base->Registro())
                {
					$pDocumento = $row2['pdocumento'];
					$pNombre = $row2['pnombre'];
					$pApellido = $row2['papellido'];
					$pTelefono = $row2['ptelefono'];

                    // Busco obj viaje con 'idviaje' de la BD y lo cargo
					$objViaje  = new Viaje();
					$objViaje -> buscar($row2['idviaje']);
                    // Cargo el pasajero
					$pasajero = new Pasajero();
					$pasajero -> cargar ($pDocumento, $pNombre, $pApellido, $pTelefono, $objViaje);
					array_push ($arregloPasajero, $pasajero);
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
		return $arregloPasajero;
	}	
  
    // Métodos get

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
     * Get de dni
     * @return int
     */
    public function getDni ()
    {
        return $this -> dni;
    }

    /**
     * Get de telefono
     * @return int
     */
    public function getTelefono ()
    {
        return $this -> telefono;
    }

    /**
     * Get de mensajeOperacion
     * @return string
     */
    public function getMensajeOperacion ()
    {
        return $this -> mensajeOperacion;
    }

    /**
     * Get de objViaje
     * @return Viaje
     */
    public function getObjViaje ()
    {
        return $this -> objViaje;
    }

    // Métodos set

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
     * Set de dni
     * @param int $dniNuevo
     */
    public function setDni ($dniNuevo)
    {
        $this -> dni = $dniNuevo;
    }

    /**
     * Set de telefono
     * @param int $telefonoNuevo
     */
    public function setTelefono ($telefonoNuevo)
    {
        $this -> telefono = $telefonoNuevo;
    }

    /**
     * Set de mensajeOperacion
     * @param string $mensajeOperacionNuevo
     */
	public function setMensajeOperacion ($mensajeOperacionNuevo)
    {
		$this -> mensajeOperacion = $mensajeOperacionNuevo;
	}

    /**
     * Set de objViaje
     * @param int $objViajeNuevo
     */
    public function setObjViaje ($objViajeNuevo)
    {
        $this -> objViaje = $objViajeNuevo;
    }

    // Método __toString

    /**
     * Retorna la información de los atributos de las clases en forma de string
     * 
     * @return string
     */
    public function __toString ()
    {
        $frase = 
        "Nombre: ".$this -> getNombre ().
        ". Apellido: ".$this -> getApellido ().
        ". Documento: ".$this -> getDni ().
        ". Teléfono: ".$this -> getTelefono ().
        ". ID del viaje: ".$this -> getObjViaje() -> getIdViaje();
        return $frase;
    }
}