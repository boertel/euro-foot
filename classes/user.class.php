<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 21:55
  * @description : User
  */


class User {
private $id;
private $facebookId;
private $username;
private $first_name;
private $last_name;
private $email;
private $token;
private $score;


public function __construct() {
	$argc = func_num_args();
	$args = func_get_args();

	if ($argc == 1 && getType($args[0]) == "array") {
		$this->createObjectWithArray($args[0]);
	}
	else if ($argc == 8) {
		$this->createObject($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6], $args[7]);
	}
	else if ($argc == 7) {
		$this->createObjectWithoutPrimaryKey($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

protected function createObject($id, $facebookId, $username, $first_name, $last_name, $email, $token, $score) {
	$this->id = $id;
        $this->facebookId = $facebookId;
	$this->username = $username;
	$this->first_name = $first_name;
	$this->last_name = $last_name;
	$this->email = $email;
	$this->token = $token;
	$this->score = $score;
}

protected function createObjectWithoutPrimaryKey($facebookId, $username, $first_name, $last_name, $email, $token, $score) {
	$id = null;
	$this->createObject($id, $facebookId, $username, $first_name, $last_name, $email, $token, $score);
}

protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 8) {
		$this->createObject($array[0],$array[1],$array[2],$array[3],$array[4],$array[5],$array[6],$array[7]);
	}
	else if ($numAttributes == 7) {
		$this->createObjectWithoutPrimaryKey($array[0],$array[1],$array[2],$array[3],$array[4],$array[5],$array[6]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

/**
*
* @param Bet $Bet
* @return bool true on success or false on failure. 
*/
public static function add(User $User) {
    
    $statement = Db::prepareRequest("INSERT INTO User (facebookId, username, first_name, last_name, email, token, score) VALUES (:facebookId, :userName, :firstName, :lastName, :email, :token, :score)");
    
    $result = $statement->execute(array('facebookId' => $User->getFacebookId(),'userName' => $User->getUsername(), 
        'firstName' => $User->getFirst_name(), 'lastName' => $User->getLast_name(), 'email' => $User->getEmail(), 
        'token' => $User->getToken(), 'score' => $User->getScore()));
   
    $User->setid(Db::lastId());
    
    return $result;
}

/**
*
* @param Bet $Bet
* @return bool true on success or false on failure. 
*/
public static function update(User $User) {
	$statement = Db::prepareRequest("UPDATE User SET facebookId = :facebookId, username = :userName, first_name = :firstName, last_name = :lastName, email = :email, token = :token, score = :score WHERE id = :id");

        $result = $statement->execute(array('facebookId' => $User->getFacebookId(),'userName' => $User->getUsername(), 
            'firstName' => $User->getFirst_name(), 'lastName' => $User->getLast_name(), 'email' => $User->getEmail(), 
            'token' => $User->getToken(), 'score' => $User->getScore(), 'id' => $User->getId()));
        
        return $result;
}

public static function delete(User $User) {
	Db::request("DELETE FROM User WHERE id=\"" . $User->getid() . "\"");
}

public static function find($id) {
	$result = Db::request("SELECT * FROM User WHERE id = \"" . $id . "\"");
	return new User($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="", $values = array()) {
	$statement = Db::prepareRequest("SELECT * FROM User " . $condition);
        $statement->execute($values);
	return Db::createObjects('User', $statement->fetchAll(PDO::FETCH_NUM));
}


public static function findUserByFacebookId($facebookId) {
    return self::findAll('WHERE facebookId = :facebookId', array('facebookId'=>$facebookId));
}

public static function findAllOrderByScore() {
    return self::findAll('ORDER BY score DESC');
}

public function getId() {
	return $this->id;
}

public function getFacebookId(){
    return $this->facebookId;
}

public function getUsername() {
	return $this->username;
}

public function getFirst_name() {
	return $this->first_name;
}

public function getLast_name() {
	return $this->last_name;
}

public function getEmail() {
	return $this->email;
}

public function getToken() {
	return $this->token;
}

public function getScore() {
	return $this->score;
}

public function setId($i) {
	$this->id = $i;
}

public function setFacebookId($fbId){
    $this->facebookId = $fbId;
}

public function setUsername($u) {
	$this->username = $u;
}

public function setFirst_name($f) {
	$this->first_name = $f;
}

public function setLast_name($l) {
	$this->last_name = $l;
}

public function setEmail($e) {
	$this->email = $e;
}

public function setToken($t) {
	$this->token = $t;
}

public function setScore($s) {
	$this->score = $s;
}
public function toArray() {
    return get_object_vars($this);
}
public function toJSON() {
    return json_format($this->toArray());
}

public function __toString() {
	$view = "Object(User) {\n";
	$view .= "\tid : " . $this->id . ";\n";
	$view .= "\tusername : " . $this->username . ";\n";
	$view .= "\tfirst_name : " . $this->first_name . ";\n";
	$view .= "\tlast_name : " . $this->last_name . ";\n";
	$view .= "\temail : " . $this->email . ";\n";
	$view .= "\ttoken : " . $this->token . ";\n";
	$view .= "\tscore : " . $this->score . ";\n";
	$view .= "}";
	return $view;
}


}
?>
