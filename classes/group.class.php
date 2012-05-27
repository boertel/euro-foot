<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 22:00
  * @description : Group
  */


class Group {
private $id;
private $title;


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

protected function createObject($id, $title) {
	$this->id = $id;
	$this->title = $title;
}

protected function createObjectWithoutPrimaryKey($title) {
	$id = null;
	$this->createObject($id, $title);
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


public static function add(Group $Group) {
	Db::request("INSERT INTO Group (title) VALUES (\"" . $Group->getTitle() . "\")");
	$Group->setid(Db::lastId());
}

public static function update(Group $Group) {
	Db::request("UPDATE Group SET title=\"" . $Group->getTitle() . "\" WHERE id=\"" . $Group->getid() . "\"");
}

public static function delete(Group $Group) {
	Db::request("DELETE FROM Group WHERE id=\"" . $Group->getId() . "\"");
}

public static function find($id) {
	$result = Db::request("SELECT * FROM Group WHERE id = \"" . $id . "\"");
	return new Group($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="") {
	$result = Db::request("SELECT * FROM `Group` " . $condition);
	return Db::createObjects('Group', $result->fetchAll(PDO::FETCH_NUM));
}

public function getId() {
	return $this->id;
}

public function getTitle() {
	return $this->title;
}

public function setId($id) {
	$this->id = $id;
}

public function setTitle($title) {
	$this->title = $title;
}

public function __toString() {
	$view = "Object(Group) {\n";
	$view .= "\tid : " . $this->id . ";\n";
	$view .= "\ttitle : " . $this->title . ";\n";
	$view .= "}";
	return $view;
}
}
?>