<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 21:53
 */

namespace app\controllers;

use app\entities\Rel;
use core\Controller;
use core\Repository;
use core\View;

class RelController extends Controller
{
	public function actionIndex(){
		
		$repo = new Repository( Rel::class );
		
		$entities = $repo->findAll();
		
		return new View( '//rel/index', [ 'entities' => $entities ] );
		
	}
	
	public function actionAdd()
	{
		$data = $_GET;
		
		$Entity = new Rel();
		
		$Entity->setNdc( $data['ndc']);
		
		$repo = new Repository( Rel::class );
		
		$repo->add( $Entity );
	}
	
}