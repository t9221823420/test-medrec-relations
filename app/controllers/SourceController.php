<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 21:53
 */

namespace app\controllers;

use app\services\SearchService;
use app\entities\Source;
use core\Controller;
use core\Repository;
use core\View;

class SourceController extends Controller
{
	public function actionIndex( $name = null )
	{
		$repo = new Repository( Source::class );
		
		if( $name ) {
			$entities = ( new SearchService( $repo ) )->search( $name );
		}
		else {
			$entities = $repo->findAll();
		}
		
		return new View( '//source/index', [ 'entities' => $entities ] );
		
	}
	
	public function actionAdd()
	{
		$data = $_GET;
		
		$Entity = new Source();
		
		$Entity->setIcd( $data['icd'] );
		$Entity->setPatientName( $data['name'] );
		
		$repo = new Repository( Source::class );
		
		$repo->add( $Entity );
	}
}