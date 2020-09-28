<?php
/**
 * Not active View
 * 
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 *
 * 
 */
?>

<div class='side-body <?= getSideBodyClass(false); ?>'>
    <div class="container-center">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="pagetitle"><?php echo  GSTranslator::translate("Grouped Statistics (Addon) - Settings") ?></h3>
            </div>
        </div>
        <?php echo TbHtml::form(array("plugins/direct/plugin/GroupedStatistics/method/initStats"), 'post', array('name' => 'gsinsurveysettings', 'id' => 'gsinsurveysettings')); ?>
        <div class="row">
            <div class="col-sm-12 text-right ls-space margin bottom-10">
                <input type="hidden" name="surveyid" value="<?php echo  Yii::app()->request->getParam('surveyid'); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-right ls-space margin bottom-10">
                <div class="col-xs-12 jumbotron jumbotron-default well">
                    <h2><?php echo  PSTranslator::translate("Grouped Statistics is not initialized") ?></h2>
                    <p class="lead"><?php echo  PSTranslator::translate("The grouped Statistics is not initialised yet") ?></p>
                    <div class="col-sm-12 text-center ls-space margin bottom-10">
                        <button type="submit" class="btn btn-success" id="ps--save-button">
                            <i class="fa fa-rocket"></i>
                            <?php echo  GSTranslator::translate("Initialise") ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>
</div>