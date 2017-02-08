<?php
namespace App\Utility;

class Exception extends \Exception {    
    public static function handler ($e) {
        $_SESSION['error_message'] = $e->getMessage() . ' <code>in ' . $e->getFile() . ' on line ' . $e->getLine() . ' through url <a href="'.$_SERVER['REQUEST_URI'].'">"' . $_SERVER['REQUEST_URI'] . '"</a></code>';
        empty($_SERVER['HTTP_REFERER']) ? header ('Location: /') : header ('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    public static function throwf (\Throwable $e){
        throw $e;
        return true;
    }
}