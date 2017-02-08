<?php
namespace App\Utility;

class Translator {
    static $lang = ['en_US' => 'English (US)', 'fr_FR' => 'Français'];
    static $dico;
    
    public static function _($string){
        if (!empty($string)){
            $string = strtolower($string);
            if (empty(self::$dico)){ self::setDico(); }
            if (!array_key_exists($string, self::$dico)) {
                self::$dico[$string] = (self::getCurrentLanguage() == 'en_US') ? $string : '_' . $string;
                self::updateDico();
            }
            return self::$dico[$string];
        }
    }
    
    /* dico language */
    public static function setDico(){
        if (empty(self::$dico)){
            $file = dirname(__FILE__).'/translation/'.self::getCurrentLanguage().'.json';
            if (file_exists($file)){
                self::$dico = json_decode(file_get_contents($file), true);
            } else {
                throw new \Exception ('Translation file not found "'.self::getCurrentLanguage().'translation/.json"');
            }
        }
    }
    public static function updateDico(){
        if (!empty(self::$dico)){
            $fp = fopen(dirname(__FILE__).'/translation/'.self::getCurrentLanguage().'.json', 'w');
            fwrite($fp, json_encode(self::$dico));
            fclose($fp);
            self::setDico();
        }
    }
    
    public static function getCurrentLanguage(){
        if (empty($_COOKIE['lang'])){
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch ($lang){
                case "fr":
                    $lang = 'fr_FR';
                    break;
                default:
                    $lang = 'en_US';
                    break;
            }
            self::setLanguage($lang);
            return $lang;
        }
        return $_COOKIE['lang'];
    }
    public static function setLanguage($languageCode){
        if (array_key_exists($languageCode,self::$lang)){
            setcookie("lang", $languageCode, time() + 2592000, '/'); // 1 month
        } else {
            throw new \Exception (self::_('Unknown language').' ('.$languageCode.')');
        }
    }
    public static function getAllLanguagesCode (){ return self::$lang; }
}