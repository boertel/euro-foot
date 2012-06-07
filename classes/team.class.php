<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 22:00
  * @description : Team
  */


class Team {
private $id;
private $name;
private $flag;

public function __construct() {
	$argc = func_num_args();
	$args = func_get_args();

	if ($argc == 1 && getType($args[0]) == "array") {
		$this->createObjectWithArray($args[0]);
	}
	else if ($argc == 3) {
		$this->createObject($args[0],$args[1],$args[2]);
	}
	else if ($argc == 2) {
		$this->createObjectWithoutPrimaryKey($args[0],$args[1]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

protected function createObject($id, $name,$flag) {
	$this->id = $id;
	$this->name = $name;
        $this->flag = $flag;
}

protected function createObjectWithoutPrimaryKey($name,$flag) {
	$id = null;
	$this->createObject($id, $name,$flag);
}

protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 3) {
		$this->createObject($array[0],$array[1],$array[2]);
	}
	else if ($numAttributes == 2) {
		$this->createObjectWithoutPrimaryKey($array[0],$array[1]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}


public static function add(Team $Team) {
    //use DB::prepareRequest + add id_group field
//	Db::request("INSERT INTO Team (name) VALUES (\"" . $Team->getname() . "\")");
//	$Team->setid(Db::lastId());
}

public static function update(Team $Team) {
    //use DB::prepareRequest + add id_group field
//	Db::request("UPDATE Team SET name=\"" . $Team->getname() . "\" WHERE id=\"" . $Team->getid() . "\"");
}

public static function delete(Team $Team) {
    //use DB::prepareRequest + add id_group field
//	Db::request("DELETE FROM Team WHERE id=\"" . $Team->getid() . "\"");
}

public static function find($id) {
    //use DB::prepareRequest + add id_group field
//	$result = Db::request("SELECT * FROM Team WHERE id = \"" . $id . "\"");
//	return new Team($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="",$values = array()) {
	$statement = Db::prepareRequest("SELECT * FROM team " . $condition);
        $statement->execute($values);
	return Db::createObjects('Team', $statement->fetchAll(PDO::FETCH_NUM));
}

public function getId() {
	return $this->id;
}

public function getName() {
	return $this->name;
}

public function getFlag(){
        return $this->flag;
}

public function setId($i) {
	$this->id = $i;
}

public function setName($n) {
	$this->name = $n;
}

public function setFlag($f) {
	$this->flag = $f;
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