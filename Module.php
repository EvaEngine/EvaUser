<?php

namespace Eva\EvaUser;

use Eva\EvaEngine\Engine;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Eva\EvaEngine\Module\StandardInterface;
use Eva\EvaUser\Models\Login;
use Eva\EvaEngine\Exception;

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

        static::registerGlobalServices($di);
    }

    /**
     * 注册全局的服务，需在 app 或者 其他 module 中的 registerServices 手动调用。
     *
     * @param \Phalcon\DiInterface $di
     */
    public static function registerGlobalServices($di)
    {
        $config = $di->getConfig();
        // 当用户系统和
        if (!empty($config->user->dbAdapter)) {
            $di->set('userDbMaster', function () use ($di, $config) {
                $slaves = $config->user->dbAdapter->slave;
                $slaveKey = array_rand($slaves->toArray());
                if (!isset($slaves->$slaveKey) || count($slaves) < 1) {
                    throw new Exception\RuntimeException(sprintf('No DB slave options found'));
                }

                return Engine::diDbAdapter($slaves->$slaveKey->adapter, $slaves->$slaveKey->toArray(), $di);
            });
            $di->set('userDbSlave', function () use ($di, $config) {
                $slaves = $config->user->dbAdapter->slave;
                $slaveKey = array_rand($slaves->toArray());
                if (!isset($slaves->$slaveKey) || count($slaves) < 1) {
                    throw new Exception\RuntimeException(sprintf('No DB slave options found'));
                }

                return Engine::diDbAdapter($slaves->$slaveKey->adapter, $slaves->$slaveKey->toArray(), $di);
            });
        }

    }
}
