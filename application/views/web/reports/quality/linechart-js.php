<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 08.05.2019
 * Time: 3:15
 */
$data = [];
$stats = $report->getStats();
foreach ($report->getProjectsORObjects($type) as $entity){

    $data[] = '{
            values: ['.implode(',',$stats[$type][$entity['id']]['yearFkk']['a+b+fixed']).'],
            lineColor: "'.$stats[$type][$entity['id']]['color'].'",
            lineStyle: "dashed",
            marker: {
                backgroundColor: "'.$stats[$type][$entity['id']]['color'].'"
            },
            scales: "scale-x, scale-y"
        }, {
            values: ['.implode(',',$stats[$type][$entity['id']]['yearFkk']['a+b']).'],
            lineColor: "'.$stats[$type][$entity['id']]['color'].'",
            marker: {
                backgroundColor: "'.$stats[$type][$entity['id']]['color'].'"
            },
            scales: "scale-x, scale-y"
        }
        ';
}
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
                            labels: [ <?=$months?> ],
                            offsetEnd: '15'
                        },
                        "scale":{
                            "sizeFactor": 0.8
                        }
                        plotarea: {
                            margin: 'dynamic'
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
