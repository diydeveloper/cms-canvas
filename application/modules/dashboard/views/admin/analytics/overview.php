<table id="new_returning" class="list">
    <thead>
        <tr>
            <th class="center">New Visitors</th>
            <th class="center">Returning Visitors</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="center"><?php echo $ga_visitor_type['new_visitor']; ?>%</td>
            <td class="center"><?php echo $ga_visitor_type['returning_visitor']; ?>%</td>
        </tr>
    </tbody>
</table>

<table class="list">
    <thead>
        <tr>
            <th>Day</th>
            <th class="right">Vists</th>
            <th class="right">Page Views</th>
            <th class="right">Avg Time on Site</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($ga_data as $day => $metric): ?>    
        <tr>
            <td><?php echo date('F d, Y', strtotime($day)); ?></td>
            <td class="right"><?php echo $metric['visits']; ?></td>
            <td class="right"><?php echo $metric['pageviews']; ?></td>
            <td class="right"><?php echo $this->google_analytics_model->avg_time_on_site($metric['timeOnSite'], $metric['visits']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
