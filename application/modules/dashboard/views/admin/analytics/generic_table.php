<table class="list">
    <thead>
        <tr>
            <?php foreach($table_cells as $heading): ?>
                <th><?php echo $heading; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty($ga_data)): ?>
            <?php array_shift($table_cells); ?>
            <?php foreach($ga_data as $dimension => $metric): ?>    
            <tr>
                <td><?php echo $dimension; ?></td>
                <?php if (is_array($metric)): ?>
                    <?php foreach($table_cells as $key => $heading): ?>
                        <td><?php echo $metric[$key]; ?></td>
                    <?php endforeach; ?>
                <?php else: ?>
                    <td><?php echo $metric; ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td class="center" colspan="2">No data found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

