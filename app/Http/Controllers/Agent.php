<?php

class Agent extends Controller
{

    public function __construct()
    {
        
    }
    public function __GET($atributo)
    {
        if (!property_exists($this, $atributo)) {
            throw new Exception("Atributo $atributo não existe na classe Datajud");
        }
        return $this->$atributo;
    }

    public function __SET($atributo, $valor)
    {
        if (!property_exists($this, $atributo)) {
            throw new Exception("Atributo $atributo não existe na classe Datajud");
        }
        $this->$atributo = $valor;
    }
    public function GetorInsert()
    {
      if($variavel == null) return;
      
       $query = 'Select * from table where column = ?';
        
       // Implementation for Get or Insert logic
    }
}