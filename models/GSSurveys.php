<?php

/**
 * Class GSSurveys
 * grouped Survey Statistics model
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 *
 * 
 */

class GSSurveys extends LSActiveRecord
{

    /**
     * @inheritdoc
     * @return PSSurveys
     */
    public static function model($class = __CLASS__)
    {
        /** @var self $model */
        $model = parent::model($class);
        return $model;
    }

    /** @inheritdoc */
    public function tableName()
    {
        return '{{GSSurveys}}';
    }

    /** @inheritdoc */
    public function primaryKey()
    {
        return 'sid';
    }

    /** @inheritdoc */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /** @inheritdoc */
    public function relations()
    {
        return array(
            'survey' => array(self::BELONGS_TO, 'Survey', 'sid'),
        );
    }


    /**
     * Initialise new grouped statistics for a survey
     *
     * @param int $sid Survey Id
     * @return bool
     */
    public  function initStats($sid)
    {

        $this->sid = $sid;
        $this->common_surveys = json_encode([]);
        $this->common_questions = json_encode([]);
        $this->stats_active = 1;

        return $this->save();
    }

    /**
     * Self delete
     *
     * @return bool
     */
    public function deactivateStats()
    {
        return $this->delete();
    }


    /**
     * Creates buttons for a gridView
     *
     * @return string
     */
    public function getButtons()
    {
        return '';
    }

    /**
     * 
     *
     * @return array
     */
    public function getColums()
    {
        return [];
    }
}
