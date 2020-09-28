<?php

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
                $questions[$sQuestion->title] = [
                    "title" => $sQuestion->title,
                    "type" => $sQuestion->type,
                    "template" => QuestionAttribute::model()->getQuestionAttributes($sQuestion->qid)['question_template'],
                ];
            }
        }

        $result = [];
        $result["surveyTitle"] = $survey->getLocalizedTitle();

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
                    "qid"    => $question->qid,
                    "title" => $question->title,
                    "type"  => $question->type,
                    "template" =>  QuestionAttribute::model()->getQuestionAttributes($question->qid)['question_template'],
                ];
            }
        }

        return $baseQuestions;
    }
}
