<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 08.05.2019
 * Time: 3:15
 */
$data = [];
$stats = $report->getStats();

$from = $report->getDateFrom();
$to = $report->getDateTo();
$fromN = Date('Ym',$from);
$toN = Date('Ym',$to);
$tsP = [];
$tsDp = [];
$max = 7;
foreach ($report->getMonthsTimestamps($report->getDateTo()) as $key => $ts){
    $tsN = Date('Ym',$ts);
    if($tsN <= $toN AND $tsN >= $fromN){
        $tsP[$key] = $ts;
        $tsDp[$key] = '"'.Date('m/Y',$ts).'"';
    }
    if(count($tsP) == $max) break;
}
$abf = [];
$ab = [];
foreach ($stats[$type][$entityId]['yearFkk']['a+b+fixed'] as $key => $val){
    if(isset($tsP[$key])){
        $abf[$key] = $val;
    }
    if(count($abf) == $max) break;
}
foreach ($stats[$type][$entityId]['yearFkk']['a+b'] as $key => $val){
    if(isset($tsP[$key]))
        $ab[$key] = $val;
    if(count($ab) == $max) break;
}
$data[] = '{
            values: ['.implode(',',$abf).'],
            lineColor: "'.$stats[$type][$entityId]['color'].'",
            lineStyle: "dashed",
            marker: {
                backgroundColor: "'.$stats[$type][$entityId]['color'].'"
            },
            scales: "scale-x, scale-y"
        }, {
            values: ['.implode(',',$ab).'],
            lineColor: "'.$stats[$type][$entityId]['color'].'",
            marker: {
                backgroundColor: "'.$stats[$type][$entityId]['color'].'"
            },
            scales: "scale-x, scale-y"
        }
        ';
?>
<script>
    $(document).ready(function(){
        zingchart.render({
            id : '<?=$id?>',
            data : {
                gui: {
                    contextMenu: {
                        backgroundColor: '#003366',
                        item: {
                            backgroundColor: '#003366',
                            fontColor: 'white',
                            borderColor: '#336666',
                            hoverState: {
                                backgroundColor: '#003366',
                                fontColor: 'white'
                            }
                        }
                    },
                    behaviors: [
                        // {
                        //     id: 'DownloadPDF',
                        //     enabled: 'none'
                        // },
                        {
                            id: 'ViewSource',
                            enabled: 'none'
                        },
                        {
                            id: 'ViewDataTable',
                            enabled: 'none'
                        },
                        // {
                        //     id: 'ViewAsPNG',
                        //     enabled: 'none'
                        // },
                        {
                            id: 'CrosshairHide',
                            enabled: 'all'
                        }
                    ]
                },
                "graphset":[
                    {
                        type: 'line',
                        scaleY: {
                            maxItems: 100,
                            lineColor: '#e53935' // make scale line color match (not necessary)
                        },
                        scaleX: {
                            labels: [ <?=implode(',',$tsDp)?> ],
                            offsetEnd: '15'
                        },
                        plotarea: {
                            margin: 'dynamic'
                        },
                        plot : {
                            "tooltip":{
                                "text":"%v%"
                            }
                        },
                        "series" : [
                            <?=implode(",",$data)?>
                        ]
                    }
                ]
            },
            height: '100%',
            width: '100%'
        });
    });
</script>
