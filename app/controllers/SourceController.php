<?php

namespace app\controllers;

use app\services\SearchService;
use app\entities\Source;
use core\Controller;
use core\Repository;
use core\View;
use Exception;

/**
 * Class SourceController
 * @package app\controllers
 */
class SourceController extends Controller
{
    /**
     * @param null $name
     * @return View
     * @throws \Exception
     */
    public function actionIndex($name = null)
    {
        $repo = new Repository(Source::class);

        if ($name) {
            $entities = (new SearchService($repo))->search($name);
        } else {
            $entities = $repo->findAll();
        }

        return new View('//source/index', ['entities' => $entities]);
    }

    /**
     * @throws Exception
     */
    public function actionAdd()
    {
        $data = $_GET;
        $Entity = new Source();
        $Entity->setIcd($data['icd']);
        $Entity->setPatientName($data['name']);
        $repo = new Repository(Source::class);
        $repo->add($Entity);
    }
}
