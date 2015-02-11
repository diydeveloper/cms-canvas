<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/home.png') !!}"> Dashboard</h1>
    </div>
    <div class="content">
        {{-- // IE 7 display: table fix --}}
        @if (Config::get('cmscanvas::config.ga_profile_id') == '')
            <!--[if lt IE 8]>
            <script type="text/javascript">
                $(document).ready( function() {
                    $('#dashboard_sitemap > ul > li').each( function() {
                        $(this).replaceWith('<td' + ($(this).attr('id') ? ' id="' + $(this).attr('id') + '"' : '') +  '>' + $(this).html() + '</td>');
                    });

                    $('#dashboard_sitemap > ul').each( function() {
                        $(this).replaceWith('<table width="100%"><tr>' + $(this).html() + '</tr></table>');
                    });
                });
            </script>
            <![endif]-->
        @endif

        <div id="dashboard_sitemap">
            @include('theme::partials.navigation')
        </div>

        @if (Config::get('cmscanvas::config.ga_profile_id') != '')
            <div id="analytics">
                <div class="analytics-heading">
                    <div style="float: left;">Analytics</div>
                    <div id="metrics">
                        <?php //echo form_dropdown('ga_data_type', array('overview' => 'Overview', 'referrers' => 'Referrers', 'keywords' => 'Keywords', 'top_content' => 'Top Content', 'visits_by_country' => 'Visits By Country', 'browsers' => 'Browsers', 'screen_resolutions' => 'Screen Resolutions'), null, 'id="ga_data_type"'); ?>
                        &nbsp;<?php //echo form_dropdown('month_year', $month_year, null, 'id="month_year"'); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div id="ga_tables">
                    <?php //echo $ga_data; ?>
                </div>

                <script type="text/javascript">
                    $(document).ready( function() {
                        $('#ga_data_type').change( fetch_ga_data );
                        $('#month_year').change( fetch_ga_data );
                        
                        function fetch_ga_data()
                        {
                            $('#ga_tables').html('Loading...');

                            $.post('<?php //echo site_url(ADMIN_PATH . '/dashboard/get-ga-data'); ?>', { ga_data_type: $('#ga_data_type').val(), month_year: $('#month_year').val() }, function(html) {
                                $('#ga_tables').html(html);
                            });
                        }
                    });
                </script>
            </div>
        @endif

        <div class="clear"></div>
    </div>
</div>
