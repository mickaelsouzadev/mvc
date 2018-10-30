<?php

/* PHP Class for connecting to database
 * AUTHOR: Antony Acosta
 * LAST EDIT: 2018-10-29
 */
class Connection {
    
    private $connectionString;
    private $user;
    private $password;
    
    public function __construct($user, $password, $dbname, $host, $charset = "utf-8")
    {
        $this->connectionString = "mysql:host={$host};dbname={$dbname};charset={$charset}";
        $this->user     = $user;
        $this->password = $password;
    }
    
    public function exec($query, $callback, $params = null)
    {
        try{
            
            $connection = new PDO($this->connectionString, $this->user, $this->password);
            $preparedStatement = $connection->prepare($query);
            
            if($parameters !== null){
                foreach($parameters as $key=>$value){
                    $preparedStatement->bindValue($key,$value);
                }
            }
            
            if($preparedStatement->execute() === false){
                throw new PDOException($preparedStatement->errorCode());
            }
            
            if(method_exists($preparedStatement,$callback)){
                $return = $preparedStatement->$callback();
                
            }elseif(method_exists($connection, $callback)){
                $return = $connection->$callback();
                
            }else{
                $return = $preparedStatement->rowCount();
            }            
            
            $connection->commit();
            return $return;
            
        }catch(PDOException $exc){
            if(isset($connection) && $connection->inTransaction()){
                $connection->rollBack();
            }
            die("Error: ".$exc->getCode()." ".$exc->getMessage());
        }
    }
    
    
    
    
}
