<?php

namespace core;

use core\interfaces\ViewContextInterface;

/**
 * Class View
 * @package core
 */
class View
{
    /** @var */
    protected $_name;
    /** @var */
    protected $_params;
    /** @var */
    protected $_viewPath;
    /** @var string */
    public $defaultExtension = 'php';

    /**
     * View constructor.
     * @param $viewName
     * @param $params
     */
    public function __construct($viewName, $params)
    {
        $this->_name = $viewName;
        $this->_params = $params;
    }

    /**
     * @param null $viewName
     * @param array $params
     * @return string
     * @throws \Throwable
     */
    public function render($viewName = null, $params = []): string
    {
        if ($viewName) {
            $this->_name = $viewName;
        }

        if ($params) {
            $this->_params = $params;
        }

        $this->_viewPath = $this->_findViewFile($this->_name);

        return $this->renderFile($this->_viewPath, $this->_params);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $viewName
     * @return bool|mixed|string
     * @throws \ReflectionException
     */
    protected function _findViewFile($viewName)
    {
        if (strncmp($viewName, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = App::getAlias($viewName);
        } else {
            if (strncmp($viewName, '//', 2) === 0) {
                // e.g. "//layouts/main"
                $file = $this->getViewPath() . DIRECTORY_SEPARATOR . ltrim($viewName, '/');
            } else {
                if (App::$instance->controller instanceof Controller) {
                    $file = App::$instance->controller->getViewPath() . DIRECTORY_SEPARATOR . ltrim($viewName, '/');
                } else {
                    throw new \Exception("Unable to locate view file for view '$viewName': no active controller.");
                }
            }
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getViewPath()
    {
        if ($this->_viewPath === null) {
            $this->_viewPath = App::$instance->getBasePath() . DIRECTORY_SEPARATOR . 'views';
        }

        return $this->_viewPath;
    }

    /**
     * @param $_file_
     * @param array $_params_
     * @return false|string
     * @throws \Throwable
     */
    public function renderFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;

            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}
