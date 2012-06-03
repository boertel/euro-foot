<?php

class Session {

    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    private function __construct() {
        session_start();
    }

    public function regenerateId() {
        session_regenerate_id();
    }

    public function setUserSession(User $user) {
        $_SESSION['user'] = $user;
    }

    public function getUserSession() {
        return $_SESSION['user'];
    }
    
    public function isUserConnected() {
        return isset($_SESSION['user']);
    }
    
    public function disconnectUser(){
        unset($_SESSION['user']);
    }

    public function setSessionVar($varName, $value) {
        $_SESSION[$varName] = $value;
    }

    public function getSessionVar($varName) {
        return $_SESSION[$varName];
    }

    public function sessionUnregister($varName) {
        session_unregister($varName);
    }
}
?>