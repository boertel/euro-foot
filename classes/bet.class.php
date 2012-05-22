<?php

/** @author : 
 * @version : 1
 * @date : 21/05/2012 22:01
 * @description : Bet
 */
class Bet {

    private $id;
    private $match_id;
    private $user_id;
    private $score_a;
    private $score_b;

    public function __construct() {
	$argc = func_num_args();
	$args = func_get_args();

	if ($argc == 1 && getType($args[0]) == "array") {
	    $this->createObjectWithArray($args[0]);
	} else if ($argc == 5) {
	    $this->createObject($args[0], $args[1], $args[2], $args[3], $args[4]);
	} else if ($argc == 4) {
	    $this->createObjectWithoutPrimaryKey($args[0], $args[1], $args[2], $args[3]);
	} else {
	    throw new IllegalArgumentException("wrong number of arguments");
	}
    }

    protected function createObject($id, $match_id, $user_id, $score_a, $score_b) {
	$this->id = $id;
	$this->match_id = $match_id;
	$this->user_id = $user_id;
	$this->score_a = $score_a;
	$this->score_b = $score_b;
    }

    protected function createObjectWithoutPrimaryKey($match_id, $user_id, $score_a, $score_b) {
	$id = null;
	$this->createObject($id, $match_id, $user_id, $score_a, $score_b);
    }

    protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 5) {
		$this->createObject($array[0],$array[1],$array[2],$array[3],$array[4]);
	}
	else if ($numAttributes == 4) {
		$this->createObjectWithoutPrimaryKey($array[0],$array[1],$array[2],$array[3]);
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
    public static function add(Bet $Bet) {
	$statement = Db::prepareRequest("INSERT INTO Bet (match_id, user_id, score_a, score_b)"
				." VALUES (:matchId, :userId, :scoreA, :scoreB)");
	
	$result = $statement->execute(array('matchId' => $Bet->getmatch_id(), 'userId' => $Bet->getuser_id(), 
	    'scoreA' => $Bet->getscore_a(), 'scoreB' => $Bet->getscore_b()));
	
	$Bet->setid(Db::lastId());
	return $result;
    }

    /**
     *
     * @param Bet $Bet
     * @return bool true on success or false on failure. 
     */
    public static function update(Bet $Bet) {
	$statement = Db::prepareRequest("UPDATE Bet SET match_id = :matchId, user_id = :userId, score_a = :scoreA,"
					." score_b = :scoreB WHERE id= :id");
	return $statement->execute(array('matchId' => $Bet->getmatch_id(), 'userId' => $Bet->getuser_id(), 'scoreA' => $Bet->getscore_a(), 'scoreB' => $Bet->getscore_b(), "id" => $Bet->getId()));
    }
    
    /**
     *
     * @param Bet $Bet
     * @return bool true on success or false on failure. 
     */
    public static function delete(Bet $Bet) {
	$statement = Db::prepareRequest("DELETE FROM Bet WHERE id = :id");
	return $statement->execute(array('id' =>  $Bet->getId()));
    }
    
    public static function find($id) {
	$statement = Db::prepareRequest("SELECT * FROM Bet WHERE id = :id");
	$statement->execute(array('id' =>  $id));
	return new Bet($statement->fetch(PDO::FETCH_NUM));
    }

    public static function findAll($condition = "") {
	$result = Db::request("SELECT * FROM Bet " . $condition . "");
	return Db::createObjects('Bet', $result->fetchAll(PDO::FETCH_NUM));
    }

    public function getId() {
	return $this->id;
    }

    public function getMatch_id() {
	return $this->match_id;
    }

    public function getUser_id() {
	return $this->user_id;
    }

    public function getScore_a() {
	return $this->score_a;
    }

    public function getScore_b() {
	return $this->score_b;
    }

    public function setId($i) {
	$this->id = $i;
    }

    public function setMatch_id($m) {
	$this->match_id = $m;
    }

    public function setUser_id($u) {
	$this->user_id = $u;
    }

    public function setScore_a($s) {
	$this->score_a = $s;
    }

    public function setScore_b($s) {
	$this->score_b = $s;
    }

    public function __toString() {
	$view = "Object(Bet) {\n";
	$view .= "\tid : " . $this->id . ";\n";
	$view .= "\tmatch_id : " . $this->match_id . ";\n";
	$view .= "\tuser_id : " . $this->user_id . ";\n";
	$view .= "\tscore_a : " . $this->score_a . ";\n";
	$view .= "\tscore_b : " . $this->score_b . ";\n";
	$view .= "}";
	return $view;
    }

}

?>