<table class="list">
    <thead>
        <tr>
            <th>Page</th>
            <th>Page Views</th>
            <th>Unique Page Views</th>
            <th>Avg Time on Page</th>
            <th>Bounce Rate</th>
            <th>Entrance</th>
            <th>Exit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($ga_data as $page => $metric): ?>    
        <tr>
            <td><?php echo $page; ?></td>
            <td><?php echo $metric['pageviews']; ?></td>
            <td><?php echo $metric['uniquePageviews']; ?></td>
            <td><?php echo $this->google_analytics_model->avg_time_on_site($metric['timeOnPage'], $metric['pageviews'] - $metric['exits']); ?></td>
            <td><?php echo $this->google_analytics_model->calc_percent($metric['bounces'], $metric['entrances']); ?>%</td>
            <td><?php echo $this->google_analytics_model->calc_percent($metric['entrances'], $metric['pageviews']); ?>%</td>
            <td><?php echo $this->google_analytics_model->calc_percent($metric['exits'], $metric['pageviews']); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
