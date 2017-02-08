<?php

namespace App\Post;

use \App\Utility\Database;
use \App\Utility\Translator;

class Entity {    
    private $vars = [];
    
    public function getAsArray (){ return $this->vars; }
    
    public function getChildren (){
        if (!isset($this->vars['children'])){
            $bdd = new Database();
            $children = $bdd->getPostChildren($this->getPost_id());
            foreach ($children as $key => $child){ $children[$key] = new Entity ($child['post_id_64'],$child); }
            $this->setChildren($children);
        }
        return $this->vars["children"];
    }
    public function setChildren($children){ $this->vars["children"] = $children; }
    
    /* construct */
    public function __construct ($id, $objectData = null){
        $id = strval($id);
        $bdd = new Database ();
        if ($bdd->isValidPostId($id)){
            if (empty($objectData)){
                $this->hydrate($bdd->getPost($id));
            } else {
                $this->hydrate($objectData);
            }
        } else {
            throw new \Exception (Translator::_("unknown post Id"). ' "' . $id . '"');
        }            
    }
    
    public function hydrate ($objectData){
        foreach ($objectData as $key => $value){
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)){ $this->$method($value); }
        }
    }
    
    /* post_id post_id_64 parent_id description score post_type targeted_post_id is_anonymous_post post_date author_pseudo author_avatar_file_full_path author_avatar_file_height author_avatar_file_width file_full_path file_height file_width */
    public function getPost_id(){ return $this->vars["post_id"]; }
    public function setPost_id($post_id){ $this->vars["post_id"] = $post_id; }

    public function getPost_id_64(){ return $this->vars["post_id_64"]; }
    public function setPost_id_64($post_id_64){ $this->vars["post_id_64"] = $post_id_64; }

    public function getParent_id(){ return $this->vars["parent_id"]; }
    public function setParent_id($parent_id){ $this->vars["parent_id"] = $parent_id; }

    public function getDescription(){ return $this->vars["description"]; }
    public function setDescription($description){ $this->vars["description"] = $description; }

    public function getScore(){ return $this->vars["score"]; }
    public function setScore($score){ $this->vars["score"] = $score; }

    public function getPost_type(){ return $this->vars["post_type"]; }
    public function setPost_type($post_type){ $this->vars["post_type"] = $post_type; }

    public function getTargeted_post_id(){ return $this->vars["targeted_post_id"]; }
    public function setTargeted_post_id($targeted_post_id){ $this->vars["targeted_post_id"] = $targeted_post_id; }

    public function getIs_anonymous_post(){ return $this->vars["is_anonymous_post"]; }
    public function setIs_anonymous_post($is_anonymous_post){ $this->vars["is_anonymous_post"] = $is_anonymous_post; }

    public function getPost_date(){ return $this->vars["post_date"]; }
    public function setPost_date($post_date){ $this->vars["post_date"] = $post_date; }

    public function getAuthor_pseudo(){ return $this->vars["author_pseudo"]; }
    public function setAuthor_pseudo($author_pseudo){ $this->vars["author_pseudo"] = $author_pseudo; }

    public function getAuthor_avatar_file_full_path(){ return $this->vars["author_avatar_file_full_path"]; }
    public function setAuthor_avatar_file_full_path($author_avatar_file_full_path){ $this->vars["author_avatar_file_full_path"] = $author_avatar_file_full_path; }

    public function getAuthor_avatar_file_height(){ return $this->vars["author_avatar_file_height"]; }
    public function setAuthor_avatar_file_height($author_avatar_file_height){ $this->vars["author_avatar_file_height"] = $author_avatar_file_height; }

    public function getAuthor_avatar_file_width(){ return $this->vars["author_avatar_file_width"]; }
    public function setAuthor_avatar_file_width($author_avatar_file_width){ $this->vars["author_avatar_file_width"] = $author_avatar_file_width; }

    public function getFile_full_path(){ return $this->vars["file_full_path"]; }
    public function setFile_full_path($file_full_path){ $this->vars["file_full_path"] = $file_full_path; }

    public function getFile_height(){ return $this->vars["file_height"]; }
    public function setFile_height($file_height){ $this->vars["file_height"] = $file_height; }

    public function getFile_width(){ return $this->vars["file_width"]; }
    public function setFile_width($file_width){ $this->vars["file_width"] = $file_width; }
}