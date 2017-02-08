<?php

namespace App\User;

use \App\Utility\Database;
use \App\Utility\Translator;

class Entity {
    private $vars = [];
    
    public function getAsArray (){ return $this->vars; }
    
    public static function getCurrentUser (){ return (self::getCurrentUserPseudo() != null) ? new Entity(self::getCurrentUserPseudo()) : null; }
    public static function getCurrentUserPseudo (){ return empty($_SESSION['pseudo']) ? null : $_SESSION['pseudo']; }
    public static function userIsLogged (){ return !empty($_SESSION['pseudo']); }
    
    public function getPosts (){
        if (!isset($this->vars['posts'])){
            $bdd = new Database();
            $posts = $bdd->getUserPosts($this->getPseudo());
            $this->setposts($posts);
        }
        return $this->vars["posts"];
    }
    public function setPosts($posts){ $this->vars["posts"] = $posts; }
    
    /* Construct */
    public function __construct ($pseudo, $objectData = null){
        $bdd = new Database ();
        if ($bdd->isUsedPseudo($pseudo)){
            if (empty($objectData)){
                $this->hydrate($bdd->getUser($pseudo));
            } else {
                $this->hydrate($objectData);
            }
        } else {
            throw new \Exception (Translator::_("unknown user") . ' "' . $pseudo . '"');
        }            
    }
    
    public function hydrate ($objectData){
        foreach ($objectData as $key => $value){
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)){ $this->$method($value); }
        }
    }
    
    /* pseudo user_type user_avatar_file_full_path post_amount */
    public function getPseudo(){ return $this->vars["pseudo"]; }
    public function setPseudo($pseudo){ $this->vars["pseudo"] = $pseudo; }

    public function getUser_type(){ return $this->vars["user_type"]; }
    public function setUser_type($user_type){ $this->vars["user_type"] = $user_type; }

    public function getUser_avatar_file_full_path(){ return $this->vars["user_avatar_file_full_path"]; }
    public function setUser_avatar_file_full_path($user_avatar_file_full_path){ $this->vars["user_avatar_file_full_path"] = $user_avatar_file_full_path; }

    public function getPost_amount(){ return $this->vars["post_amount"]; }
    public function setPost_amount($post_amount){ $this->vars["post_amount"] = $post_amount; }
}