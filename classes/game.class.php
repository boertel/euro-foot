<?php
/** @author : 
  * @version : 1
  * @date : 21/05/2012 21:59
  * @description : Game
  */


class Game {
private $id;
private $team_a;
private $team_b;
private $score;
private $start_date;
private $end_date;
private $location;
private $stadium;


public function __construct() {
	$argc = func_num_args();
	$args = func_get_args();

	if ($argc == 1 && getType($args[0]) == "array") {
		$this->createObjectWithArray($args[0]);
	}
	else if ($argc == 8) {
		$this->createObject($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6],$args[7]);
	}
	else if ($argc == 7) {
		$this->createObjectWithoutPrimaryKey($args[0],$args[1],$args[2],$args[3],$args[4],$args[5],$args[6]);
	}
	else {
		throw new IllegalArgumentException("wrong number of arguments");
	}
}

protected function createObject($id, $team_a, $team_b, $score, $start_date, $end_date, $location, $stadium) {
	$this->id = $id;
	$this->team_a = $team_a;
	$this->team_b = $team_b;
	$this->score = $score;
	$this->start_date = $start_date;
	$this->end_date = $end_date;
	$this->location = $location;
	$this->stadium = $stadium;
}

protected function createObjectWithoutPrimaryKey($team_a, $team_b, $score, $start_date, $end_date, $location, $stadium) {
	$id = null;
	$this->createObject($id, $team_a, $team_b, $score, $start_date, $end_date, $location, $stadium);
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


public static function add(Game $Game) {
	Db::request("INSERT INTO Game (team_a, team_b, score, start_date, end_date, location, stadium) VALUES (\"" . $Game->getteam_a() . "\", \"" . $Game->getteam_b() . "\", \"" . $Game->getscore() . "\", \"" . $Game->getstart_date() . "\", \"" . $Game->getend_date() . "\", \"" . $Game->getlocation() . "\", \"" . $Game->getstadium() . "\")");
	$Game->setid(Db::lastId());
}

public static function update(Game $Game) {
	Db::request("UPDATE Game SET team_a=\"" . $Game->getteam_a() . "\", team_b=\"" . $Game->getteam_b() . "\", score=\"" . $Game->getscore() . "\", start_date=\"" . $Game->getstart_date() . "\", end_date=\"" . $Game->getend_date() . "\", location=\"" . $Game->getlocation() . "\", stadium=\"" . $Game->getstadium() . "\" WHERE id=\"" . $Game->getid() . "\"");
}

public static function delete(Game $Game) {
	Db::request("DELETE FROM Game WHERE id=\"" . $Game->getid() . "\"");
}

public static function find($id) {
	$result = Db::request("SELECT * FROM Game WHERE id = \"" . $id . "\"");
	return new Game($result->fetch(PDO::FETCH_NUM));
}

public static function findAll($condition="") {
	$result = Db::request("SELECT * FROM Game " . $condition . "");
	return Db::createObjects('Game', $result->fetchAll(PDO::FETCH_NUM));
}

public function getId() {
	return $this->id;
}

public function getTeam_a() {
	return $this->team_a;
}

public function getTeam_b() {
	return $this->team_b;
}

public function getScore() {
	return $this->score;
}

public function getStart_date() {
	return $this->start_date;
}

public function getEnd_date() {
	return $this->end_date;
}

public function getLocation() {
	return $this->location;
}

public function getStadium() {
	return $this->stadium;
}

public function setId($i) {
	$this->id = $i;
}

public function setTeam_a($t) {
	$this->team_a = $t;
}

public function setTeam_b($t) {
	$this->team_b = $t;
}

public function setScore($s) {
	$this->score = $s;
}

public function setStart_date($s) {
	$this->start_date = $s;
}

public function setEnd_date($e) {
	$this->end_date = $e;
}

public function setLocation($l) {
	$this->location = $l;
}

public function setStadium($s) {
	$this->stadium = $s;
}

public function __toString() {
	$view = "Object(Game) {\n";
	$view .= "\tid : " . $this->id . ";\n";
	$view .= "\tteam_a : " . $this->team_a . ";\n";
	$view .= "\tteam_b : " . $this->team_b . ";\n";
	$view .= "\tscore : " . $this->score . ";\n";
	$view .= "\tstart_date : " . $this->start_date . ";\n";
	$view .= "\tend_date : " . $this->end_date . ";\n";
	$view .= "\tlocation : " . $this->location . ";\n";
	$view .= "\tstadium : " . $this->stadium . ";\n";
	$view .= "}";
	return $view;
}
}
?>
