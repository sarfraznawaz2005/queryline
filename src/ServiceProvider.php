<?php

namespace Sarfraznawaz2005\QueryLine;

use Event;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    private static $counter = 0;
    private static $dataString = '';

    public function boot()
    {
        // publish our files over to main laravel app
        $this->publishes([
            __DIR__ . '/config/queryline.php' => config_path('queryline.php')
        ]);

        if (!$this->isEnabled()) {
            return;
        }

        $this->prepare();

        DB::listen(function ($sql, $bindings = null, $time = null) {
            if ($sql instanceof QueryExecuted) {
                $time = $sql->time;
                $bindings = $sql->bindings;
                $sql = $sql->sql;
            }

            $sql = $this->applyBindings($sql, $bindings);

            $queryParts = explode(' ', $sql);

            if (isset($queryParts[0]) && strtolower($queryParts[0]) === 'select') {
                $count = ++self::$counter;

                self::$dataString .= "[$count, $time, \"$sql\"],\n";
            }
        });

        // Fired when laravel is done sending response. We use this event so that our
        // response is not repeated
        Event::listen('kernel.handled', function () {
            $this->output();
        });
    }

    protected function output()
    {
        $data = self::$dataString;

        echo <<< SCRIPT
        
    <script>
    google.charts.load('current', {'packages':['corechart']});
    
    google.charts.setOnLoadCallback(function() {
      window.QueryLineDataTable = new google.visualization.DataTable();
      
      window.QueryLineDataTable.addColumn('number', 'Time');
      window.QueryLineDataTable.addColumn('number', 'Query');
      window.QueryLineDataTable.addColumn({type: 'string', role: 'tooltip'});
      
      window.QueryLineDataTable.addRows([$data]);
    
      window.QueryLineDataTable.options = {"legend": "none", "title": "QueryLine", "vAxis": {"format": "#ms", title: "Time"}, "hAxis": {"format": "#", title: "Query", "minValue": 1, "maxValue": 5}, "width": "100%", "height":600};
      window.QueryLineDataTable.chart = new google.visualization.ColumnChart(document.getElementById('__queryline_chart__'));
      
      window.QueryLineDataTable.chart.draw(window.QueryLineDataTable, window.QueryLineDataTable.options);
    });     
    </script>
        
SCRIPT;

    }

    protected function prepare()
    {
        $html = <<< HTML
        
    <div id="__queryline_chart__" style="margin: 0 auto; z-index: 99999999; position: relative;"></div>
    
    <script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>

HTML;

        echo $html;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function isEnabled()
    {
        $enabled = config('queryline.enabled');

        if (!$enabled) {
            return false;
        }

        $queryString = config('queryline.querystring_name');

        if ($this->app->runningInConsole()) {
            return in_array($queryString, $_SERVER['argv']);
        }

        return request()->exists($queryString);
    }

    protected function applyBindings($sql, array $bindings)
    {
        if (empty($bindings)) {
            return $sql;
        }

        $placeholder = preg_quote('?', '/');

        foreach ($bindings as $binding) {
            $binding = is_numeric($binding) ? $binding : "'{$binding}'";
            $sql = preg_replace('/' . $placeholder . '/', $binding, $sql, 1);
        }

        return $sql;
    }
}