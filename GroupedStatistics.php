<?php

/**
 * GroupedStatistics Controller
 * An addon plugin for public statistics that checks for same questions in other 
 * activated surveys and add the responses to the public statistics 
 * 
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 * @category Plugin (Addon)
 * @requires PublicStatistics Plugin 
 */

//Load libraries and components
spl_autoload_register(function ($class_name) {
    if (preg_match("/^GS.*/", $class_name)) {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            include __DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $class_name . '.php';
        }
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            include __DIR__ . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $class_name . '.php';
        }
    }
});

class GroupedStatistics extends PluginBase
{
    protected $storage = 'DbStorage';
    static protected $description = 'Grouped Statistics (Public Statistics Addon) - Statistics for surveys that have the same questions';
    static protected $name = 'GroupedStatistics';


    public function init()
    {

        //Suscribe to plugin events in LS
        $this->subscribe('beforeActivate');
        $this->subscribe('beforeDeactivate');
        $this->subscribe('beforeAdminMenuRender');
        $this->subscribe('newUnsecureRequest');
        $this->subscribe('newDirectRequest');
    }

    /**
     * Operations that run before the plugin is activated
     * 
     * @return void
     */

    public function beforeActivate()
    {
        if (GSInstaller::model()->checkParentPlugin()) {
            GSInstaller::model()->installMenues();
            GSInstaller::model()->installTables();
        }
    }

    /**
     * Operations that run before the plugin is deactivate
     * 
     * @return void
     */
    public function beforeDeactivate()
    {
        GSInstaller::model()->removeMenues();
        GSInstaller::model()->uninstallTables();
    }


    /**
     * Actions to run when admin menu renders
     * @return void
     */
    public function beforeAdminMenuRender()
    {
        //code
    }

    /**
     * Relay a direct request to the called method
     *
     * @return void
     */
    public function newDirectRequest()
    {
        $request = $this->api->getRequest();
        $oEvent = $this->getEvent();

        if ($oEvent->get('target') !== 'GroupedStatistics') {
            return;
        }

        $action = $request->getParam('method');
        return call_user_func([$this, $action], $oEvent, $request);
    }

    /**
     * Relays an unsecure request to the called method
     *
     * @return void
     */

    public function newUnsecureRequest()
    {
        $request = $this->api->getRequest();
        $oEvent = $this->getEvent();

        if ($oEvent->get('target') !== 'GroupedStatistics') {
            return;
        }

        $action = $request->getParam('method');
        return call_user_func([$this, $action], $oEvent, $request);
    }


    /**
     * Render the setting page of the grouped Statistics module
     * 
     * @return mixed
     * 
     */
    public function settings()
    {
        //get current survey id
        $sid = Yii::app()->request->getParam('surveyid');

        //Fetch setting of the survey id 
        $oGSSurvey = GSSurveys::model()->findByAttributes(['sid' => $sid]);

        //Check if settings exists | if not redirect to the not Ative page
        if (!$oGSSurvey) {
            return $this->renderPartial('notActive', ['sid' => $sid], true);
        }

        $aData = [];

        //register scripts and css
        $this->registerAssets();

        //get survey list for the multiple selectbox
        $aData["surveyList"] = GSHelper::getFilteredSurveyList($sid);
        $aData["commonSurveys"] = json_decode($oGSSurvey->common_surveys);
        $aData["commonQuestions"] = json_decode($oGSSurvey->common_questions, true);

        return $this->renderPartial('settings', $aData, true);
    }


    /**
     * Initialise Grouped Statistics setting
     * 
     * @return mixed
     * 
     */
    public function initStats()
    {
        $sid = Yii::app()->request->getPost('surveyid');

        $oGSSurvey = GSSurveys::model()->findAllByAttributes(['sid' => $sid]);

        if (!$oGSSurvey) {
            $GS = new GSSurveys();
            if ($GS->initStats($sid)) {
                Yii::app()->setFlashMessage(PSTranslator::translate("Grouped statistics initiliased"), 'success');
            } else {
                Yii::app()->setFlashMessage(PSTranslator::translate("Error on initialisation"), 'error');
            }
        }

        return Yii::app()->getController()->redirect(
            Yii::app()->createUrl(
                'admin/pluginhelper/sa/sidebody',
                ['surveyid' => $sid, 'plugin' => 'GroupedStatistics', 'method' => 'settings']
            )
        );
    }

    /**
     * Delete Grouped Statistics Setting and Hooks in the Public Statistics
     * 
     * @return mixed
     * 
     */
    public function deactivateStats()
    {
        $sid = Yii::app()->request->getPost('surveyid');

        $oGSSurvey = GSSurveys::model()->findByAttributes(['sid' => $sid]);


        if ($oGSSurvey->deactivateStats()) {

            Yii::app()->setFlashMessage(GSTranslator::translate("Grouped statistics deactivated"), 'success');
        }

        return Yii::app()->getController()->redirect(
            Yii::app()->createUrl(
                'admin/pluginhelper/sa/sidebody',
                ['surveyid' => $sid, 'plugin' => 'GroupedStatistics', 'method' => 'settings']
            )
        );
    }

    /**
     * Analyse the settings and check for common questions in selected surveys
     * 
     * @return mixed
     * 
     */

    public function analyse()
    {
        //get survey id
        $sid = Yii::app()->request->getPost('surveyid');

        //retrieve selected surveys
        $ids = Yii::app()->request->getPost('surveySelection');

        if (!$ids) {
            $ids = [];
        }

        //find current Grouped Survey data
        $oGSSurvey = GSSurveys::model()->findByAttributes(['sid' => $sid]);

        if ($oGSSurvey) {

            //insert selected survey 
            $oGSSurvey->common_surveys = json_encode($ids);
            $result = [];

            //get current survey questions
            $baseQuestions = GSHelper::getSurveyBaseQuestions($sid);

            //Compare each questions with base question
            if ($baseQuestions && $ids) {
                foreach ($ids as $id) {
                    $result[$id] = GSHelper::compareQuestionsWithBaseSurvey($baseQuestions, $id);
                }
            }

            //Insert result into the database
            $oGSSurvey->common_questions = json_encode($result);

            if ($oGSSurvey->save()) {
                Yii::app()->setFlashMessage(GSTranslator::translate("Analyse done!!"), 'success');
            }
        }

        return Yii::app()->getController()->redirect(
            Yii::app()->createUrl(
                'admin/pluginhelper/sa/sidebody',
                ['surveyid' => $sid, 'plugin' => 'GroupedStatistics', 'method' => 'settings']
            )
        );
    }


    /**
     * Register all assets of the plugins
     *
     * @return void
     */
    private function registerAssets()
    {
        $this->registerScript('assets/js/GroupedStatistics.js', LSYii_ClientScript::POS_END);
        $this->registerScript('assets/js/bootstrap-select.min.js', LSYii_ClientScript::POS_END);
        $this->registerCss('assets/css/bootstrap-select.min.css');
    }

    /**
     * Adding a script depending on path of the plugin
     * This method checks if the file exists depending on the possible different plugin locations, which makes this Plugin LimeSurvey Pro safe.
     *
     * @param string $relativePathToScript
     * @param integer $pos See LSYii_ClientScript constants for options, default: LSYii_ClientScript::POS_BEGIN
     * @return void
     */
    protected function registerScript($relativePathToScript, $parentPlugin = null, $pos = LSYii_ClientScript::POS_BEGIN)
    {
        $parentPlugin = get_class($this);
        $pathPossibilities = [
            YiiBase::getPathOfAlias('userdir') . '/plugins/' . $parentPlugin . '/' . $relativePathToScript,
            YiiBase::getPathOfAlias('webroot') . '/plugins/' . $parentPlugin . '/' . $relativePathToScript,
            Yii::app()->getBasePath() . '/application/core/plugins/' . $parentPlugin . '/' . $relativePathToScript,
            //added limesurvey 4 compatibilities
            YiiBase::getPathOfAlias('webroot') . '/upload/plugins/' . $parentPlugin . '/' . $relativePathToScript,
        ];

        $scriptToRegister = null;
        foreach ($pathPossibilities as $path) {
            if (file_exists($path)) {
                $scriptToRegister = Yii::app()->getAssetManager()->publish($path);
            }
        }

        Yii::app()->getClientScript()->registerScriptFile($scriptToRegister, $pos);
    }

    /**
     * Adding a stylesheet depending on path of the plugin
     * This method checks if the file exists depending on the possible different plugin locations, which makes this Plugin LimeSurvey Pro safe.
     *
     * @param string $relativePathToCss
     * @return void
     */
    protected function registerCss($relativePathToCss, $parentPlugin = null)
    {
        $parentPlugin = get_class($this);

        $pathPossibilities = [
            YiiBase::getPathOfAlias('userdir') . '/plugins/' . $parentPlugin . '/' . $relativePathToCss,
            YiiBase::getPathOfAlias('webroot') . '/plugins/' . $parentPlugin . '/' . $relativePathToCss,
            Yii::app()->getBasePath() . '/application/core/plugins/' . $parentPlugin . '/' . $relativePathToCss,
            //added limesurvey 4 compatibilities
            YiiBase::getPathOfAlias('webroot') . '/upload/plugins/' . $parentPlugin . '/' . $relativePathToCss,
        ];

        $cssToRegister = null;
        foreach ($pathPossibilities as $path) {
            if (file_exists($path)) {
                $cssToRegister = Yii::app()->getAssetManager()->publish($path);
            }
        }

        Yii::app()->getClientScript()->registerCssFile($cssToRegister);
    }
}
