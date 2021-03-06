<?php
class Connection {
    private static $conexion;
    private static $instance;
    private static $data;

    private function __construct() {
        self::$data = parse_ini_file('./config/config.ini');
    }

    public static function getInstance() {
        if(empty(self::$instance)):
            self::$instance = new self;
        endif;
        return self::$instance;
    }

    public function getConn() {
        if(empty(self::$conexion):
            $dsn = "{$this->getConfig('db')}:dbname={$this->getConfig('dbname')};host={$this->getConfig('host')}";
            $charset = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
            try{
                self::$conexion = new PDO($dsn,$this->getConfig('user'),$this->getConfig('password'),$charset);
            }catch(PDOException $e){
                echo "Error: {$e->getMessage()}";
            }   
        endif;
        return self::$conexion;
        
    }

    private function getConfig($key) {
        return self::$data[$key];
    }
}
