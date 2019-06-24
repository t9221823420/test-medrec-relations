<?php

namespace core;

defined('BASE_PATH') or define('BASE_PATH', __DIR__);

/**
 * Class Base
 * @package core
 */
class Base
{
    /**
     * @var array class map used by the Yii autoloading mechanism.
     * The array keys are the class names (without leading backslashes), and the array values
     * are the corresponding class file paths (or [path aliases](guide:concept-aliases)). This property mainly affects
     * how [[autoload()]] works.
     * @see autoload()
     */
    protected static $_classMap = [];

    /**
     * @var array registered path aliases
     * @see getAlias()
     * @see setAlias()
     */
    protected static $_aliases = ['@core' => __DIR__];

    /** @var mixed */
    protected $_basePath;

    /** @var string */
    public $defaultRoute = '';

    /** @var string */
    public $controllerNamespace;

    /** @var string */
    protected $_db;

    /** @var string */
    protected $_entityManager;

    /** @var App */
    public static $instance;

    /** @var Controller */
    public $controller;

    /**
     * Base constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        if (isset($config['basePath'])) {
            $this->_basePath = $config['basePath'];
        }

        if (isset($config['aliases']) && is_array($config['aliases'])) {
            static::$_aliases = array_merge(static::$_aliases, $config['aliases']);
        }

        static::$instance = $this;
    }

    /**
     * @param $className
     * @throws \Exception
     */
    public static function autoload($className)
    {
        if (isset(static::$_classMap[$className])) {
            $classFile = static::$_classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } else {
            if (strpos($className, '\\') !== false) {
                $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
                if ($classFile === false || !is_file($classFile)) {
                    return;
                }
            } else {
                return;
            }
        }

        include $classFile;

        if (ENV_DEBUG && !class_exists($className, false) && !interface_exists($className,
                false) && !trait_exists($className, false)) {
            throw new \Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    /**
     * @param $alias
     * @param bool $throwException
     * @return bool|mixed|string
     * @throws \Exception
     */
    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$_aliases[$root])) {
            if (is_string(static::$_aliases[$root])) {
                return $pos === false ? static::$_aliases[$root] : static::$_aliases[$root] . substr($alias, $pos);
            }

            foreach (static::$_aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        if ($throwException) {
            throw new \Exception("Invalid path alias: $alias");
        }

        return false;
    }

    /**
     * @param $alias
     * @param $path
     * @throws \Exception
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }

        $pos = strpos($alias, '/');

        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if ($path !== null) {

            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);

            if (!isset(static::$_aliases[$root])) {
                if ($pos === false) {
                    static::$_aliases[$root] = $path;
                } else {
                    static::$_aliases[$root] = [$alias => $path];
                }
            } else {
                if (is_string(static::$_aliases[$root])) {
                    if ($pos === false) {
                        static::$_aliases[$root] = $path;
                    } else {
                        static::$_aliases[$root] = [
                            $alias => $path,
                            $root => static::$_aliases[$root],
                        ];
                    }
                } else {
                    static::$_aliases[$root][$alias] = $path;
                    krsort(static::$_aliases[$root]);
                }
            }
        } else {
            if (isset(static::$_aliases[$root])) {
                if (is_array(static::$_aliases[$root])) {
                    unset(static::$_aliases[$root][$alias]);
                } else {
                    if ($pos === false) {
                        unset(static::$_aliases[$root]);
                    }
                }
            }
        }
    }

    /**
     * @param $classMap
     * @throws \Exception
     */
    public static function setClassMap($classMap)
    {

        if (is_string($classMap)) {
            $classMap = [$classMap];
        } else {
            if (!is_array($classMap)) {
                throw new \Exception('$classMap has to be an array or string.');
            }
        }

        foreach ($classMap as $className => $path) {

            if (is_numeric($className)) {
                $className = $path;
            }

            if (!class_exists($className)) {
                throw new \Exception("class $className not found");
            }

            if ($className == $path) {
                static::$_classMap[$className] = \ReflectionClass::getFileName($className);
            } else {
                static::$_classMap[$className] = $path;
            }

        }

    }

    /**
     * @param Request $Request
     * @return Response
     * @throws \Exception
     */
    protected function _handleRequest(Request $Request): Response
    {
        list($route, $params) = $Request->resolve();
        $result = $this->_runAction($route, $params);
        $Response = new Response($result);

        return $Response;
    }

    /**
     * @param $route
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    protected function _runAction($route, $params = [])
    {
        $parts = $this->_createController($route);

        if (is_array($parts)) {

            /** @var Controller $Controller */
            list($Controller, $actionID) = $parts;

            App::$instance->controller = $Controller;

            $result = $Controller->runAction($actionID, $params);

            return $result;
        }

        throw new \Exception('Unable to resolve the request');
    }

    /**
     * @param $route
     * @return array|bool
     * @throws \Exception
     */
    protected function _createController($route)
    {
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        // double slashes or leading/ending slashes may cause substr problem
        $route = trim($route, '/');
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        if (($pos = strrpos($route, '/')) !== false) {
            $id .= '/' . substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
        }

        $Controller = $this->_createControllerByID($id);
        if ($Controller === null && $route !== '') {
            $Controller = $this->createControllerByID($id . '/' . $route);
            $route = '';
        }

        return $Controller === null ? false : [$Controller, $route];
    }

    /**
     * @param $id
     * @return null
     * @throws \Exception
     */
    protected function _createControllerByID($id)
    {
        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        $className = preg_replace_callback('%-([a-z0-9_])%i', function ($matches) {
                return ucfirst($matches[1]);
            }, ucfirst($className)) . 'Controller';

        $className = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix) . $className, '\\');

        if (strpos($className, '-') !== false || !class_exists($className)) {
            return null;
        }

        if (is_subclass_of($className, 'core\Controller')) {

            $Controller = new $className();

            return get_class($Controller) === $className ? $Controller : null;
        } else {
            if (ENV_DEBUG) {
                throw new \Exception('Controller class must extend from \\core\\Controller.');
            }
        }

        return null;
    }

    /**
     * @return mixed|string
     * @throws \ReflectionException
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $this->_basePath = dirname((new \ReflectionClass($this))->getFileName());
        }

        return $this->_basePath;
    }

    /**
     * @return \PDO|null
     */
    public function getDB(): ?\PDO
    {
        if (is_null($this->_db)) {
            $this->_initDB();
        }

        return $this->_db;
    }

    /**
     * @throws \ReflectionException
     */
    protected function _initDB()
    {
        $connectionParams = require(static::$instance->getBasePath() . "/../config/db.php");

        $this->_db = new Db($connectionParams['dsn'], $connectionParams['user'], $connectionParams['password']);

    }
}

spl_autoload_register(['core\Base', 'autoload'], true, true);
$classMap = require __DIR__ . '/classes.php';

Base::setClassMap($classMap);
