<?php
class Crud {
    private $query;
    private $parseString;
    private $result;
    private $error;
    private $conexion;
    private $table;
    private $terms;
    private $data;
  
    public function create($table, array $data) {
        $this->table = (string) $table;
        $this->data = $data;
        $this->prepareData('create');
    }

    public function read($table, $terms = NUll, $parseString = NULL) {
        $this->table = (string) $table ? $table : NULL;
        $this->terms = (string) $terms ? $terms : NULL;
        if(!empty($parseString)):
            $this->parseString = (string) $parseString ? $parseString : NULL;
            parse_str($parseString, $this->parseString);
            $this->prepareData('read');
        else:
            $this->prepareData('read');
        endif;
    }

    public function fullRead($query, $parseString = NULL) {
        $this->query = (string) $query;
        if(!empty($parseString)):
            $this->parseString = (string) $parseString ? $parseString : NULL;
            parse_str($parseString,$this->parseString);
            $this->prepareData('read');
        else:
            $this->prepareData('read');
        endif;
    }

    public function update($table,$terms,array $data,$parseString) {
        $this->table = (string) $table;
        $this->terms = (string)$terms;
        $this->data = $data;
        $this->parseString = (string) $parseString;
        parse_str($parseString,$this->parseString);
        $this->prepareData('update');
    }

    public function delete($table,$terms,$parseString) {
        $this->table = (string) $table;
        $this->terms = (string)$terms;
        $this->parseString = (string) $parseString;
        parse_str($parseString,$this->parseString);
        $this->prepareData('delete');
    }

    public function getResult() {
        return $this->result;
    }
    public function getError() {
        return $this->error;
    }

    //PRIVATES METHODS

    private function getConn() {
        $this->conexion = Connection::getInstance();
        $this->conexion = $this->conexion->getConn();
        return $this->conexion;
    }

    private function getSyntax($type){
        switch ($type):
            case 'read':
                if(empty($this->query)):
                    $this->query = "SELECT * FROM {$this->table} {$this->terms}";
                endif;
            break;

            case 'create':
                $this->parseString = '';
                $this->terms = '';

                foreach($this->data as $key => $values):
                    $this->parseString .= "{$key}, ";
                    $this->terms .= ":{$key}, ";
                endforeach;

                $this->parseString = substr($this->parseString, 0, -2);
                $this->terms = substr($this->terms, 0, -2);
                $this->query = "INSERT INTO {$this->table} ($this->parseString) VALUES ($this->terms)";
            break;

            case 'update':
                $bind = '';
                foreach($this->data as $key => $values):
                    $bind .= "{$key} = :{$key}, ";
                endforeach;
                $bind= substr($bind, 0, -2);
                $this->query = "UPDATE {$this->table} SET $bind {$this->terms}";
            break;

            case 'delete':
                $this->query = "DELETE FROM {$this->table} {$this->terms}";
            break;
        endswitch;
    }

    private function setBinds(array $dados,array $parseString = NULL) {
        if(!empty($parseString)):
            $this->data = array_merge($this->data,$this->parseString);
            foreach($this->data as $key => $value):
                $this->query->bindValue(":{$key}",$value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        else:
            foreach($dados as $key => $value):
                $this->query->bindValue(":{$key}",$value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        endif;
    }

    private function execute() {
        $this->query->execute(); 
        if($this->query->rowCount() > 0):
            $this->result = $this->query->fetchAll();
            $this->error = FALSE;
        else:
            $this->result = NULL;
            $this->error = TRUE;
        endif;
    }

    private function prepareData($type) {
        
        switch($type):
            case 'read':
                if(!empty($this->parseString)):
                    $this->getSyntax($type);
                    $this->query = $this->getConn()->prepare($this->query);
                    $this->setBinds($this->parseString);
                    $this->execute();
                else:
                    $this->getSyntax($type);
                    $this->query = $this->getConn()->query($this->query);

                    if($this->query->rowCount() > 0):
                        $this->result = $this->query->fetchAll();
                        $this->error = FALSE;
                    else:
                        $this->result = NULL;
                        $this->error = TRUE;
                    endif;
                endif;
            break;
            case 'create':
                $this->getSyntax($type);
                $this->query = $this->getConn()->prepare($this->query);
                $this->setBinds($this->data);
                $this->execute();
            break;
            case 'update':
                $this->getSyntax($type);
                $this->query = $this->getConn()->prepare($this->query);
                $this->setBinds($this->data,$this->parseString);
                $this->execute();
            break;
            case 'delete':
                $this->getSyntax($type);
                $this->query = $this->getConn()->prepare($this->query);
                $this->setBinds($this->parseString);
                $this->execute();
            break;
        endswitch;
    }


}