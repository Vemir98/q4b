// Компонент Модал
Vue.component('modal', {
    template: `
      <div id="choose-plan-modal" data-backdrop="static" data-keyboard="false" class="modal fade no-delete in" role="dialog" style="display: block; padding-left: 15px;">
        <div class="modal-dialog choose-plan-dialog modal-dialog-1070">
                <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header q4_modal_header">
                    <div class="q4_modal_header-top">
                        <button type="button" class="close q4-close-modal q4-close-child-modal" ><i class="q4bikon-close"></i></button>
                        <div class="clear"></div>
                    </div>
                    <div class="q4_modal_sub_header">
                        <h3><?=__('Choose a plan')?></h3>
                    </div>
                </div>
                <div class="modal-body bb-modal">
                    <div class="scrollable-table">
                        <table class="rwd-table responsive_table table scrollable-tbody" data-toggle="table">
                            <thead>
                            <tr>
                                <th class="td-50"><?=__('')?></th>
                                <th data-field="Name/Type" data-sortable="true" class="td-cell-10"><?=__('Name/Type')?></th>
                                <th data-field="Profession" data-sortable="true" class="td-cell-10"><?=__('Profession')?></th>
                                <th data-field="Floor" class="td-10" data-sortable="true"><?=__('Floor')?></th>
                                <th data-field="Element number" data-sortable="true" class="w-10"><?=__('Element number')?></th>
                                <th data-field="Edition" data-sortable="true" class="w-10"><?=__('Edition')?></th>
                                <th data-field="Date" data-sortable="true" class="td-10"><?=__('Date')?></th>
                                <th data-field="Image" data-sortable="true" class="td-10"><?=__('Image')?></th>
                            </tr>
                            </thead>
                            <tbody class="qc-v-scroll">
                            <?$i= '';?>
                            <?foreach($plans as $plan):?>
                                <?
    
                                        $crafts = [];
                                        foreach ($plan->crafts->find_all() as $craft) {
                                            $crafts[] = $craft->id;
                                        }
                                        ?>
                                    <tr data-crafts='<?=json_encode($crafts)?>' class="<?=in_array($item->craft_id,$crafts) OR empty($crafts) ? '' : 'hidden'?>">
                                    <td class="enable-plan-action align-center-left td-50" data-th="Select">
                                        <div class="div-cell">
                                            <label class="q4-radio">
                                                <input  name="plan" type="radio" value="<?=$plan->id?>" data-img="<?=$plan->files->where('status','=',Enum_FileStatus::Active)->find()->getImageLink()?>">
                                                <span> </span>
                                            </label>
                                        </div>
    
    
                                        <div class="pln-data hide">
                                            <input type="hidden" name="plan_id" value="<?=$plan->id?>"/>
                                            <h4 class="table-modal-label-h4"><?=__('Plan name')?>: <?=$plan->name ? $plan->name : $plan->file()->getName() ?></h4>
                                            <div class="col-20">
                                                <label class="table_label"><?=__('Edition')?></label>
                                                <input type="text" class="table_input disabled-input" value="<?=$plan->edition?>"/>
                                            </div>
                                            <div class="col-30">
                                                <label class="table_label"><?=__('Date')?></label>
                                                <input type="text" class="table_input disabled-input" value="<?=date('d/m/Y',$plan->date)?>"/>
                                            </div>
                                            <div class="col-50">
                                                <label class="table_label"><?=__('Status')?></label>
                                                <input type="text" class="table_input disabled-input" value="<?=__($plan->status)?>"/>
                                            </div>
                                            <div class="clear"></div>
    
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="choose-view-format">
                                                        <span class="choose-view-format-title"><?=__('Choose view format')?>: </span>
                                                        <ul class="choose-view-format-list">
                                                            <?$i = 0?>
                                                            <?$file = $plan->files->where('status','=',Enum_FileStatus::Active)->find()?>
                                                            <li>
                                                                <a data-url="<?=$file->getImageLink()?>" data-fileid="<?=$file->id?>" data-ext="<?=$file->mime?>" data-controller="update_quality_control_plan_image"  class="call-lit-plugin" title="<?=$file->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($file->ext)?>.png" alt="<?=$file->ext?>"/></a>
    
                                                            </li>
    
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Name/Type">
                                        <div class="div-cell break-c">
                                            <?=$plan->name?>
                                        </div>
                                    </td>
                                    <td data-th="Profession">
                                        <div class="div-cell">
                                            <?=$plan->profession->name?>
                                        </div>
                                    </td>
                                    <td data-th="Floor">
                                        <div class="div-cell">
                                            <?=$plan->getFloorsAsString() ? :'-'?>
                                        </div>
                                    </td>
                                    <td data-th="Element number">
                                        <div class="div-cell">
                                            <?if($plan->place_id):?>
                                                <?=isset($plan->place->custom_number) ? $plan->place->custom_number : $plan->place->number?>
                                            <?else:?>
                                                -
                                            <?endif?>
                                        </div>
                                    </td>
                                    <td data-th="Edition">
                                        <div class="div-cell">
                                            <?=$plan->edition?>
                                        </div>
                                    </td>
                                    <td data-th="Date">
                                        <div class="div-cell">
                                            <?=date('d/m/Y',$plan->date)?>
                                        </div>
                                    </td>
                                    <td data-th="Image">
    
                                        <?$i = 0; $ext = null?>
                                        <?foreach ($plan->files->where('status','=',Enum_FileStatus::Active)->find_all() as $img):?>
                                            <?if($i > 1) break?>
                                            <?if($img->ext != $ext) $ext = $img->ext; else continue?>
                                            <a href="<?=$img->originalFilePath()?>" target="_blank" title="<?=$img->original_name?>"><img src="/media/img/choose-format/format-<?=strtolower($img->ext)?>.png" alt="<?=$img->ext?>"/></a>
                                        <?endforeach;?>
                                    </td>
                                </tr>
                            <?endforeach;?>
                            </tbody>
                        </table>
    
    
                    </div>
                </div>
                <div class="panel-modal-footer text-right">
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="#" class="inline_block_btn blue-light-button confirm-plan"><?=__('Confirm')?></a>
                        </div>
                    </div>
                </div>
            </div><!-- Modal content close-->
    
        </div>
    </div>
    `,
    data() {
        return {};
    },
    created() {
        // this.tabs = this.$children;
    },
    methods: {
        close() {
            console.log(444);
            this.$emit('close');
        },
    }
});
// Компонент конкретного таба
// Vue.component('tab', {
//
//     template: `
//         <div v-show="isActive"><slot></slot></div>
//     `,
//     props: {
//         name: {required: true},
//         selected: {default: false}
//     },
//     data() {
//         return {
//             isActive: false
//         };
//
//     },
//     computed: {
//         href() {
//             return '#' + this.name.toLowerCase().replace(/ /g, '-');
//         }
//     },
//     mounted() {
//         this.isActive = this.selected;
//     }
// });
