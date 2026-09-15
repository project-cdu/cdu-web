<?php

class Agent extends Controller
{
    public function index()
    {
        return view('agent.index');
    }

    public function GetorInsert()
    {
      if($variavel == null) return;
      
       $query = 'Select * from table where column = ?';
        
       // Implementation for Get or Insert logic
    }
}