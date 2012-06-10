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
    private $validated;

    public function __construct() {
        $argc = func_num_args();
        $args = func_get_args();

        if ($argc == 1 && getType($args[0]) == "array") {
            $this->createObjectWithArray($args[0]);
        } else if ($argc == 6) {
            $this->createObject($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
        } else if ($argc == 5) {
            $this->createObjectWithoutPrimaryKey($args[0], $args[1], $args[2], $args[3], $args[4]);
        } else {
            throw new IllegalArgumentException("wrong number of arguments");
        }
    }

    protected function createObject($id, $match_id, $user_id, $score_a, $score_b, $validated) {
        $this->id = $id;
        $this->match_id = $match_id;
        $this->user_id = $user_id;
        $this->score_a = $score_a;
        $this->score_b = $score_b;
        $this->validated = $validated;
    }

    protected function createObjectWithoutPrimaryKey($match_id, $user_id, $score_a, $score_b, $validated) {
	$id = null;
	$this->createObject($id, $match_id, $user_id, $score_a, $score_b, $validated);
    }

    protected function createObjectWithArray($array) {
	$numAttributes = count($array);
	if ($numAttributes == 6) {
		$this->createObject($array[0],$array[1],$array[2],$array[3],$array[4], $array[5]);
	}
	else if ($numAttributes == 5) {
		$this->createObjectWithoutPrimaryKey($array[0],$array[1],$array[2],$array[3], $array[4]);
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
        $statement = Db::prepareRequest("INSERT INTO bet (game_id, user_id, score_a, score_b, validated)"
                    ." VALUES (:gameId, :userId, :scoreA, :scoreB, :validated)");
        
        $result = $statement->execute(array('gameId' => $Bet->getmatch_id(), 'userId' => $Bet->getuser_id(), 
            'scoreA' => $Bet->getscore_a(), 'scoreB' => $Bet->getscore_b(), 'validated' => $Bet->getValidated()));
        
        $Bet->setid(Db::lastId());
        return $result;
    }

    /**
     *
     * @param Bet $Bet
     * @return bool true on success or false on failure. 
     */
    public static function update(Bet $Bet) {
        $statement = Db::prepareRequest("UPDATE bet SET game_id = :gameId, user_id = :userId, score_a = :scoreA,"
                        ." score_b = :scoreB, validated = :validated WHERE id= :id");
        return $statement->execute(array('gameId' => $Bet->getmatch_id(), 'userId' => $Bet->getuser_id(), 'scoreA' => $Bet->getscore_a(), 'scoreB' => $Bet->getscore_b(), 'validated' => $Bet->getValidated(), "id" => $Bet->getId()));
    }
    
    /**
     *
     * @param Bet $Bet
     * @return bool true on success or false on failure. 
     */
    public static function delete(Bet $Bet) {
	$statement = Db::prepareRequest("DELETE FROM bet WHERE id = :id");
	return $statement->execute(array('id' =>  $Bet->getId()));
    }
    
    public static function find($id) {
	$statement = Db::prepareRequest("SELECT * FROM bet WHERE id = :id");
	$statement->execute(array('id' =>  $id));
	return new Bet($statement->fetch(PDO::FETCH_NUM));
    }

    public static function findAll($condition = "", $values = array()) {
	$statement = Db::prepareRequest("SELECT * FROM bet " . $condition);
        $statement->execute($values);
	return Db::createObjects('Bet', $statement->fetchAll(PDO::FETCH_NUM));
    }
    
    public static function findAllBetsForUser(User $user) {
        return Bet::findAll("WHERE user_id = :userId", array('userId' => (int) $user->getId()));
    }
    
    public static function findAllBetsForUserIdsAndGameId(array $usersId,$gameId) {
        for($i = 0 ; $i < count($usersId);$i++){
            $usersId[$i] = Db::getConnection()->quote($usersId[$i]);
        }
        if(count($usersId) > 0){
            $result = Db::request ('SELECT B.*, U.* FROM bet B JOIN user U ON U.id = B.user_id WHERE game_id = '.(int) $gameId.' AND U.facebookId IN('.implode(',', $usersId).') ORDER BY U.last_name, U.first_name');
            return $result->fetchAll(PDO::FETCH_ASSOC);  
        }else{
            return array();
        }
    }
    
    public static function findBetByGameIdForUser($matchId, User $user) {
        return Bet::findAll("WHERE user_id = :userId AND game_id = :gameId", array('gameId' => (int) $matchId, 'userId'=>$user->getId()));
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

    public function getValidated() {
        return $this->validated;
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
	$view .= "\tvalidated : " . $this->validated . ";\n";
	$view .= "}";
	return $view;
    }

}

?>
