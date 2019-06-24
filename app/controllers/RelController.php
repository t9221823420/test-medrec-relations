<?php

namespace app\controllers;

use app\entities\Rel;
use core\Controller;
use core\Repository;
use core\View;

/**
 * Class RelController
 * @package app\controllers
 */
class RelController extends Controller
{
    /**
     * @return View
     * @throws \Exception
     */
    public function actionIndex()
    {
        $repo = new Repository(Rel::class);
        $entities = $repo->findAll();

        return new View('//rel/index', ['entities' => $entities]);
    }

    /**
     * @throws \Exception
     */
    public function actionAdd()
    {
        $data = $_GET;
        $Entity = new Rel();
        $Entity->setNdc($data['ndc']);
        $repo = new Repository(Rel::class);

        $repo->add($Entity);
    }
}
