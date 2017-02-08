<?php
namespace App;
session_start();
// Autoload
require 'vendor/autoload.php';
// Set exception handler
set_exception_handler('App\Utility\Exception::handler');
// Ensure we display all errors
error_reporting (-1);
ini_set ( 'display_errors', 'On' );

// var_dump (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
// var_dump ($_SERVER['HTTP_ACCEPT_LANGUAGE']);

use \App\Utility\Translator;

/* functions */
function displayPostQuery ($id = '1', $options = ['within a player' => true]){
    // SELECT post_path FROM tifod.post WHERE post_path LIKE '%/1/%' OR post_path LIKE '1/%' OR post_path LIKE '%/1'
    $post = [new Post\Entity ($id)];        
    if ($id != '1'){
        $parent = new Post\Entity ($post[0]->getParent_id());
        $post = $parent->getChildren();
        foreach ($post as $key => $value){ if ($value->getPost_id() == $id){ $post = array_merge(array($key => $value) + $post); break; } }
    }
    return $options['within a player'] ? Utility\Twig::render('post/player.html',['children' => $post]) : Utility\Twig::render('post/no-player.html',['children' => $post]);
}
// Routes list
// $salt = 'nw8v4c4nGsBsu7PkKvjl42iEmyCXREHjcOPmaPwqy4xlAAz2oA';
// $salt = Math::random_str(50);
// echo 'pass = koala<br>salt = ' . $salt . '<br>hash = ' . Math::bcrypt('koala',$salt) . '<br>';
$router = new Utility\Router($_GET['url']);

// Dev URLs
$router->get('/dev/getset/:query',function ($query){
    $all_val = explode(' ', $query);
    echo '/* ' . $query . ' */';
    foreach ($all_val as $val){
        $ucfirst_val = ucfirst($val); echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;public function get' . $ucfirst_val . '(){ return $this->vars["' . $val . '"]; }<br>&nbsp;&nbsp;&nbsp;&nbsp;public function set' . $ucfirst_val . '($' . $val . '){ $this->vars["' . $val . '"] = $' . $val . '; }<br>' ;
    }
});

// Normal URLs
$router->get('/',function (){ echo displayPostQuery(); });
$router->get('/r',function (){ echo displayPostQuery(); });
$router->get('/r/:id',function ($id){ echo displayPostQuery($id); });
$router->get('/r/:id/siblings',function ($id){ echo displayPostQuery($id, ['within a player' => false]); });
$router->get('/r/:id/about',function ($id){ echo Utility\Twig::render('post/single-post-player.html',['post' => new Post\Entity($id)]); });
$router->get('/u',function (){
    $bdd = new Utility\Database ();
    echo Utility\Twig::render('user/all-users.html', ['allUsers' => $bdd->getAllUsers()]);
});
$router->get('/u/:id',function ($id){
    echo Utility\Twig::render('user/user.html',['user' => new User\Entity($id)]);
});
$router->get('/login',function (){
    if (User\Entity::userIsLogged()){
        header ('Location: /u/' . User\Entity::getCurrentUserPseudo());
        exit;
    } else {
        echo Utility\Twig::render('user/login.html');
    }
});
$router->post('/login',function (){
    if (!empty($_POST['pseudo']) and !empty($_POST['password']) and !User\Entity::userIsLogged()){
        $bdd = new Utility\Database ();
        $pseudo = $bdd->isUsedPseudo($_POST['pseudo']);
        if (empty($pseudo)){
            throw new \Exception (Translator::_('unknown pseudo'));
        } else {
            $donnees = $bdd->query('SELECT hashed_pass, pass_salt FROM user WHERE pseudo = :pseudo',['pseudo' => $pseudo])[0];
            if ($donnees['hashed_pass'] == Utility\Math::bcrypt($_POST['password'],$donnees['pass_salt'])){
                $_SESSION['pseudo'] = $pseudo;
            } else {
                throw new \Exception (Translator::_('wrong password'));
            }
        }
        empty($_SERVER['HTTP_REFERER']) ? header ('Location: /') : header ('Location: ' . $_SERVER['HTTP_REFERER']); exit;
    } else {
        if (User\Entity::userIsLogged()){
            throw new \Exception (Translator::_('you are already logged'));
        } else {
            throw new \Exception (Translator::_('you forgot to fill some fields on login form'));
        }
    }
});
$router->get('/logout',function (){
    session_destroy();
    header('Location: /');
    exit;    
});
$router->get('/signup',function (){
    if (User\Entity::userIsLogged()){
        throw new \Exception (Translator::_('you are already logged'));
    } else {
        echo Utility\Twig::render('user/signup.html');
    }
});
$router->post('/signup',function (){
    if (User\Entity::userIsLogged()){
        throw new \Exception (Translator::_('you are already logged'));
    } elseif (!empty($_POST['email']) and !empty($_POST['pseudo']) and !empty($_POST['password']) and !empty($_POST['passwordCheck'])){
        if ($_POST['passwordCheck'] !== $_POST['password']) throw new \Exception (Translator::_("the two passwords don't match, try again"));
        $bdd = new Utility\Database ();
        $pseudo = $bdd->isUsedPseudo($_POST['pseudo']);
        if (empty($pseudo)){
            $email = $bdd->isUsedEmail($_POST['email']);
            if (empty($email)){
                $pass_salt = Utility\Math::random_str(50);
                $donnees = $bdd->query('INSERT INTO user (pseudo,hashed_pass, pass_salt, email) VALUES (:pseudo, :hashed_pass, :pass_salt, :email)',[
                    'pseudo' => $_POST['pseudo'],
                    'hashed_pass' => Utility\Math::bcrypt($_POST['password'],$pass_salt),
                    'pass_salt' => $pass_salt,
                    'email' => $_POST['email']
                ])[0];
                $_SESSION['pseudo'] = $_POST['pseudo'];
                header ('Location: /');
            } else {
                throw new \Exception (Translator::_('email already used, did you forget your password?'));
            }
        } else {
            throw new \Exception (Translator::_('pseudo already used') . ' (<a href="/u/' . $pseudo . '">' . $pseudo . '</a>)');
        }
    } else {
        throw new \Exception (Translator::_('you forgot to fill some fields on login form'));
    }
});
$router->get('/lang/:lang',function ($lang){
    Translator::setLanguage($lang);
    empty($_SERVER['HTTP_REFERER']) ? header ('Location: /') : header ('Location: ' . $_SERVER['HTTP_REFERER']); exit;
});
// URLs to code
$router->get('/settings',function (){ echo '- welcome on the blog'; });
$router->get('/settings/resetpassword',function (){
    // if isset GET ?ml&tk
        // destroy expired tokens
        // if valid GET ?(e)m(ai)l=xxx&t(o)k(en)=xxx
            // destroy token
            // SESSION['pseudo'] = pseudo
        // else
            // throw 'sorry, link expired or already used, click here to ask for another one'
        
    // if connected
        // echo form : old password, new password, passwordCheck
    // else
        // echo form : email -> send me an email
    echo '- welcome on the blog'; });
$router->post('/settings/resetpassword',function (){
    // if (!empty($_POST['oldPassword']) and !empty($_POST['newPassword']) and !empty($_POST['newPasswordCheck'])){
        // if $_POST['newPasswordCheck'] != $_POST['newPassword']
            // throw 'not the same, try again'
        
        // if old password works
            // update password in users where user.pseudo = :pseudo (currentUser)
        // else
            // throw 'wrong old password'
    // } else {
        // throw 'Some fields are left empty'
    // }
    echo '- welcome on the blog'; });
$router->get('/r/:id/add',function ($id){ echo '- welcome on the reading page'; });
$router->get('/r/:id/edit',function ($id){ echo '- welcome on the reading page'; });
$router->get('/r/:id/upvote',function ($id){ echo '- welcome on the reading page'; });
$router->get('/r/:id/downvote',function ($id){ echo '- welcome on the reading page'; });
$router->run();