<?php
/**
 * Created by PhpStorm.
 * User: basil
 * Date: 17.05.2022
 * Time: 10:35
 */

namespace app\commands;
use Yii;

final class Parse
{
private static $instance = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): ParseRepoController
    {
        if (static::$instance === null) {
            static::$instance = new ParseRepoController('parse-repo',Yii::$app);
        }

        return static::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}