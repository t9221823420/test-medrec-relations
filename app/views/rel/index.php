<table>

    <tr>
        <th>MEDREC_ID</th>
        <th>NCD</th>
    </tr>

    <?php foreach ($entities as $key => $Entity) : ?>
        <tr>
            <td><?= $Entity->getMedrecId() ?></td>
            <td><?= $Entity->getNdc() ?></td>
        </tr>
    <?php endforeach; ?>

</table>