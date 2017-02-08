<?php
namespace App\Utility;

class Database extends \PDO {
    public function __construct (){
        $host = 'mysql:host=localhost;dbname=tifod';
        $username = 'root';
        $password = 'koala';

        date_default_timezone_set ("Europe/Paris");
        parent :: __construct($host, $username, $password);
    }
    
    /* Query */
    public function query ($query, $arguments){
        $reponse = $this->prepare ($query);
        $reponse->execute($arguments) or Exception::throwf(new Exception($this->errorInfo()[2] . ' ' . $query));
        while ($donnees[] = $reponse->fetch());
        $reponse->closeCursor();
        return $donnees;
    }
    
    /* Getting entity */
    public function getPost ($id,$column = 'post_id'){
        $t = $this->query(
            "SELECT post.post_id,
            post.post_id_64,
            post.parent_id,
            post.description,
            post.score,
            post.post_type,
            post.targeted_post_id,
            post.is_anonymous_post,
            post.post_date,
            u.pseudo author_pseudo,
            CONCAT(avatar_folder.file_name,avatar_f.file_name) author_avatar_file_full_path,
            avatar_f.file_height author_avatar_file_height,
            avatar_f.file_width author_avatar_file_width,
            CONCAT(folder.file_name,f.file_name) file_full_path,
            f.file_height,
            f.file_width
            FROM post
            INNER JOIN user u ON u.user_id = post.author_id
            INNER JOIN file f ON f.file_id = post.file_id
            INNER JOIN file folder ON folder.file_id = f.file_path_id
            INNER JOIN file avatar_f ON avatar_f.file_id = u.avatar_file_id
            INNER JOIN file avatar_folder ON avatar_folder.file_id = avatar_f.file_path_id
            WHERE post.$column = :id",
            ['id' => Math::from64to10($id)]
        );
        return ($column == 'post_id' ? $t[0] : $t );
    }
    public function getPostChildren ($id){
        $t = $this->getPost($id,'parent_id');
        array_pop($t);
        return $t;
    }
    public function getUserPosts ($pseudo){
        $t = $this->getPost(Math::from10to64($this->query("SELECT user.user_id FROM user WHERE user.pseudo = :pseudo",['pseudo' => $pseudo])[0]['user_id']),'author_id');
        array_pop($t);
        return $t;
    }
    public function getUser ($pseudo){
        return $this->query(
            'SELECT user.pseudo, user.user_type, CONCAT(folder.file_name,f.file_name) user_avatar_file_full_path, user.post_amount
            FROM user
            INNER JOIN file f ON f.file_id = user.avatar_file_id
            INNER JOIN file folder ON folder.file_id = f.file_path_id
            WHERE user.pseudo = :pseudo',
            ['pseudo' => $pseudo]
        )[0];
    }
    public function getAllUsers (){
        $t = $this->query(
            'SELECT user.pseudo, user.user_type, CONCAT(folder.file_name,f.file_name) user_avatar_file_full_path
            FROM user
            INNER JOIN file f ON f.file_id = user.avatar_file_id
            INNER JOIN file folder ON folder.file_id = f.file_path_id
            ORDER BY user.pseudo',null
        );
        array_pop($t);
        return $t;
    }
    
    /* Cheking validity of an entity in database */
    private function isInDatabase ($table, $column, $id){
        $reponse = $this->prepare ("SELECT $column FROM $table WHERE $column = :id");
        $reponse->execute([
            'id' => $id
        ]) or Exception::throwf(new Exception($this->errorInfo()[2]));
        $donnees = $reponse->fetch();
        $reponse->closeCursor();
        return empty($donnees) ? false : strval($donnees[$column]);
    }
    public function isValidPostId ($id){ return $this->isInDatabase('post','post_id',Math::from64to10($id)); }
    public function isUsedPseudo ($pseudo){ return $this->isInDatabase('user','pseudo',$pseudo); }
    public function isUsedEmail ($email){ return $this->isInDatabase('user', 'email', $email); }
}