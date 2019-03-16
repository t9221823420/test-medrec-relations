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
		<th>ICD</th>
		<th>PATIENT_NAME</th>
	</tr>
	
	<?php foreach( $entities as $key => $Entity ) : ?>
	<tr>
		<td><?= $Entity->getMedrecId() ?></td>
		<td><?= $Entity->getIcd() ?></td>
		<td><?= $Entity->getPatientName() ?></td>
	</tr>
	<?php endforeach; ?>
	
</table>