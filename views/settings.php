<div class='side-body <?= getSideBodyClass(false); ?>'>
    <div class="container-center">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="pagetitle"><?php echo  GSTranslator::translate("Grouped Statistics (Addon) - Settings") ?></h3>
            </div>
        </div>
        <?php echo TbHtml::form(array("plugins/direct/plugin/PublicStatistics/method/saveinsurveysettings"), 'post', array('name' => 'psinsurveysettings', 'id' => 'psinsurveysettings')); ?>
        <div class="row">
            <div class="col-sm-12 text-right ls-space margin bottom-10">
                <button type="submit" class="btn btn-success pull-right" id="ps--save-button">
                    <i class="fa fa-save"></i>
                    <?php echo  PSTranslator::translate("Save settings") ?>
                </button>
            </div>
        </div>
        </form>
    </div>
</div>