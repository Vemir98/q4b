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
                        "values" : ['.$stats[$type][$entity['id']]['filtered']['percents']['a'].','.$stats[$type][$entity['id']]['filtered']['percents']['b'].',],
                        "target": "graph",
                        "text":"'.$entity['name'].'",
                        "data-custom-token":['.$stats[$type][$entity['id']]['filtered']['statuses']['a'].','.$stats[$type][$entity['id']]['filtered']['statuses']['b'].',],
                        "backgroundColor": "'.$stats[$type][$entity['id']]['color'].'",
                        "legendText": "%t",
                        "legendMarker":{
                            "type": "square",
                            "size": 10,
                            "backgroundColor":"'.$stats[$type][$entity['id']]['color'].'",
                            "align": "right"
                        },
                        "tooltip":{
                            "backgroundColor": "'.$stats[$type][$entity['id']]['color'].'",
                            "color": "#000"
                        }
                    }';
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
                "type": "bar3d",
                "3d-aspect":{
                    "true3d":false,
                    "depth":"10px"
                },
                 <?if(Request::current()->headers('Pjsbot76463') == '99642'):?>"height": "360px",<?endif?>
                "legend":{
                    "text":"%t<br>",
                    "verticalAlign": "top",
                    "align":"right",
                    "padding":"10%",
                    "layout":"5x1",
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
                    "refAngle": 230,
                    "decimals": 0,
                    "thousandsSeparator": "",
                    "detach": false,
                    "valueBox":{
                        "decimals": 0,
                        "visible": true,
                         "text": "%v %",
                        "color": "#000"
                    },
                    "animation":{
                        "effect": 2,
                        "method": 1,
                        "sequence": 1,
                        "onLegendToggle": false
                    },
                    tooltip:{
                        visible:true,
                        text: "%data-custom-token",
                        "color": "#000"
                    }
                },
                "scale":{
                    "sizeFactor": 1
                },
                "scale-x": {
                    "line-color": "#d2dae2",
                    "line-width": "2px",
                    "values": ["<?=__(Enum_QualityControlStatus::Existing)?> + <?=__(Enum_QualityControlStatus::Normal)?>", "<?=__(Enum_QualityControlStatus::Existing)?> + <?=__(Enum_QualityControlStatus::Normal)?> + <?=__(Enum_QualityControlStatus::Repaired)?>"],
                    "item": {
                        "font-weight": "bold",
                        "font-size": "12px",
                        "fontFamily": '"proxima_nova_rgregular", Arial, Helvetica, sans-serif',
                        "wrap-text": false
                    },
                    "tick": {
                        "line-color": "#d2dae2",
                        "line-width": "1px"

                    },

                },
                "scale-y":{
                    "values":"0:100:10",
                    "format":"%v%",
                    "guide":{
                        "line-style":"dashdot"
                    }
                },
                "plotarea":{
                    <?=(Language::getCurrent()->direction == 'rtl' && !$isPhantom ? '"margin-left":"8%", "width": "80%",' : '"margin-right":"20%",')?>
                },
                "series" : [
                    <?=implode(",\n",$data)?>
                ]
            }
        ]
    },
    });
});
</script>
