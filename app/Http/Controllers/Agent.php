<?php

class Agent
{

    public function __construct()
    {

    }
    public function __GET($attribute)
    {
        if (!property_exists($this, $attribute)) {
            throw new Exception("Atributte $attribute not exists in  the class  Agent");
        }
        return $this->$attribute;
    }
    public function __SET($attribute, $value)
    {
        if (!property_exists($this, $attribute)) {
            throw new Exception("Atributte $attribute not exists in  the class  Agent");
        }
        $this->$attribute = $value;
    }
    public function GetorInsert()
    {
      if($variavel == null) return;
      
       $query = 'Select * from table where column = ?' PDO ;
        

       // Implementation for Get or Insert logic
    }
}