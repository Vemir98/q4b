<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 07.05.2019
 * Time: 20:43
 */
$data = [];
$stats = $report->getStats();
$summ = 0;
foreach ($stats[$type]['statuses'] as $key => $val){
    $summ += $val;
}
foreach ($stats[$type]['statuses'] as $key => $val){
    if ( !in_array($key,Enum_QualityControlStatus::toArray()) AND $key !== QualityReport::STATUS_EXISTING_AND_FOR_REPAIR) continue;
    if ($key == QualityReport::STATUS_EXISTING_AND_FOR_REPAIR AND $val > 0){
        $offset =  '"offset-r": "6%","backgroundColor": "#d515d3",';
        $bgColor = "#d515d3";
    } else {
        if ($key == QualityReport::STATUS_EXISTING_AND_FOR_REPAIR){
            $offset =  '"backgroundColor": "#d515d3",';
            $bgColor = "#d515d3";
        } else {
            $offset = '';
            $bgColor = '';
        }

    }
    $data[] = '{
        "values" : ['.$val.'],
        "target":"graph",
        "text":"'.__($key).'",
         '.$offset.'
        "backgroundColor": "'.($bgColor ? $bgColor : $stats['colors'][$key]).'",
        "legendText": "'.($summ ? '%t %node-percent-value%' : '%t 0%').'",
        "legendMarker":{
            "type": "square",
            "size": 10,
            "backgroundColor":"'.($bgColor ? $bgColor : $stats['colors'][$key]).'",
            '.(Language::getCurrent()->direction == 'rtl' ? '"offsetX": 35' : '').'
        },
        "tooltip":{
            "backgroundColor": "'.$stats['colors'][$key].'",
            "text": "'.(Language::getCurrent()->direction == 'rtl' ? strrev($val) : $val).'",
        "rtl": '.(Language::getCurrent()->direction == 'rtl' ? 1 : 0).'
        }
    }';
}
?>
<script>
    $(document).ready(function(){
        zingchart.loadModules('patterns');
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
                        "type": "pie3d",
                        "height": "200px",
                        //"width": "575px",
                        "legend":{
                            "text":"%t<br>",
                            "verticalAlign": "middle",
                            "borderWidth": 0,
                            "toggleAction": "remove",
                            // "offset-x": "-20px",
                            "item":{
                                "fontColor": "#708492",
                                "align": "right",
                                "fontSize": "15px",
                                "fontFamily": '"proxima_nova_rgregular", Arial, Helvetica, sans-serif'
                            },
                            "itemOff":{
                                "alpha": 0.7,
                                "textAlpha": 1,
                                "fontColor": "#616161",
                                "text": "%t",
                                "textDecoration": "line-through",
                                "lineWidth": 2
                            },
                            "markerOff":{
                                "alpha": 0.75
                            }
                        },
                        "plot":{
                            "refAngle": 270,
                            "decimals": 0,
                            "thousandsSeparator": ".",
                            "detach": false,
                            "valueBox":{
                                "decimals": 2,
                                "visible": false
                            },
                            "animation":{
                                "effect": 2,
                                "method": 1,
                                "sequence": 1,
                                "onLegendToggle": false
                            }
                        },
                        "scale":{
                            "sizeFactor": 0.8
                        },
                        "series" : [
                            <?=implode(",\n",$data)?>
                        ]
                    }
                ]
            }
        });
    });

</script>
