<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 17:14
 */
?>

<table>
	
	<tr>
		<th>MEDREC_ID</th>
		<th>NCD</th>
	</tr>
	
	<?php foreach( $entities as $key => $Entity ) : ?>
	<tr>
		<td><?= $Entity->getMedrecId() ?></td>
		<td><?= $Entity->getNdc() ?></td>
	</tr>
	<?php endforeach; ?>
	
</table>