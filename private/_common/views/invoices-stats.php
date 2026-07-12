<?php
$invoice=new Invoice();
$stats=$invoice->stats();
?>
<h2><i class="glyphicon glyphicon-hdd"></i> Statistiche fatturato</h2>
<hr>
<div class="row dashboard" style="margin-bottom:15px;">

    <div class="col-md-12">
        <article>
            <header><h4>Fatturato per anno e mese</h4></header>
            <div id="revenue_by_month_and_year" style="width:100%; height:350px;"></div>
            <table class="table">
                <thead>
                    <tr>
                        <?php foreach($stats->revenue_by_month_and_year as $i=>$array):?>
                        <th><?php echo $array[0]?></th>
                        <?php endforeach?>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=1; $i<count($stats->revenue_by_month_and_year[0]); $i++):?>
                    <tr>
                        <?php for($j=0; $j<count($stats->revenue_by_month_and_year); $j++):?>
                        <?php echo $j==0? '<th>':'<td>';?>
                        <?php echo $j==0? $stats->revenue_by_month_and_year[$j][$i] : App::currency($stats->revenue_by_month_and_year[$j][$i])?>
                        <?php echo $j==0? '</th>':'</td>';?>
                        <?php endfor?>
                    </tr>
                    <?php endfor?>
                </tbody>
            </table>
        </article>
    </div>

</div>

<div class="row dashboard">

    <div class="col-md-6">
        <article>
            <header><h4>Fatturato per anno</h4></header>
            <div id="revenue_by_year" style="width:100%; height:200px;"></div>
            <table class="table">
                <thead>
                    <tr>
                    <?php
                    for($i=1; $i<count($stats->revenue_by_year); $i++):		
                    echo '<th>'.$stats->revenue_by_year[$i][0].'</th>';	
                    endfor;
                    ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        for($i=1; $i<count($stats->revenue_by_year); $i++):
                        echo '<td>'.App::currency($stats->revenue_by_year[$i][1]).'</td>';	
                        endfor;
                        ?>
                    </tr>
                </tbody>
            </table>
        </article>
    </div>   

    <div class="col-md-6">
        <article>
            <header><h4>Media Mensile</h4></header>
            <div id="monthly_average_by_year" style="width:100%; height:200px;"></div>
            <table class="table">
                <thead>
                    <tr>
                    <?php
                    for($i=1; $i<count($stats->monthly_average_by_year); $i++):		
                    echo '<th>'.$stats->monthly_average_by_year[$i][0].'</th>';	
                    endfor;
                    ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                    for($i=1; $i<count($stats->monthly_average_by_year); $i++):
                    echo '<td>'.App::currency($stats->monthly_average_by_year[$i][1]).'</td>';	
                    endfor;
                    ?>
                    </tr>
                </tbody>
            </table>
        </article>
    </div>
   
</div>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
    var revenue_by_year=<?php echo json_encode($stats->revenue_by_year)?>;
    var monthly_average_by_year=<?php echo json_encode($stats->monthly_average_by_year)?>;
    var revenue_by_month_and_year=<?php echo json_encode($stats->revenue_by_month_and_year)?>;

    google.load('visualization', '1', {packages: ['corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart(){
        var data = google.visualization.arrayToDataTable(revenue_by_year);
        var chart = new google.visualization.ColumnChart(document.getElementById('revenue_by_year'));
        chart.draw(data, { legend: {position: 'none'} } );
            
        var data2 = google.visualization.arrayToDataTable(monthly_average_by_year);
        var chart2 = new google.visualization.ColumnChart(document.getElementById('monthly_average_by_year'));
        chart2.draw(data2, { legend: {position: 'none'} } );
            
        var data3 = google.visualization.arrayToDataTable(revenue_by_month_and_year);
        var chart3 = new google.visualization.ColumnChart(document.getElementById('revenue_by_month_and_year'));
        chart3.draw(data3);
    }
    window.onload=function(){
        $(window).resize(function(){
            drawChart();
        });
    }
</script>