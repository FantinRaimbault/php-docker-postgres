<?php

namespace App;

class Autoloader
{

    public static function register(){
        //Executer une fonction si on essaye d'instancier un class inconnue
        spl_autoload_register(function ($class){

            //   App\Core\router -> \Core\router
            $class = str_ireplace(__NAMESPACE__, "", $class);

            // \Core\router.php -> /Core/router
            $class = str_ireplace("\\", "/", $class);

            // /Core/router -> Core/router
            $class = ltrim($class, "/");

            if(file_exists($class.".php")){
                include $class.".php";
            }

        });

    }

}