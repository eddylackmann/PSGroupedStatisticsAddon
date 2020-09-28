<?php
/**
 * Class GSInstaller 
 * This clas handle all insltall / Unsinstall / Update operations of the plugin
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 *
 * 
 */

class GSInstaller
{

    private static $model = null;

    public static function model()
    {

        if (self::$model == null) {
            self::$model = new self();
        }

        return self::$model;
    }
    /**
     * This function checks if the Parent statistics is installed
     *
     * @return bool 
     */
    public function checkParentPlugin()
    {
        $oModel = Plugin::model()->findByAttributes(['name' => 'PublicStatistics']);
        if ($oModel) {
            if ($oModel->active == 1) {
                return true;
            } else {
                throw new Exception(GSTranslator::translate('Please activate the PublicStatistics plugin first.'));
            }
        } else {
            throw new Exception(GSTranslator::translate('This addon requires the PublicStatistics plugin on your system'));
        }
    }

    /**
     * Install all needed database Table
     *
     * @return void
     */
    public function installTables()
    {

        $this->createTable('GSSurveys', array(
            'sid' => 'pk',
            'common_surveys' => 'text',
            'common_questions' => 'text',
            'stats_active' => 'int DEFAULT 0',
            'last_analysed' => 'datetime DEFAULT NULL',
        ));
    }


    /**
     * Delete database Tables of the plugin
     *
     * @return void
     */

    public function uninstallTables()
    {

        $oDB = Yii::app()->db;

        if (tableExists('GSSurveys')) {

            $oDB->createCommand()->dropTable('{{GSSurveys}}');
        }
    }



    /**
     * Install Menues for the frontend
     *
     * @return void
     */
    public function installMenues()
    {

        $aMenuSettings = [
            "name" => 'groupedstatssettings',
            "title" => 'groupedstatssettings',
            "menu_title" => GSTranslator::translate('Grouped Statistics (Addon)'),
            "menu_description" => GSTranslator::translate('Settings for grouped statistics'),
            "menu_icon" => 'area-chart',
            "menu_icon_type" => 'fontawesome',
            "menu_link" => 'admin/pluginhelper/sa/sidebody',
            "permission" => 'surveysecurity',
            "permission_grade" => 'update',
            "hideOnSurveyState" => false,
            "linkExternal" => false,
            "manualParams" => ['plugin' => 'GroupedStatistics', 'method' => 'settings'],
            "pjaxed" => true,
            "addSurveyId" => true,
            "addQuestionGroupId" => false,
            "addQuestionId" => false,
        ];

        $oMenu = Surveymenu::model()->findByAttributes(['name' => 'mainmenu']);
        return SurveymenuEntries::staticAddMenuEntry($oMenu->id, $aMenuSettings);
    }

    /**
     * Remove all frontend menus 
     *
     * @return void
     */
    public function removeMenues()
    {
        $result = false;

        $oSuerveymenuEntry = SurveymenuEntries::model()->findByAttributes(['name' => 'groupedstatssettings']);

        if ($oSuerveymenuEntry) {
            $result = $oSuerveymenuEntry->delete();
        }

        return $result;
    }

    /**
     * Run Plugin updates
     *
     * @return boolean
     */
    public function proccessUpdate()
    {
        $result = false;

        return $result;
    }


    /**
     * Create database table 
     *
     * @param string $tableName
     * @param array $arguments
     * @return bool
     */
    private function createTable(string $tableName, array $arguments)
    {

        $result = false;

        if (!tableExists($tableName)) {

            $oDB = Yii::app()->db;
            $oTransaction = $oDB->beginTransaction();
            try {

                $oDB->createCommand()->createTable('{{' . $tableName . '}}', $arguments);
                $oTransaction->commit();

                $result =  true;
            } catch (Exception $e) {
                $oTransaction->rollback();
                throw new CHttpException(500, $e->getMessage());
            }
        }

        return $result;
    }
}
