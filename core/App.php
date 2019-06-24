<?php

namespace core;

use Exception;

/**
 * Class App
 * @package core
 */
class App extends Base
{
    /**
     * @throws Exception
     */
    public function run()
    {
        $Request = new Request();

        if ($Response = $this->_handleRequest($Request)) {
            $Response->send();

            return;
        }

        throw new \Exception('Shit happens');
    }
}
