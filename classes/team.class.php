<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 22:00
  * @description : Team
  */


class Team {
private $id;
private $name;


public function __construct() {
	$argc = func_num_args();
	$args = func_get_args();

	if ($argc == 1 && getType($args[0]) == "array") {
		$this->createObjectWithArray($args[0]);
	}
	else if ($argc == 2) {
		$this->createObject($args[0],$args[1]);
	}
	else if ($argc == 1) {
		$this->createObjectWithoutPrimaryKey($args[0]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

protected function createObject($id, $name) {
	$this->id = $id;
	$this->name = $name;
}

protected function createObjectWithoutPrimaryKey($name) {
	$id = null;
	$this->createObject($id, $name);
}

protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 2) {
		$this->createObject($array[0],$array[1]);
	}
	else if ($numAttributes == 1) {
		$this->createObjectWithoutPrimaryKey($array[0]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}


public static function add(Team $Team) {
	Db::request("INSERT INTO Team (name) VALUES (\"" . $Team->getname() . "\")");
	$Team->setid(Db::lastId());
}

public static function update(Team $Team) {
	Db::request("UPDATE Team SET name=\"" . $Team->getname() . "\" WHERE id=\"" . $Team->getid() . "\"");
}

public static function delete(Team $Team) {
	Db::request("DELETE FROM Team WHERE id=\"" . $Team->getid() . "\"");
}

public static function find($id) {
	$result = Db::request("SELECT * FROM Team WHERE id = \"" . $id . "\"");
	return new Team($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="") {
	$result = Db::request("SELECT * FROM Team " . $condition . "");
	return Db::createObjects('Team', $result->fetchAll(PDO::FETCH_NUM));
}

public function getId() {
	return $this->id;
}

public function getName() {
	return $this->name;
}

public function setId($i) {
	$this->id = $i;
}

public function setName($n) {
	$this->name = $n;
}

public function __toString() {
	$view = "Object(Team) {\n";
	$view .= "\tid : " . $this->id . ";\n";
	$view .= "\tname : " . $this->name . ";\n";
	$view .= "}";
	return $view;
}
}
?>