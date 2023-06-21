<?php
include_once ("BaseDatos.php");
class Viaje
{   
    // Atributos
    private $destinoViaje, $cantMaxPasajeros, $objResponsableV, $costoPasaje, $idViaje, $objEmpresa, $mensajeOperacion;

    /**
     * Constructor de la clase
     */
    public function __construct ()
    {
        $this -> destinoViaje = "";
        $this -> cantMaxPasajeros = "";
        $this -> objResponsableV = new ResponsableV ();
        $this -> costoPasaje = "";
        $this -> idViaje = 0;
        $this -> objEmpresa = new Empresa ();
    }
    
    // Métodos

    /**
     * Carga los atributos de la clase con datos.
     * 
     * @param int $idViaje, $vCantMaxPasajeros
     * @param string $vDestino
     * @param Empresa $objEmpresa
     * @param ResponsableV $objResponsableV
     * @param float $vImporte
     */
    public function cargar ($idViaje, $vDestino, $vCantMaxPasajeros, $objEmpresa, $objResponsableV, $vImporte)
    {
        $this -> setIdViaje($idViaje);
        $this -> setDestinoViaje($vDestino);
        $this -> setCantMaxPasajeros($vCantMaxPasajeros);
        $this -> setObjEmpresa ($objEmpresa);
        $this -> setObjResponsableV ($objResponsableV);
        $this -> setCostoPasaje($vImporte);
    }   

    /**
	 * Busca un viaje por su ID. Retorna un booleano segun su exito. 
     * 
	 * @param int $idViaje
	 * @return boolean 
	 */		
    public function buscar ($idViaje)
    {
        // Variables Internas
        // string $consultaViaje
        // boolean $resp
        // Empresa $objEmpresa
        // ResponsableV $objResponsable
        // BaseDatos $base
		$base = new BaseDatos();
		$consultaViaje = "Select * from viaje where idviaje=".$idViaje;
		$resp = false;
		if($base -> Iniciar())
        {
			if($base -> Ejecutar($consultaViaje))
            {
				if($row2=$base->Registro())
                {					
				    $this -> setIdViaje ($idViaje);
					$this -> setDestinoViaje ($row2['vdestino']);
					$this -> setCantMaxPasajeros ($row2['vcantmaxpasajeros']);
                    $this -> setCostoPasaje ($row2['vimporte']);

                    // Busco obj empresa con 'idempresa' de la BD y lo cargo.
                    $objEmpresa = new empresa();
					$objEmpresa -> buscar ($row2['idempresa']);
					$this -> setObjEmpresa ($objEmpresa);

                    // Busco obj responsable con 'rnumeroempleado' de la BD y lo cargo
					$objResponsable = new ResponsableV();
					$objResponsable -> buscar ($row2['rnumeroempleado']);
					$this -> setObjResponsableV ($objResponsable);
					$resp= true;
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
     * Modifica un viaje. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
    public function modificar ()
    {
        // Variables Internas
        // string $consultaModificar
        // Boolean $resp
        // BaseDatos $base

	    $resp =false; 
	    $base = new BaseDatos();
		$consultaModificar = "UPDATE viaje SET idempresa='".$this->getObjEmpresa()->getIdEmpresa()."',vdestino='".$this->getDestinoViaje()."'
        ,vcantmaxpasajeros='".$this->getCantMaxPasajeros()."',vimporte='".$this->getCostoPasaje()."',rnumeroempleado='".$this->getObjResponsableV()->getNroEmpleado().
        "' WHERE idviaje=". $this->getIdviaje();
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
     * Elimina un viaje. Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function eliminar ()
    {
        // Variables Internas
        // string $consultaBorrar
        // boolean $resp
        // BaseDatos $base
		$base = new BaseDatos();
		$resp = false;
		if($base->Iniciar())
        {
			$consultaBorrar = "DELETE FROM viaje WHERE idviaje=".$this->getIdViaje();
			if($base -> Ejecutar( $consultaBorrar))
            {
				$resp=  true;
			}
            else
            {
				$this -> setMensajeOperacion ($base -> getError());
			}
		}
        else
        {
			$this -> setMensajeOperacion($base -> getError());
		}
		return $resp; 
	}

    /**
     * Inserta un viaje en la BD con los datos que tenia cargados.
     * Retorna un booleano segun su exito.
     * 
     * @return boolean
     */
	public function insertar ()
    {
        // Variables Internas
        // string $consultaInsertar
        // boolean $resp
        // int $id
        // BaseDatos $base
        $base=new BaseDatos();
		$resp= false;
		$consultaInsertar = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) 
		VALUES ('".$this->getDestinoViaje()."','".$this->getCantMaxPasajeros()."','".$this->getObjEmpresa()->getIdEmpresa()."','".$this->getObjResponsableV()->getNroEmpleado()."','".$this -> getCostoPasaje()."')";
        if ($base -> Iniciar())
        {
            if($id = $base -> devuelveIDInsercion ($consultaInsertar))
            {
                $this->setIdViaje ($id);
                $resp = true;
            }	
            else 
            {
                $this->setmensajeoperacion ($base -> getError());
            }
        } 
        else 
        {
            $this->setmensajeoperacion ($base -> getError());
        }
    return $resp;
}

    /**
     * Crea un arreglo con los viajes. Puede tener una condicion o no. 
     * 
     * @return array
     */
    public static function listar ($condicion="")
    {
        // Variables Internas
        // boolean $inicio
        // array $arregloViajes
        // string $consultaViajes
        // BaseDatos $base
	    $arregloViajes = null;
		$base = new BaseDatos();
		$consultaViajes = "Select * from viaje ";
		if ($condicion != "")
        {
		    $consultaViajes = $consultaViajes.' where '.$condicion;
		}
		$consultaViajes.=" order by idviaje ";
        $inicio = $base -> Iniciar();
		if ($inicio)
        {
			if ($base -> Ejecutar($consultaViajes))
            {				
				$arregloViajes = array();
				while ($row2=$base->Registro())
                {
                    $idViaje = $row2['idviaje'];
                    $destino = $row2['vdestino'];
                    $cantMaxPas = $row2['vcantmaxpasajeros'];
                    $viajeImporte = $row2['vimporte'];

                    $objResponsableV = new ResponsableV ();
                    $objResponsableV -> buscar ($row2['rnumeroempleado']);
                    
                    $empresa = new Empresa ();
                    $empresa -> buscar ($row2['idempresa']);

					$viaje = new Viaje();
					$viaje -> cargar($idViaje, $destino, $cantMaxPas, $empresa, $objResponsableV, $viajeImporte);
					array_push ($arregloViajes,$viaje);
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
		return $arregloViajes;
	}	
    
    /**
     * Incorpora el pasajero a la colección de pasajeros (solo si hay espacio disponible) y 
     * retorna el costo que debe abonar el pasajero.
     * Retorna 0 si no se pudo vender.
     * 
     * @param string $pNombre, $pApellido
     * @param int $pDocumento, $pTelefono
     * @param Viaje $viaje
     * @return float
     */
    public function venderPasaje ($pDocumento, $pNombre, $pApellido, $pTelefono, $viaje)
    {
        // Variables Internas
        // float $costoFinal
        // string $condicion
        // array $colPasajeros
        // boolean $hayPasajesDisp
        // Pasajero $objPasajero
        $costoFinal = 0;
        $objPasajero = new Pasajero ();

        $condicion = "idviaje=".$viaje->getIdViaje();
        $colPasajeros = $objPasajero -> listar ($condicion);
        $hayPasajesDisp = $this -> hayPasajesDisponible ($colPasajeros);
        if ($hayPasajesDisp)
        {
            $costoFinal = $this -> getCostoPasaje ();
            $objPasajero -> cargar ($pDocumento, $pNombre, $pApellido, $pTelefono, $viaje);
            $objPasajero -> insertar ();
        }
        return $costoFinal;
    }
    
    /**
     * Retorna verdadero si la cantidad de pasajeros del viaje es menor a la cantidad máxima de pasajeros y falso caso contrario.
     * 
     * @param array $colPasajeros
     * @return boolean
     */
    public function hayPasajesDisponible ($colPasajeros)
    {
        // Variables Internas
        // array $arrayPasajeros
        // int $cantMaxPasajeros, $cantPasajeros
        // boolean $hayPasajesDisp
        $hayPasajesDisp = false;
        $cantMaxPasajeros = $this -> getCantMaxPasajeros();
        $cantPasajeros = count ($colPasajeros);
        if ($cantPasajeros < $cantMaxPasajeros)
        {   
            $hayPasajesDisp = true;
        }
        return $hayPasajesDisp;
    }

    // Métodos get

    /**
     * get de destinoViaje
     * @return string
     */
    public function getDestinoViaje ()
    {
        return $this -> destinoViaje;
    }

    /**
     * Get de cantMaxPasajeros
     * @return int
     */
    public function getCantMaxPasajeros ()
    {
        return $this -> cantMaxPasajeros;
    }    

    /**
     * Get de $objResponsableV
     * @return ResponsableV
     */
    public function getObjResponsableV ()
    {
        return $this -> objResponsableV;
    }

    /**
     * Get de costoPasaje
     * @return float
     */
    public function getCostoPasaje ()
    {
        return $this -> costoPasaje;
    }

    /**
     * Get de idViaje
     * @return int
     */
    public function getIdViaje ()
    {
        return $this -> idViaje;
    }

    /**
     * Get de objEmpresa
     * @return Empresa
     */
    public function getObjEmpresa ()
    {
        return $this -> objEmpresa;
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
     * Set de destinoViaje 
     * @param string $destinoViajeNuevo
     */
    public function setDestinoViaje ($destinoViajeNuevo)
    {
        $this -> destinoViaje = $destinoViajeNuevo;
    }

    /**
     * Set de cantMaxPasajeros 
     * @param int $cantMaxPasajerosNuevo
     */
    public function setCantMaxPasajeros ($cantMaxPasajerosNuevo)
    {
        $this -> cantMaxPasajeros = $cantMaxPasajerosNuevo;
    }
 
    /**
     * Set de objResponsableV
     * @param ResponsableV $ObjResponsableVNuevo
     */
    public function setObjResponsableV ($ObjResponsableVNuevo)
    {
        $this -> objResponsableV = $ObjResponsableVNuevo;
    }

    /**
     * Set de costoPasaje
     * @param float $costoPasajeNuevo
     */
    public function setCostoPasaje ($costoPasajeNuevo)
    {
        $this -> costoPasaje = $costoPasajeNuevo;
    }

    /**
     * Set de idViaje
     * @param int $idViajeNuevo
     */
    public function setIdViaje ($idViajeNuevo)
    {
        $this -> idViaje = $idViajeNuevo;
    }

    /**
     * Set de objEmpresa
     * @param Empresa $objEmpresaNuevo
     */
    public function setObjEmpresa ($objEmpresaNuevo)
    {
        $this -> objEmpresa = $objEmpresaNuevo;
    }

    /**
     * Set de mensajeOperacion
     * @param string $mensajeOperacionNuevo
     */
	public function setMensajeOperacion ($mensajeOperacionNuevo)
    {
		$this -> mensajeOperacion = $mensajeOperacionNuevo;
	}

    // Métodos __toString y mostrarPasajeros

    /**
     * Retorna la información de los atributos de las clases en forma de string
     * @return string
     */
    public function __toString ()
    {
        // Variables Internas
        // string $frase, $condicion
        // array $colPasajeros
        // int $cantPasajeros
        // Pasajero $pasajero
        $pasajero = new Pasajero();
        $condicion = "idviaje=".$this -> getIdViaje ();
        $colPasajeros = $pasajero -> listar($condicion);
        $frase = 
        "\n- - - - Informacion del viaje - - - -\n\n".
        "- - ID del viaje: ".$this -> getIdViaje().
        ".\n- - El destino es: ".$this -> getDestinoViaje().
        ".\n- - ID de la empresa: ".($this -> getObjEmpresa() -> getIdEmpresa()).
        ".\n- - Cantidad maxima de pasajeros: ".$this -> getCantMaxPasajeros().
        ".\n- - Pasajeros cargados: ".count ($colPasajeros). 
        ".\n- - Costo del pasaje: $ ".$this -> getCostoPasaje().
        "\n\n- - - - Informacion del responsable del viaje - - - -\n\n".$this -> getObjResponsableV ().
        ".\n\n- - - - Datos de los pasajeros - - - -\n".$this -> mostrarPasajeros ();
        return $frase;
    }

    /**
     * Recorre el array de pasajeros exhaustivamente y guarda en un string cada una de sus datos para luego retornarlo
     * 
     * @return string
     */
    public function mostrarPasajeros () 
    {
        // Variables Internas
        // string $frase, $condicion
        // array $colPasajeros
        // int $pos, $cantPasajeros
        // Pasajero $pasajero
        $frase = "";
        $pasajero = new Pasajero();
        $condicion = "idviaje=".$this -> getIdViaje ();
        $colPasajeros = $pasajero -> listar($condicion);
        $cantPasajeros = count ($colPasajeros);
        for ($pos = 0; $pos < $cantPasajeros; $pos ++)
        {
            $pasajero = $colPasajeros [$pos];
            $frase = $frase. "\n- - Pasajero N°".($pos+1).": ".$pasajero;
        }
        return $frase;
    }
}