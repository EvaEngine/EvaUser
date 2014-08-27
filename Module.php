<?php

namespace Eva\EvaUser;

use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Eva\EvaEngine\Module\StandardInterface;
use Eva\EvaUser\Models\Login;

class Module implements ModuleDefinitionInterface, StandardInterface
{
    public static function registerGlobalAutoloaders()
    {
        return array(
            'Eva\EvaUser' => __DIR__ . '/src/EvaUser',
        );
    }

    public static function registerGlobalEventListeners()
    {
    }

    public static function registerGlobalViewHelpers()
    {
    }

    public static function registerGlobalRelations()
    {
    }

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
    }

    /**
     * Registers the module-only services
     *
     * @param \Phalcon\DiInterface $di
     */
    public function registerServices($di)
    {
        $dispatcher = $di->getDispatcher();
        $dispatcher->setDefaultNamespace('Eva\EvaUser\Controllers');
    }
}
