<?php

spl_autoload_register(function ($class_name) {
    if (preg_match("/^GS.*/", $class_name)) {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            include __DIR__ . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $class_name . '.php';
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
        /**
         * Here you should handle subscribing to the events your plugin will handle
         */
        $this->subscribe('beforeActivate');
        $this->subscribe('beforeDeactivate');
        $this->subscribe('beforeAdminMenuRender');
        $this->subscribe('newUnsecureRequest');
        $this->subscribe('newDirectRequest');
    
    }

    /**
     * Operations that run when the plugin is activated
     * 
     */
    public function beforeActivate()
    {
        if(GSInstaller::model()->checkParentPlugin()){
            GSInstaller::model()->installMenues();
            GSInstaller::model()->installAddonHooks();
        }
          
    }

    /**
     * Operations that run when the plugin is deactivated
     * 
     */
    public function beforeDeactivate()
    {
        GSInstaller::model()->removeMenues();
        GSInstaller::model()->removeAddonHooks();
    }


    /**
     * Actions to run when admin menu renders
     * @return void
     */
    public function beforeAdminMenuRender()
    {
        //PSInstaller::model()->proccessUpdate();
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
     * Render the setting page for a single survey
     * 
     * @return mixed
     * 
     */
    public function settings()
    {
        $sid = Yii::app()->request->getParam('surveyid');

        $aData = [];
       
        return $this->renderPartial('settings', $aData, true);
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
