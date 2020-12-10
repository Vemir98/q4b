<div class="modal fade qc-list-modal" data-backdrop="static" data-keyboard="false" id="element-qc-forms" tabindex="-1" role="dialog" aria-labelledby="element-qc-forms">

    <?
    $statusArray = [
        "All" => "symbol-all",
        "waiting" => "symbol-active",
        "for_repair" => "symbol-inactive",
        "approved" => "symbol-archive",
    ];
    ?>
    <div class="modal-dialog q4_project_modal modal-report-craft-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header q4_modal_header">
                <div class="q4_modal_header-top">
                    <button type="button" class="close q4-close-modal" data-dismiss="modal"><i class="q4bikon-close"></i></button>
                    <div class="clear"></div>
                </div>
                <div class="q4_modal_sub_header">
                    <h3><?=__('Quality control list')?></h3>
                </div>
            </div>
            <form action="/" class="q4_form" autocomplete="off">
                <div class="modal-body q4-modal-body-small">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="add-new-row-double">
                                <div class="add-new-right-modal">
                                    <a class="orange_plus_small create-quality-control" data-url="<?=URL::site('projects/quality_control/'.$items[0]->place->id)?>"  title="Add QC">
                                        <i class="plus q4bikon-plus"></i>
                                    </a>
                                </div>

                                <div class="q4-inside-filter">
                                    <?if(!empty($filterData['statuses'])):?>
                                        <?
                                        // echo "line: ".__LINE__." ".__FILE__."<pre>"; print_r([$filterData['statuses'],$selectedStatus]); echo "</pre>"; exit;?>
                                        <div class="filter-status-text"><?=__('Filter by')?>
                                            <?=__('status')?>:
                                        </div>
                                        <ul class="inside-filters-list">
                                            <?foreach ($filterData['statuses'] as $key => $status):?>
                                                <li>
                                                    <?$active = $selectedStatus == strtolower($status['text']) ? ' active': ''?>
                                                    <a href="#"  data-url="<?=$status['url']?>" data-status="<?=$status['text']?>" class="inside-filter-button filter-settings-button <?=$active?>">
                                                        <span class="<?=$statusArray[$status['text']]?> status"></span>
                                                        <span class="filter-button-text"><?=__(strtolower($status['text']))?>
                                                            </span>
                                                        <span class="filter-button-numb">(<?=$status['count']?>)&#x200E;</span>
                                                    </a>
                                                </li>
                                            <?endforeach?>
                                        </ul>
                                    <?endif?>
                                </div>

                            </div>

                        </div>
                    </div>
                    <?if(count($items)>0):?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="qc-list-modal-scroll">
                                    <table class="rwd-table responsive_table table" data-toggle="table">
                                        <thead>
                                        <tr>
                                            <th data-field="ID"  class="td-25"><?=__('Quality control')?></th>
                                            <th  data-field="Crafts"  class="td-250"><?=__('Crafts')?></th>
                                            <th  data-field="Status"  class="td-125"><?=__('Status')?></th>
                                            <th data-field="Property" ><?=__('Stage')?></th>
                                            <th data-field="Create date" class="td-125"><?=__('Created Date')?></th>
                                            <th  data-field="Approvement Status"  class="td-125"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?foreach ($items as $i):?>
                                            <tr>
                                                <td class="rwd-td0 qc-sort" data-th="<?=__('Quality control')?>">
                                                    <div class="project-props-qc">
                                                        <a data-modalid="quality-control-modal" href="#" data-qc="quality-control" data-url="<?=URL::site('reports/quality_control/'.$i->id)?>"><?='#'.$i->id?>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="rwd-td6 qc-sort" data-th="<?=__('Crafts')?>">
                                                    <?=__($i->craft->name)?>
                                                </td>
                                                <td class="rwd-td7 qc-sort" data-th="<?=__('Status')?>">
                                                    <?=__($i->status)?>
                                                </td>
                                                <td class="rwd-td2 qc-sort" data-th="<?=__('Stage')?>">
                                                    <?=__($i->project_stage)?>
                                                </td>
                                                <td class="rwd-td6 qc-sort" data-th="<?=__('Created Date')?>">
                                                    <?=date('d.m.Y',$i->created_at)?>
                                                </td>
                                                <td class="rwd-td7 qc-sort" data-th="<?=__('Approvement Status')?>">
                                                    <span class="q4-status-<?=$i->approval_status?>"><?=__($i->approval_status)?></span>
                                                </td>
                                            </tr>
                                        <?endforeach;?>

                                        </tbody>
                                    </table>

                                </div><!--scrollable table-->
                            </div>
                        </div>
                    <?else:?>
                        <h5 class="no-records-found"><?=__('Not found')?></h5>
                    <?endif;?>


                </div>
            </form>
        </div>
    </div>
</div>
