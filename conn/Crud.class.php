<?php
class Crud {
    private $query;
    private $parseString;
    private $result;
    private $error;
    private $conexion;
    private $table;
    private $terms;

    public function read($table, $terms = NUll, $parseString = NULL) {
        $this->table = (string) $table ? $table : NULL;
        $this->terms = (string) $terms ? $terms : NULL;
        if(!empty($parseString)):
            $this->parseString = (string) $parseString ? $parseString : NULL;
            parse_str($parseString, $this->parseString);
            $this->execute();
        else:
            $this->execute();
        endif;
    }

    public function fullRead($query, $parseString = NULL) {
        $this->query = (string) $query;
        if(!empty($parseString)):
            $this->parseString = (string) $parseString ? $parseString : NULL;
            parse_str($parseString,$this->parseString);
            $this->execute();
        else:
            $this->execute();
        endif;
    }

    public function getResult() {
        return $this->result;
    }
    public function getError() {
        return $this->error;
    }

    private function getConn() {
        $this->conexion = Conexion::getInstance();
        $this->conexion = $this->conexion->getConn();
        return $this->conexion;
    }

    private function getSyntax(){
        if(empty($this->query)):
            $this->query = "SELECT * FROM {$this->table} {$this->terms}";
        endif;
    }


    private function execute() {
        
        if(!empty($this->parseString)):
            $this->getSyntax();
            $this->query = $this->getConn()->prepare($this->query);

            foreach($this->parseString as $key => $value):
                $this->query->bindValue(":{$key}",$value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
            
            $this->query->execute();
            
            if($this->query->rowCount() > 0):
                $this->result = $this->query->fetchAll();
                $this->error = FALSE;
            else:
                $this->result = NULL;
                $this->error = TRUE;
            endif;

        else:
            $this->getSyntax();
            $this->query = $this->getConn()->query($this->query);

            if($this->query->rowCount() > 0):
                $this->result = $this->query->fetchAll();
                $this->error = FALSE;
            else:
                $this->result = NULL;
                $this->error = TRUE;
            endif;

        endif;
        
    }

}