<?php
class Auto
{
    public $patente;
    public $date;
    public $email;
    public $fecha_egreso;
    public $importe;

    function __construct($patente, $date, $email, $fecha_egreso = '', $importe = 0)
    {
        $this->patente = $patente;
        $this->date = $date;
        $this->email = $email;
        $this->fecha_egreso = $fecha_egreso;
        $this->importe = $importe;
    }
    /* Métodos mágicos */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
    public  function Save($patente)
    {
        $retorno=null;
        if (Auto::ValidarPatenteRepetida($patente)==false)
        {
            $lista= Archivos::GuardarJson("archivosGuardados/autos.json",$this);
            $retorno=$lista;
        }
        return $retorno;
    }

    public static function ValidarPatenteRepetida($patente)
    { 
        $listaJson=Archivos::TraerJson("archivosGuardados/autos.json");
        $retorno=false;

        if ($listaJson!=false) {
            foreach ($listaJson as $value) {
            
                if ($value->patente == $patente) {
                     $retorno = true;
                     break;
                }
                  
             }
        }   
         return $retorno;

    }
    public static function update($object)
    {
        if (!Auto::isValidUnique($object->patente)) {
            $archivoArray = (array) JsonHandler::readJson('Autos.json');
            $listaAutos = [];

            foreach ($archivoArray as $datos) {
                $nuevoAuto = new Auto($datos->patente, $datos->tipo, $datos->date, $datos->email, $datos->fecha_egreso, $datos->importe);

                if ($nuevoAuto->patente == $object->patente) {
                    array_push($listaAutos, $object);
                } else {
                    array_push($listaAutos, $nuevoAuto);
                }
            }

            // Update.
            JsonHandler::saveAllJson($listaAutos, 'Autos.json');
        }

        return false;
    }

    public static function isValidUnique($unique)
    {
        $autos = Auto::getAll();
        foreach ($autos as $auto) {
            if ($auto->patente == $unique) {
                return false;
            }
        }

        return true;
    }

    public static function getAll()
    {
        $archivoArray = (array) JsonHandler::readJson('Autos.json');
        $listaAutos = [];

        foreach ($archivoArray as $datos) {
            $nuevoAuto = new Auto($datos->patente, $datos->tipo, $datos->date, $datos->email, $datos->fecha_egreso, $datos->importe);

            // Sólo los estacionados, no los retirados.
            if ($nuevoAuto->importe == 0 && $nuevoAuto->fecha_egreso == '')
                array_push($listaAutos, $nuevoAuto);
        }

        sort($listaAutos);

        return $listaAutos;
    }

    public static function sumAllByType($tipo)
    {
        $archivoArray = (array) JsonHandler::readJson('Autos.json');
        $suma = 0;

        foreach ($archivoArray as $datos) {
            $nuevoAuto = new Auto($datos->patente, $datos->tipo, $datos->date, $datos->email, $datos->fecha_egreso, $datos->importe);

            // Sólo los estacionados, no los retirados.
            if ($nuevoAuto->tipo == $tipo)
                $suma = $suma + $nuevoAuto->importe;
        }

        return $suma;
    }

    public static function getByPatente($patente)
    {
        $archivoArray = (array) JsonHandler::readJson('Autos.json');

        foreach ($archivoArray as $datos) {
            $nuevoAuto = new Auto($datos->patente, $datos->tipo, $datos->date, $datos->email);

            if ($nuevoAuto->patente == $patente) {
                return $nuevoAuto;
            }
        }

        return null;
    }

    public static function exists($patente)
    {
        $archivoArray = (array) JsonHandler::readJson('Autos.json');

        foreach ($archivoArray as $datos) {
            $nuevoAuto = new Auto($datos->patente, $datos->tipo, $datos->date, $datos->email);

            if ($nuevoAuto->patente == $patente) {
                return true;
            }
        }

        return false;
    }

    
}

?>