<?php

use Zend\Http\Header\Date;

/**
 * Class GSHelper
 * some helper functionalities of the plugin to get the controller clean 
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 * 
 */

class GSHelper
{

    /**
     * Filter survey list for select box in setttings view
     *
     * @param  $sid surveyid 
     * @return array 
     */
    public static function getFilteredSurveyList($sid)
    {
        $surveyList = [];
        //retrieve all active surveys for the selection 
        $surveys = Survey::model()->findAllByAttributes(['active' => 'Y']);
        if ($surveys) {
            foreach ($surveys as $survey) {
                if ($survey->sid != $sid) {
                    $surveyList[] = [
                        "id" => $survey->sid,
                        "title" => $survey->getLocalizedTitle(),
                    ];
                }
            }
        }

        return $surveyList;
    }

    /**
     * Compare a selected survey questions with the base questions
     *
     * @param array $base
     * @param int $sid
     * @return array
     */
    public static function compareQuestionsWithBaseSurvey(array $base, $sid)
    {

        $questions = [];
        $survey = Survey::model()->findByPk($sid);
        $surveyQuestions = Question::model()->findAllByAttributes(['sid' => $sid, 'parent_qid' => 0]);
        if ($surveyQuestions) {
            foreach ($surveyQuestions as $sQuestion) {

                if (isset($base[$sQuestion["title"]])) {
                    $questions[$sQuestion->title] = [
                        "origin_fieldname" => $base[$sQuestion["title"]]["fieldname"],
                        "fieldname" => "{$sQuestion->sid}X{$sQuestion->gid}X{$sQuestion->qid}",
                        "gid" => $sQuestion->gid,
                        "qid" => $sQuestion->qid,
                        "title" => $sQuestion->title,
                        "type" => $sQuestion->type,
                        "template" => QuestionAttribute::model()->getQuestionAttributes($sQuestion->qid)['question_template'],
                    ];
                }
            }
        }

        $result = [];
        $result["surveyTitle"] = $survey->getLocalizedTitle();
        $result["surveyId"] = $sid;

        foreach ($questions as $question) {
            if (isset($base[$question["title"]])) {
                if ($question["template"] == $base[$question["title"]]["template"]) {
                    $result["common"][] = $question;
                }
            } else {
                $result["common"] = [];
            }
        }

        return $result;
    }

    /**
     * retrieve base questios of a survey
     *
     * @param int $sid
     * @return array
     */
    public static function getSurveyBaseQuestions($sid)
    {
        $baseQuestions = [];
        $questions =  Question::model()->findAllByAttributes(['sid' => $sid, 'parent_qid'  => 0]);

        if ($questions) {
            foreach ($questions as $question) {
                $baseQuestions[$question->title] = [
                    "fieldname" => "{$question->sid}X{$question->gid}X{$question->qid}",
                    "qid"    => $question->qid,
                    "title" => $question->title,
                    "type"  => $question->type,
                    "template" =>  QuestionAttribute::model()->getQuestionAttributes($question->qid)['question_template'],
                ];
            }
        }

        return $baseQuestions;
    }

    /**
     * This function sychronize the result of the grouped survey to the public statistics 
     *
     * @param int $sid
     * @return void
     */
    public static function synchronizeToPublicStatistics($sid)
    {
        $settings = GSSurveys::model()->findByPk($sid);
        $result = true;
        $data = [];
        if ($settings) {
            $data = json_decode($settings->common_questions);
            if (class_exists('PSHooksHelper')) {
                $result = PSHooksHelper::appendHooks('addRelatedSurveyResponses', $sid, $data);
                if($result){
                    $settings->last_synchronized =  new CDbExpression('NOW()');
                    $settings->save();
                }
            }
        }

        return $result;
    }

    /**
     * This function sychronize the result of the grouped survey to the public statistics 
     *
     * @param int $sid
     * @return void
     */
    public static function deleteHook($sid = 0)
    {
        $result = false;

        if (class_exists('PSHooksHelper')) {
            if($sid != 0){
                $result = PSHooksHelper::deleteHooks('addRelatedSurveyResponses', $sid);
            }else{
                $result = PSHooksHelper::deleteHooks('addRelatedSurveyResponses');
            }
            
        }
        return $result;
    }


}
