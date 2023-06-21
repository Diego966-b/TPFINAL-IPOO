<?php
// PROGRAMA PRINCIPAL 
// Declaracion de variables
/* int $opcion, $opcionSubmenu, $idSeleccionado, $nroEmpleado, $cantMaxPasajerosV, $numEmpleadoV
   $nuevaCantMaxPasajerosV, $nuevoIdEmpresa, $nuevoNumEmpleado, $nuevoNumLicR, $numEmpleadoSeleccionado
   $pasajerosACargar, $pasajerosCargados, $asientosDisponibles, $maxPasajerosViaje, $j, $pTelefono, 
   $pDocumento, $docSeleccionado, $nuevoTel, $nuevoIdViaje, $numEmpleadoViaje, $numLicR  */
// float $importeV, $nuevoImporteV, $costoFinal 
// array $colViajes, $colPasajeros, $colResponsables, $colPasEmp
/* string $nomEmpresa, $dirEmpresa, $condicion, $nuevoNom, $nuevaDir, $destinoV, $nuevoDestinoV, 
   $pNombre, $pApellido, $respuesta, $nombreR, $apellidoR */
// boolean $hayPasajeros
// Empresa $objEmpresa, $empresaSeleccionada
// Viaje $objViaje, $viaje 
// Pasajero $objPasajero, $pasajero
// ResponsableV $objResponsable

include_once ("../datos/BaseDatos.php");
include_once ("../datos/Viaje.php");
include_once ("../datos/ResponsableV.php");
include_once ("../datos/Pasajero.php");
include_once ("../datos/Empresa.php");

/**
 * Este modulo imprime por pantalla los datos en la BD 
 * del obj recibido por parametro.
 * 
 * @param $obj (puede ser un $viaje, $empresa, $responsable o $pasajero)
 * @param $condicion (opcional)
 */
function verDatos ($obj, $condicion)
{
    // Variables Internas
    // array $coleccion
    // $elemento puede ser $empresa, $viaje, $responsable o $pasajero
    $coleccion = array ();
    if ($condicion <> "")
    {       
        echo $condicion;
        $coleccion = $obj -> listar($condicion);
        foreach ($coleccion as $elemento)
        {
            echo "\n-------------------------------------------------------\n";
            echo $elemento;
            echo "\n-------------------------------------------------------\n";
        }
    }
    else
    {
        $coleccion = $obj -> listar();
        foreach ($coleccion as $elemento)
        {
            echo "\n-------------------------------------------------------\n";
            echo $elemento;
            echo "\n-------------------------------------------------------\n";
        }
    }
}

/**
 * Retorna un arreglo de pasajeros cargados en la empresa.
 * 
 * @param Viaje $objViaje
 * @param Empresa $objEmpresa
 * @return array
 */
function devolverColPasajeros ($objEmpresa, $objViaje, $objPasajero)
{
    // Variables Internas 
    // array $colPasEmp, $colViajes, $colPasajeros
    // string $condicion
    // int $idViaje, $idViajePasajero
    // Pasajero $pasajero
    // Viaje $viaje
    $colPasEmp = array ();
    $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
    $colViajes = $objViaje -> listar($condicion);
    $colPasajeros = $objPasajero -> listar();
    foreach ($colViajes as $viaje)
    {
        $idViaje = $viaje -> getIdViaje ();
        foreach ($colPasajeros as $pasajero)
        {
            $idViajePasajero = $pasajero -> getObjViaje () -> getIdViaje ();
            if ($idViaje == $idViajePasajero)
            {
                array_push ($colPasEmp, $pasajero);
            }
        }
    }
    return $colPasEmp;
}

do 
{   
    $empresaSeleccionada = new Empresa ();
    $objEmpresa = new Empresa ();
    $objViaje = new Viaje ();
    $objPasajero = new Pasajero ();
    $objResponsable = new ResponsableV ();
    echo " ---------------------------------------------- ";
    echo "\n -------------- Menú de empresas -------------- \n";
    echo " ---------------------------------------------- \n";
    echo "<1> Ingresar una empresa \n";
    echo "<2> Eliminar una empresa \n";
    echo "<3> Modificar una empresa \n";
    echo "<4> Mostrar las empresas cargadas \n";
    echo "<5> Administrar Viajes \n";
    echo "<6> Salir \n";
    echo "<-> Ingrese opcion: ";
    $opcion = trim(fgets(STDIN));
    switch ($opcion)
    {
        case 1:
            // Ingresar una empresa
            echo "Ingrese el nombre de la empresa: ";
            $nomEmpresa = trim(fgets(STDIN));
            echo "Ingrese la dirección de la empresa: ";
            $dirEmpresa = trim(fgets(STDIN));
            $objEmpresa -> cargar("", $nomEmpresa, $dirEmpresa);
            if ($objEmpresa -> insertar())
            {
                echo "- - - - Se cargo la empresa en la BD con exito - - - -\n\n";
            }
            else
            {
                echo "Error no se pudo cargar la empresa a la BD \n\n";
            }
        break;
        case 2:  
            // Eliminar una empresa
            echo "Lista de empresas: \n";
            verDatos ($objEmpresa, "");       
            echo "Ingrese el ID de la empresa que quiere eliminar: \n"; 
            echo "Nota: Al eliminar una empresa si esta tiene viajes, pasajeros y responsables estos se borraran tambien. ";
            $idSeleccionado = trim(fgets(STDIN));
            if ($objEmpresa -> buscar($idSeleccionado))
            {
                $condicion = "idempresa=".$objEmpresa -> getIdEmpresa ();
                $colViajes = $objViaje -> listar ($condicion);
                if ($colViajes <> [])
                {
                    // Entro aca si hay por lo menos 1 viaje cargado.
                    foreach ($colViajes as $viaje)
                    {
                        $objResponsable = $viaje -> getObjResponsableV ();
                        $nroEmpleado = $objResponsable -> getNroEmpleado();
                        $objResponsable -> buscar ($nroEmpleado);
                        $colPasajeros = $objPasajero -> listar ();
                        if ($colPasajeros == [])
                        {
                            // Si no hay pasajeros cargados borro el viaje y el responsable
                            $objResponsable -> eliminar ();
                            $viaje -> eliminar();    
                        }
                        else
                        {
                            foreach ($colPasajeros as $pasajero)
                            {
                                // Si hay pasajeros cargados borro todos los pasajeros
                                $pasajero -> eliminar();
                            }
                            // Borro solo los responsables que son responsables de 1 viaje solamente. No borro los que no son responsables ningun viaje.
                            $objResponsable -> eliminar ();
                            $viaje -> eliminar(); 
                        }
                    }
                    $objEmpresa -> eliminar();
                    echo "- - - - Se ha borrado la empresa de la BD con exito - - - -\n\n";
                }
                else
                {
                    // Entro a este else si la colViajes es vacia, es decir no hay viajes, responsable, ni pasajeros cargados.
                    $objEmpresa -> eliminar();
                    echo "- - - - Se ha borrado la empresa de la BD con exito - - - -\n\n";
                }  
            }
            else
            {
                echo "Error, no existe una empresa con ese ID \n\n";
            }
        break;
        case 3:
            // Modificar una empresa
            echo "Lista de empresas: \n";
            verDatos($objEmpresa, "");
            echo "Ingrese el ID de la empresa que quiere modificar ";
            $idSeleccionado = trim(fgets(STDIN));
            if ($objEmpresa -> buscar($idSeleccionado))
            {
                echo "Ingrese el nuevo nombre de la empresa: ";
                $nuevoNom = trim(fgets(STDIN));
                echo "Ingrese la nueva direccion de la empresa: ";
                $nuevaDir = trim(fgets(STDIN));
                $objEmpresa -> cargar ($idSeleccionado, $nuevoNom, $nuevaDir);
                $objEmpresa -> modificar();
                echo "- - - - Se modifico la empresa con exito - - - -\n\n";
            }
            else
            {
                echo "Error, no existe una empresa con ese ID \n\n";
            }
        break;
        case 4:
            // Ver las empresas cargadas en la BD
            if ($objEmpresa -> listar () == [])
            {
                echo "No hay empresas cargadas en la BD \n\n";
            }
            else
            {
                verDatos($objEmpresa, "");
            }
        break;
        case 5:
            // Seleccionar una empresa para administrar sus viajes
            echo "Lista de empresas: \n";
            verDatos($objEmpresa, "");
            echo "Ingrese el ID de la empresa que quiere administrar sus viajes: ";
            $idSeleccionado = trim(fgets(STDIN));
            if ($objEmpresa -> buscar ($idSeleccionado))
            {
                do
                {
                    echo "\n ----------- Menú de viajes ----------- \n";
                    echo " - - - - - - - - - - - - - - - - - - - \n";
                    echo " | Empresa seleccionada: ".$objEmpresa -> getNombre()." |\n";
                    echo " - - - - - - - - - - - - - - - - - - - \n";
                    echo " --------------- Viajes --------------- \n";
                    echo " | <1> Ingresar un viaje de la empresa \n";
                    echo " | <2> Eliminar un viaje de la empresa \n";
                    echo " | <3> Modificar un viaje de la empresa \n";
                    echo " | <4> Mostrar los viajes de la empresa \n";
                    echo " ------------ Responsables ------------ \n";
                    echo " | <5> Ingresar un responsable del viaje \n";
                    echo " | <6> Eliminar un responsable del viaje \n"; 
                    echo " | <7> Modificar un responsable del viaje \n";
                    echo " | <8> Mostrar los responsables cargados \n";
                    echo " ------------- Pasajeros -------------- \n";
                    echo " | <9> Ingresar pasajeros \n";
                    echo " | <10> Eliminar pasajeros \n";
                    echo " | <11> Modificar un pasajero \n"; 
                    echo " | <12> Mostrar los pasajeros cargados \n";
                    echo " -------------------------------------- \n";
                    echo " | <13> Salir del submenú \n";
                    echo " | <-> Ingrese opcion: ";
                    $opcionSubmenu = trim(fgets(STDIN));
                    switch ($opcionSubmenu)
                    {
                        case 1:
                            // Ingresar un viaje
                            $colResponsables = $objResponsable -> listar ();
                            if ($colResponsables == [])
                            {
                                echo "No hay responsables cargados, para poder cargar un viaje cargue un responsable \n";
                            }
                            else
                            {
                                echo "Ingrese el destino del viaje: ";
                                $destinoV = trim(fgets(STDIN));
                                echo "Ingrese el importe del viaje: ";
                                $importeV = trim(fgets(STDIN));
                                echo "Ingrese la cantidad máxima de pasajeros del viaje: ";
                                $cantMaxPasajerosV = trim(fgets(STDIN));
                                if ($cantMaxPasajerosV <= 0)
                                {
                                    echo "Numero de pasajeros no valido \n";
                                }
                                else
                                {
                                    echo "Datos de los responsables: \n";
                                    verDatos ($objResponsable, "");
                                    echo "Ingrese el numero de empleado del responsable del viaje: ";
                                    $numEmpleadoV = trim(fgets(STDIN));
                                    if ($objResponsable -> buscar ($numEmpleadoV))
                                    {
                                        $objViaje -> cargar ("", $destinoV, $cantMaxPasajerosV, $objEmpresa, $objResponsable, $importeV);
                                        $objViaje -> insertar ();
                                        echo "- - - - Se cargo el viaje con exito en la BD - - - -\n";
                                    }
                                    else
                                    {
                                        echo "No existe el número de empleado del responsable \n";
                                    }
                                }
                            }
                        break;
                        case 2:
                            // Eliminar un viaje 
                            $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
                            $colViajes = $objViaje -> listar($condicion);
                            if ($colViajes == [])
                            {
                                echo "No hay viajes cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los viajes: \n";
                                verDatos ($objViaje, $condicion);
                                echo "Ingrese el ID del viaje que desea eliminar: ";
                                $idSeleccionado = trim(fgets(STDIN));
                                if ($objViaje -> buscar ($idSeleccionado))
                                {
                                    $condicion = "idviaje=".$objViaje -> getIdViaje();
                                    $colPasajeros = $objPasajero -> listar ($condicion);
                                    if ($colPasajeros == [])
                                    {
                                        // Si no hay pasajeros borro el viaje
                                        $objViaje -> eliminar($idSeleccionado);
                                        echo "- - - - Se borro con exito el viaje de la BD - - - -\n";
                                    }
                                    else
                                    {
                                        // Si hay por lo menos 1 pasajero borro el pasajero y luego el viaje
                                        foreach ($colPasajeros as $pasajero)
                                        {
                                            $pasajero -> eliminar ();
                                        }
                                        $objViaje -> eliminar($idSeleccionado);
                                        echo "- - - - Se borro con exito el viaje de la BD - - - -\n";
                                    }
                                }
                                else
                                {
                                    echo "No existe el viaje con ese ID \n";
                                }
                            }    
                        break;
                        case 3:
                            // Modificar un viaje
                            $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
                            $colViajes = $objViaje -> listar($condicion);
                            if ($colViajes == [])
                            {
                                echo "No hay viajes cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los viajes: \n";
                                verDatos($objViaje, $condicion);
                                echo "Ingrese el ID del viaje que desea modificar: ";
                                $idSeleccionado = trim(fgets(STDIN));
                                if ($objViaje -> buscar ($idSeleccionado))
                                {
                                    echo "Ingrese el nuevo destino: ";
                                    $nuevoDestinoV = trim(fgets(STDIN));
                                    $condicion = "idviaje=".$idSeleccionado;
                                    $colPasajeros = $objPasajero -> listar($condicion);
                                    $pasajerosCargados = count($colPasajeros);
                                    echo "Ingrese la nueva cantidad máxima de pasajeros: ";
                                    $nuevaCantMaxPasajerosV = trim(fgets(STDIN));
                                    if ($nuevaCantMaxPasajerosV < $pasajerosCargados)
                                    {
                                        echo "Error, la nueva cantidad de pasajeros no puede ser menor a los pasajeros ya cargados \n";
                                    }
                                    else
                                    {
                                        echo "Ingrese el nuevo importe del viaje: ";
                                        $nuevoImporteV = trim(fgets(STDIN));
                                        echo "Lista de empresas: \n";
                                        verDatos ($objEmpresa, "");
                                        echo "Ingrese el nuevo ID de la empresa del viaje: ";
                                        $nuevoIdEmpresa = trim(fgets(STDIN));
                                        if ($empresaSeleccionada -> buscar ($nuevoIdEmpresa))
                                        {
                                            echo "Lista de responsables \n";
                                            verDatos ($objResponsable, "");
                                            echo "Ingrese el nuevo numero de empleado del responsable: ";
                                            $nuevoNumEmpleado = trim(fgets(STDIN));
                                            if ($objResponsable -> buscar ($nuevoNumEmpleado))
                                            {
                                                $objViaje -> cargar ($idSeleccionado, $nuevoDestinoV, $nuevaCantMaxPasajerosV, $empresaSeleccionada, $objResponsable, $nuevoImporteV);
                                                $objViaje -> modificar();
                                                echo "- - - - Viaje modificado con exito - - - -\n";
                                            }
                                            else
                                            {
                                                echo "No se encontro el número de empleado del responsable \n";
                                            }
                                        }
                                        else
                                        {
                                            echo "No existe el ID de la empresa que escribio \n";
                                        }
                                    
                                    }
                                }
                                else
                                {
                                    echo "No existe el viaje con ese ID \n";
                                }
                            }
                        break;
                        case 4:
                            // Mostrar los viajes de la empresa
                            echo "Datos de los viajes: \n";
                            $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
                            $colViajes = $objViaje -> listar($condicion);
                            foreach ($colViajes as $viaje)
                            {
                                echo "\n-------------------------------------------------------\n";
                                echo $viaje;
                                echo "\n-------------------------------------------------------\n";
                            }
                            if ($colViajes == [])
                            {
                                echo "No hay viajes cargados en esta empresa \n";
                            }
                        break;
                        case 5:
                            // Ingresar un responsable del viaje
                            echo "Ingrese el nombre del responsable: ";
                            $nombreR = trim(fgets(STDIN));
                            echo "Ingrese el apellido del responsable: ";
                            $apellidoR = trim(fgets(STDIN));
                            echo "Ingrese el número de licencia del responsable: ";
                            $numLicR = trim(fgets(STDIN));
                            $objResponsable -> cargar ("", $numLicR, $nombreR, $apellidoR);
                            if ($objResponsable -> insertar ())
                            {
                                echo "- - - - El responsable del viaje fue cargado con exito - - - -\n";
                            }
                            else
                            {
                                echo "Error, no se ha podido cargar el responsable del viaje \n";
                            }
                        break;
                        case 6:
                            // Eliminar un responsable del viaje
                            $colResponsables = $objResponsable -> listar ();
                            if ($colResponsables == [])
                            {
                                echo "No hay responsables cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Lista de responsables \n";
                                verDatos ($objResponsable, "");
                                echo "NOTA: Al eliminar un responsable del viaje se borraran los viajes en los que este sea responsable \n";
                                echo "Ingrese el número de empleado del responsable que quiere eliminar: ";
                                $numEmpleadoSeleccionado = trim(fgets(STDIN));
                                if ($objResponsable -> buscar ($numEmpleadoSeleccionado))
                                {
                                    // Entro aca si el responsable existe
                                    $colViajes = $objViaje -> listar ();
                                    if ($colViajes <> [])
                                    {
                                        // Entro aca si hay por lo menos 1 viaje cargado
                                        foreach ($colViajes as $viaje)
                                        {
                                            $numEmpleadoViaje = $viaje -> getObjResponsableV () -> getNroEmpleado ();
                                            if ($numEmpleadoSeleccionado == $numEmpleadoViaje)
                                            {
                                                // Entro aca si en el viaje cargado el responsable es el responsable a borrar
                                                $colPasajeros = $objPasajero -> listar();
                                                if ($colPasajeros <> [])
                                                {
                                                    // Entro aca si hay por lo menos 1 pasajero cargado
                                                    foreach ($colPasajeros as $pasajero)
                                                    {
                                                        if ($pasajero -> getObjViaje() -> getIdViaje() == $viaje -> getIdViaje ())
                                                        {
                                                            // Entro aca si el pasajero es del viaje en el que esta el responsable a borrar
                                                            $pasajero -> eliminar ();
                                                        }
                                                    }
                                                    $viaje -> eliminar ();
                                                }
                                                else
                                                {
                                                    $viaje -> eliminar ();
                                                }
                                            }
                                        }
                                        $objResponsable -> eliminar();    
                                        echo "- - - - Se elimino el responsable con exito - - - -\n"; 
                                    }
                                    else
                                    {
                                        $objResponsable -> eliminar();
                                        echo "- - - - Se elimino el responsable con exito - - - -\n";
                                    } 
                                }             
                                else
                                {
                                    echo "No existe el responsalble con ese número de empleado \n";
                                }   
                            }
                        break;
                        case 7:
                            // Modificar un responsable
                            $colResponsables = $objResponsable -> listar();
                            if ($colResponsables == [])
                            {
                                echo "No hay responsables cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los responsables: \n";
                                verDatos ($objResponsable, "");
                                echo "Ingrese el número de empleado del responsable que quiere modificar: ";
                                $numEmpleadoSeleccionado = trim(fgets(STDIN));
                                if ($objResponsable -> buscar ($numEmpleadoSeleccionado))
                                {
                                    echo "Ingrese el nuevo nombre del responsable: ";
                                    $nuevoNom = trim(fgets(STDIN));
                                    echo "Ingrese el nuevo apellido del responsable: ";
                                    $nuevoAp = trim(fgets(STDIN));
                                    echo "Ingrese el nuevo numero de licencia del responsable: ";
                                    $nuevoNumLicR = trim(fgets(STDIN));
                                    $objResponsable -> cargar ($numEmpleadoSeleccionado, $nuevoNumLicR, $nuevoNom, $nuevoAp);
                                    $objResponsable -> modificar ();
                                    echo "- - - - Se modificó el responsable con exito - - - -\n";
                                }
                                else
                                {
                                    echo "No existe el responsalble con ese número de empleado \n";
                                }
                            }
                        break;
                        case 8:
                            // Ver los responsables cargados
                            $colResponsables = $objResponsable -> listar();
                            if ($colResponsables == [])
                            {
                                echo "No hay responsables cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los responsables: \n";
                                verDatos($objResponsable, "");
                            }
                        break;
                        case 9:
                            // Cargar pasajeros
                            $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
                            $colViajes = $objViaje -> listar($condicion);
                            if ($colViajes == [])
                            {
                                echo "No hay viajes cargados en esta empresa para agregar pasajeros \n";
                            }
                            else
                            { 
                                echo "Ingrese la cantidad de pasajeros que desea cargar: ";
                                $pasajerosACargar = trim(fgets(STDIN)); 
                                echo "Lista de viajes: \n";
                                $condicion = "idempresa=".$objEmpresa->getIdEmpresa();
                                verDatos ($objViaje, $condicion);
                                echo "Ingrese el ID del viaje al que pertenece: ";
                                $idViajePasajero = trim(fgets(STDIN));
                                if ($objViaje -> buscar ($idViajePasajero))
                                {
                                    if ($objViaje -> getObjEmpresa () -> getIdEmpresa () == $objEmpresa -> getIdEmpresa())
                                    {
                                        $condicion = "idviaje=".$idViajePasajero;
                                        $colPasajeros = $objPasajero -> listar($condicion);
                                        $pasajerosCargados = count($colPasajeros);
                                        $maxPasajerosViaje = $objViaje -> getCantMaxPasajeros();
                                        $asientosDisponibles = $maxPasajerosViaje - $pasajerosCargados; 
                                        if ($maxPasajerosViaje == $pasajerosCargados)
                                        {
                                            echo "Ya se alcanzó la capacidad máxima de este viaje. \n";
                                        }
                                        elseif ($pasajerosACargar > $maxPasajerosViaje)
                                        {
                                            echo "Error, no se puede cargar más pasajeros que la cantidad máxima de pasajeros del viaje. \n"; 
                                        }
                                        elseif ($pasajerosACargar > $asientosDisponibles)
                                        {
                                            echo "Error, no se puede cargar más pasajeros que los asientos disponibles. \n";
                                        }
                                        elseif ($asientosDisponibles >= $pasajerosACargar) 
                                        {
                                            echo "Ingrese ".$pasajerosACargar." pasajeros: \n";
                                            for ($j = 1; $j <= $pasajerosACargar; $j++)
                                            {
                                                echo "\n- - - Pasajero N° ".($j+$pasajerosCargados)." - - -\n";
                                                echo "Ingrese el nombre del pasajero: ";
                                                $pNombre = trim(fgets(STDIN));
                                                echo "Ingrese el apellido del pasajero: ";
                                                $pApellido = trim(fgets(STDIN));
                                                echo "Ingrese el telefono del pasajero: ";
                                                $pTelefono = trim(fgets(STDIN));
                                                echo "Ingrese el documento del pasajero: ";
                                                $pDocumento = trim(fgets(STDIN));
                                                if ($objPasajero -> buscar($pDocumento))
                                                {
                                                    echo "Error, el pasajero ya esta cargado en un viaje \n";
                                                    $j --;
                                                }
                                                else
                                                {
                                                    $costoFinal = $objViaje -> venderPasaje ($pDocumento, $pNombre, $pApellido, $pTelefono, $objViaje);
                                                    echo "\nPasajero cargado, usted debe pagar $ ".$costoFinal;
                                                }
                                            }
                                            echo "\n- - - - Se han cargado ".$pasajerosACargar." pasajeros - - - -\n"; 
                                        }
                                    }
                                    else
                                    {
                                        echo "Ese viaje pertenece a otra empresa \n";
                                    }   
                                }   
                                else
                                {
                                    echo "Error, no existe un viaje con ese ID \n";
                                }                                                           
                            }
                        break;
                        case 10:
                            // Eliminar pasajeros
                            $colPasEmp = devolverColPasajeros ($objEmpresa, $objViaje, $objPasajero);
                            if ($colPasEmp == [])
                            {
                                echo "No hay pasajeros cargados en esta empresa \n";
                            }
                            else
                            {     
                                echo "Datos de los pasajeros: \n";
                                foreach ($colPasEmp as $pasajero)
                                {
                                    echo "\n-------------------------------------------------------\n";
                                    echo $pasajero;
                                    echo "\n-------------------------------------------------------\n";
                                }
                                echo "\nIngrese el documento del pasajero que quiere eliminar: ";
                                $docSeleccionado = trim(fgets(STDIN));
                                if ($objPasajero -> buscar ($docSeleccionado))
                                {
                                    if (in_array($objPasajero, $colPasEmp))
                                    {
                                        $objPasajero -> eliminar($docSeleccionado);
                                        echo "\n- - - - Se elimino el pasajero de la BD con exito - - - -\n";
                                    }
                                    else
                                    {
                                        echo "El pasajero no corresponde a esta empresa \n";
                                    }
                                }
                                else
                                {
                                    echo "No existe un pasajero con ese documento \n";
                                }
                            }
                        break;
                        case 11:
                            // Modificar un pasajero
                            // Para cambiar el dni podria eliminar y luego cargar.
                            $colPasEmp = devolverColPasajeros ($objEmpresa, $objViaje, $objPasajero);
                            if ($colPasEmp == [])
                            {
                                echo "No hay pasajeros cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los pasajeros: \n";
                                foreach ($colPasEmp as $pasajero)
                                {
                                    echo "\n-------------------------------------------------------\n";
                                    echo $pasajero;
                                    echo "\n-------------------------------------------------------\n";
                                }
                                echo "\nIngrese el documento del pasajero que quiere modificar: ";
                                $docSeleccionado = trim(fgets(STDIN));
                                if ($objPasajero -> buscar ($docSeleccionado))
                                {
                                    if (in_array($objPasajero, $colPasEmp))
                                    {
                                        $condicion = "idempresa=".$objEmpresa -> getIdEmpresa();
                                        echo "Ingrese el nuevo nombre del pasajero: ";
                                        $nuevoNom = trim(fgets(STDIN));
                                        echo "Ingrese el nuevo apellido del pasajero: ";
                                        $nuevoAp = trim(fgets(STDIN));
                                        echo "Ingrese el nuevo telefono del pasajero: ";
                                        $nuevoTel = trim(fgets(STDIN));
                                        verDatos ($objViaje, $condicion);
                                        echo "Ingrese el nuevo ID del viaje: ";
                                        $nuevoIdViaje = trim(fgets(STDIN));
                                        if ($objViaje -> buscar ($nuevoIdViaje))
                                        {
                                            if ($objViaje -> getObjEmpresa() -> getIdEmpresa() <> ($objEmpresa -> getIdEmpresa()))
                                            {
                                                echo "Ese viaje no pertenece a esta empresa \n";
                                            }
                                            else
                                            { 
                                                echo "¿ Quiere cambiar el documento del pasajero (s/n) ? ";
                                                $respuesta = trim(fgets(STDIN));
                                                if ($respuesta == "s")
                                                {
                                                    echo "Ingrese el nuevo documento: ";
                                                    $nuevoDoc = trim(fgets(STDIN));
                                                    if (!$objPasajero -> buscar ($nuevoDoc))
                                                    {
                                                        $objPasajero -> eliminar ();
                                                        $objPasajero -> cargar ($nuevoDoc, $nuevoNom, $nuevoAp, $nuevoTel, $objViaje);
                                                        $objPasajero -> insertar ();
                                                        echo "\n- - - - Se modifico el pasajero de la BD con exito - - - -\n";
                                                    }
                                                    else
                                                    {
                                                        echo "Ese documento ya existe \n";
                                                    }
                                                }
                                                else
                                                {
                                                    echo "Usted eligio no modificar el documento \n";
                                                    $objPasajero -> cargar ($docSeleccionado, $nuevoNom, $nuevoAp, $nuevoTel, $objViaje);
                                                    $objPasajero -> modificar ();
                                                    echo "\n- - - - Se modifico el pasajero de la BD con exito - - - -\n";
                                                }
                                            }
                                        }
                                        else
                                        {
                                            echo "No existe ese ID de viaje \n";
                                        } 
                                    }
                                    else
                                    {
                                        echo "El pasajero no pertence a esta empresa \n";
                                    }                               
                                }
                                else
                                {
                                    echo "No existe ese documento de pasajero \n";
                                }
                            }
                        break;
                        case 12:
                            // Mostrar los pasajeros
                            $colPasEmp = devolverColPasajeros ($objEmpresa, $objViaje, $objPasajero);
                            if ($colPasEmp == [])
                            {
                                echo "No hay pasajeros cargados en esta empresa \n";
                            }
                            else
                            {
                                echo "Datos de los pasajeros: \n";
                                foreach ($colPasEmp as $pasajero)
                                {
                                    echo "\n-------------------------------------------------------\n";
                                    echo $pasajero;
                                    echo "\n-------------------------------------------------------\n";
                                }
                            }
                        break;
                    }
                } while ($opcionSubmenu <> 13);
            }
            else
            {
                echo "Error, no existe una empresa con ese ID \n\n";
            }
        break;  
    }
} while ($opcion <> 6);