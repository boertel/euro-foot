<?php

/** @author : 
 * @version : 1
 * @date : 27/05/2012 12:03
 * @description : Game
 */
class Game {

    private $id;
    private $id_group;
    private $team_a;
    private $team_b;
    private $score_a;
    private $score_b;
    private $start_date;
    private $end_date;
    private $location;
    private $stadium;

    public function __construct() {
        $argc = func_num_args();
        $args = func_get_args();

        if ($argc == 1 && getType($args[0]) == "array") {
            $this->createObjectWithArray($args[0]);
        } else if ($argc == 10) {
            $this->createObject($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
        } else if ($argc == 9) {
            $this->createObjectWithoutPrimaryKey($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
        } else {
            throw new IllegalArgumentException("wrong number of arguments");
        }
    }

    protected function createObject($id, $id_group, $team_a, $team_b, $score_a, $score_b, $start_date, $end_date, $location, $stadium) {
        $this->id = $id;
        $this->id_group = $id_group;
        $this->team_a = $team_a;
        $this->team_b = $team_b;
        $this->score_a = $score_a;
        $this->score_b = $score_b;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location = $location;
        $this->stadium = $stadium;
    }

    protected function createObjectWithoutPrimaryKey($id_group, $team_a, $team_b, $score_a, $score_b, $start_date, $end_date, $location, $stadium) {
        $this->createObject(null, $id_group, $team_a, $team_b, $score_a, $score_b, $start_date, $end_date, $location, $stadium);
    }

    protected function createObjectWithArray($array) {
        $numAttributes = count($array);
        if ($numAttributes == 10) {
            $this->createObject($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], $array[8], $array[9]);
        } else if ($numAttributes == 9) {
            $this->createObjectWithoutPrimaryKey($array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], $array[8]);
        } else {
            throw new IllegalArgumentException("wrong number of arguments");
        }
    }

    public static function add(Game $Game) {
        //use DB::prepareRequest + add id_group field	
        //Db::request("INSERT INTO Game (id, team_a, team_b, score_a, score_b, start_date, end_date, location, stadium) VALUES (\"" . $Game->getid() . "\", \"" . $Game->getteam_a() . "\", \"" . $Game->getteam_b() . "\", \"" . $Game->getscore_a() . "\", \"" . $Game->getscore_b() . "\", \"" . $Game->getstart_date() . "\", \"" . $Game->getend_date() . "\", \"" . $Game->getlocation() . "\", \"" . $Game->getstadium() . "\")");
    }

    public static function update(Game $Game) {
        //use DB::prepareRequest + add id_group field	        
        //Db::request("UPDATE Game SET id=\"" . $Game->getid() . "\", team_a=\"" . $Game->getteam_a() . "\", team_b=\"" . $Game->getteam_b() . "\", score_a=\"" . $Game->getscore_a() . "\", score_b=\"" . $Game->getscore_b() . "\", start_date=\"" . $Game->getstart_date() . "\", end_date=\"" . $Game->getend_date() . "\", location=\"" . $Game->getlocation() . "\", stadium=\"" . $Game->getstadium() . "\" WHERE ");
    }

    public static function delete(Game $Game) {
        //use DB::prepareRequest + add id_group field	        
        //Db::request("DELETE FROM Game WHERE ");
    }

    public static function find($gameId) {

        $statement = Db::prepareRequest("SELECT * FROM game WHERE id = :id");
        $statement->execute(array('id' => (int) $gameId));
        return new Game($statement->fetch(PDO::FETCH_NUM));
    }

    public static function findAll($condition = "", $values = array()) {
        $statement = Db::prepareRequest("SELECT * FROM game " . $condition);
        $statement->execute($values);
        return Db::createObjects('Game', $statement->fetchAll(PDO::FETCH_NUM));
    }

    public function getId() {
        return $this->id;
    }

    public function getId_group() {
        return $this->id_group;
    }

    public function getTeam_a() {
        return $this->team_a;
    }

    public function getTeam_b() {
        return $this->team_b;
    }

    public function getScore_a() {
        return $this->score_a;
    }

    public function getScore_b() {
        return $this->score_b;
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

    public function setScore_a($s) {
        $this->score_a = $s;
    }

    public function setScore_b($s) {
        $this->score_b = $s;
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
        $view .= "\tscore_a : " . $this->score_a . ";\n";
        $view .= "\tscore_b : " . $this->score_b . ";\n";
        $view .= "\tstart_date : " . $this->start_date . ";\n";
        $view .= "\tend_date : " . $this->end_date . ";\n";
        $view .= "\tlocation : " . $this->location . ";\n";
        $view .= "\tstadium : " . $this->stadium . ";\n";
        $view .= "}";
        return $view;
    }

}

?>
