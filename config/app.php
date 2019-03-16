<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 11:26
 */

return [
	'basePath' => dirname(  __DIR__, 1 ) . '/app',
	'controllerNamespace' => 'app\controllers',
	'aliases' => [
		'@app' => dirname(  __DIR__, 1 ) . '/app'
	]
];