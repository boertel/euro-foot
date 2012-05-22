<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 21:55
  * @description : User
  */


class User {
private $id;
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
	else if ($argc == 7) {
		$this->createObject($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6]);
	}
	else if ($argc == 6) {
		$this->createObjectWithoutPrimaryKey($args[0],$args[1],$args[2],$args[3],$args[4],$args[5]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

protected function createObject($id, $username, $first_name, $last_name, $email, $token, $score) {
	$this->id = $id;
	$this->username = $username;
	$this->first_name = $first_name;
	$this->last_name = $last_name;
	$this->email = $email;
	$this->token = $token;
	$this->score = $score;
}

protected function createObjectWithoutPrimaryKey($username, $first_name, $last_name, $email, $token, $score) {
	$id = null;
	$this->createObject($id, $username, $first_name, $last_name, $email, $token, $score);
}

protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 7) {
		$this->createObject($array[0],$array[1],$array[2],$array[3],$array[4],$array[5],$array[6]);
	}
	else if ($numAttributes == 6) {
		$this->createObjectWithoutPrimaryKey($array[0],$array[1],$array[2],$array[3],$array[4],$array[5]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}


public static function add(User $User) {
	Db::request("INSERT INTO User (username, first_name, last_name, email, token, score) VALUES (\"" . $User->getusername() . "\", \"" . $User->getfirst_name() . "\", \"" . $User->getlast_name() . "\", \"" . $User->getemail() . "\", \"" . $User->gettoken() . "\", \"" . $User->getscore() . "\")");
	$User->setid(Db::lastId());
}

public static function update(User $User) {
	Db::request("UPDATE User SET username=\"" . $User->getusername() . "\", first_name=\"" . $User->getfirst_name() . "\", last_name=\"" . $User->getlast_name() . "\", email=\"" . $User->getemail() . "\", token=\"" . $User->gettoken() . "\", score=\"" . $User->getscore() . "\" WHERE id=\"" . $User->getid() . "\"");
}

public static function delete(User $User) {
	Db::request("DELETE FROM User WHERE id=\"" . $User->getid() . "\"");
}

public static function find($id) {
	$result = Db::request("SELECT * FROM User WHERE id = \"" . $id . "\"");
	return new User($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="") {
	$result = Db::request("SELECT * FROM User " . $condition . "");
	return Db::createObjects('User', $result->fetchAll(PDO::FETCH_NUM));
}

public function getId() {
	return $this->id;
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