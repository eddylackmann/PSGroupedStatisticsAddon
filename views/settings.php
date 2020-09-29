<?php

/**
 * Settings View
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
        <div class="row">
            <?php echo TbHtml::form(array("plugins/direct/plugin/GroupedStatistics/method/deactivateStats"), 'post', array('name' => 'GSSettings-reset', 'id' => 'GSSettings-reset')); ?>
            <div class="col-sm-12 text-right ls-space margin bottom-10">
                <input type="hidden" name="surveyid" value="<?php echo  Yii::app()->request->getParam('surveyid'); ?>">
            </div>
            <div class="col-sm-6  ls-space margin bottom-10 ">
                This addons helps you to integrate responses of other surveys (With same question and question Type) into the public statistics.
                Please select and analyse surveys to find how many common question they have.
            </div>
            <div class="col-sm-3 text-right ls-space margin bottom-10 pull-right">
                <button type="submit" class="btn btn-default" id="">
                    <i class="fa fa-refresh"></i>
                    <?php echo  GSTranslator::translate("Reset") ?>
                </button>
            </div>
            </form>
        </div>
        <hr>
        <div class="row">
            <?php echo TbHtml::form(array("plugins/direct/plugin/GroupedStatistics/method/analyse"), 'post', array('name' => 'GSinsurveysettings', 'id' => 'GSinsurveysettings')); ?>
            <input type="hidden" name="surveyid" value="<?php echo  Yii::app()->request->getParam('surveyid'); ?>">

            <?php if ($surveyList) : ?>
                <div class="col-sm-6">

                    <div class="form-group">
                        <label><?php echo GSTranslator::translate('Select surveys'); ?></label>

                        <select name="surveySelection[]" data-style="btn-default" class="selectpicker form-control" multiple="multiple" data-max-options="20" style="width:100%;">
                            <?php foreach ($surveyList as $survey) : ?>
                                <?php
                                $selected = '';
                                if (!isset($commonSurveys) && !$commonSurveys) {
                                    $commonSurveys = [];
                                }
                                if (in_array($survey['id'], $commonSurveys)) {
                                    $selected = 'selected';
                                }

                                ?>
                                <option <?php echo $selected; ?> value="<?php echo $survey['id']; ?>"><?php echo $survey['id'] . " - " . $survey['title']; ?> </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>
            <?php endif; ?>
            <div class="col-sm-12 text-left ls-space margin bottom-10 pull-right">
                <button type="submit" class="btn btn-primary" id="">
                    <i class="fa fa-arrow-right"></i>
                    <?php echo  GSTranslator::translate("Check") ?>
                </button>
            </div>
            </form>
        </div>

        <hr>

        <div class="row">
            <div class="col-sm-12 text-left ls-space margin bottom-10">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col"><?php echo  GSTranslator::translate("Title") ?></th>
                            <th scope="col"><?php echo  GSTranslator::translate("Common questions") ?></th>
                            <th scope="col"><?php echo  gT("Questions"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ((isset($commonSurveys) && $commonSurveys)) : ?>
                            <?php foreach ($commonSurveys as $survey) : ?>
                                <?php
                                $color = "";
                                $questions = "";
                                $count = count($commonQuestions[$survey]['common']);

                                if (isset($commonQuestions[$survey]['common']) && $commonQuestions[$survey]['common']) {
                                    foreach ($commonQuestions[$survey]['common'] as $q) {
                                        $questions .= $q["title"] . ", ";
                                    }
                                }

                                if ($count == 0) {
                                    $color = "red";
                                }
                                ?>
                                <tr style="color:<?php echo $color ?>!important">
                                    <td scope="row"><?php echo $survey; ?></td>
                                    <td><?php echo $commonQuestions[$survey]['surveyTitle']; ?></td>
                                    <td><?php echo $count; ?></td>
                                    <td style="max-width: 200px;"><?php echo $questions ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <hr>
        </div>

    </div>
</div>