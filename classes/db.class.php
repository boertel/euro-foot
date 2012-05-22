<?php

require_once 'settings/dev.php';

/*
 * @date : 26/10/2009 20:13
 * @author : Benjamin Oertel
 * @version : 1.0-DEV
 * @description : classe pour se connecter à la base de données 
 * /!\ - VERSION A UTILISER POUR LE DEVELOPPEMENT ET LE DEPLOIEMENT - 
 */

class MyException extends Exception {

	public function __construct($info, $infos=false) {
		parent::__construct($info);
		$this->info = $info;
	}
	
	public function getTime() {
		return date('Y-m-d H:i:s');
    }
	
	public function __toString() {
		$view .= "<b>" . $this->info . "</b>\n";
		if($this->infos) {
			$view .= "<i>[ " . $this->getFile() . ", l. " . $this->getLine() . " ]</i>\n";
		}
		return $view;
	}
}

class InvalidArrayException extends MyException {}
class AccesTableException extends MyException {}
class AlreadyExistsException extends MyException {}
class EmptyObjectException extends MyException {}
class NoResultException extends MyException {}
class WrongParametersException extends MyException {}
class IllegalArgumentException extends MyException {}

/**
 *Classe permettant de se connecter à la base de données.
 *@author Benjamin Oertel
 *@since 15/02/08 22:08
 */

class Db extends PDO {
	private static $connection = false;
	
	/**
	*le constructeur est en public car il faut qu'il respecte la signature de la classe PDO
	*/
	public function __construct($host, $dbName, $user, $pass) {
		self::$connection = parent::__construct("mysql:host=$host;dbname=$dbName", $user, $pass);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	/**
	*@return une instance de la classe connection donc PDO.
	*On utilise cette méthode pour recuperer l'unique instance de connection à la base de données
	*Les paramètres de connection a la BD se trouve dans ce méthode.
	*/
	public static function getConnection() {
		if(!self::$connection) {
            global $DATABASE;
			$host = $DATABASE['host'];
			$dbName = $DATABASE['name'];
			$user = $DATABASE['user'];
			$pass = $DATABASE['password'];
            self::$connection = new Db($host, $dbName, $user, $pass);
		}
        return self::$connection;
    }
    
    public static function request($requete) {
    	try {
    		return Db::getConnection()->query($requete);
    	}
    	catch(PDOException $e) {
   			echo "<p class=\"err\"><pre>" . $e . "</pre></p>\n";
    	}
    }
    
    // Retourne le dernier id insere dans la table
    public static function lastId() {
    	return Db::getConnection()->lastInsertId();
    }

	// Appel le constructeur sur l'ensemble des elements d'un tableau :
	public static function createObjects($className, $array) {
        $arrayObjects = null;
		if (!class_exists($className, true)) {
			throw new PDOException("Class $className n\'existe pas");
		}
		
		foreach($array as $row) {
			$arrayObjects[] = new $className($row);
		}
		return $arrayObjects;
	}
}
?>
