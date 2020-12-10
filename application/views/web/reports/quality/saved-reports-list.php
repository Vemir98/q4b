<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 26.03.2019
 * Time: 10:48
 */
?>
<div class="saved-reports-container">
    <h2 class="saved-reports-header"><?=__('List of saved reports')?></h2>
    <div class="saved-reports">
        <div>
            <table>
            <?if(!count($reports)):?>
                <tr>
                    <td><span class="no-report"><?=__('No saved reports')?></span></td></tr>
            <?else:?>

                    <?foreach ($reports as $report):?>
                <?
                    $rp = explode(',',$report->projects);
                    if(!is_array($rp)){
                        $rp[0] = $rp;
                    }
                    foreach ($rp as $r){
                        if(!in_array($r,$projectIds)) continue 2;
                    }
                ?>
                        <tr>
                            <td><a href="<?=URL::site('reports/quality/saved/'.$report->id)?>"><?=$report->name.' '.date('d/m/Y',$report->created_at)?></a></td>
                            <td><a href="<?=URL::site('reports/quality/remove/'.$report->id)?>" class="remove-report"><i class="q4bikon-delete"></i></a></td>
                        </tr>
                    <?endforeach;?>
            <?endif;?>
            </table>
        </div>
    </div>
</div>
