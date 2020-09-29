<?php

/**
 * Class GSTranslator
 * a simple translation module for the plugin
 * 
 * @author Eddy Lackmann <eddy.lackmann@limeSurvey.org>
 * @license GPL 2.0 or later
 *
 * 
 */
class GSTranslator
{

    const DEFAULT_LNG = 'en';

    public static $availableLanguages = ["en", "de", "fr"];

    public static function translate($original, $lng = '')
    {
        $lng = $lng != "" ? $lng : App()->language;

        //Retrieve all translations
        $translations = self::translations();

        $translated = $original;

        if (isset($translations[$original])) {

            if (isset($translations[$original][$lng])) {
                $translated = $translations[$original][$lng];
            } else {
                if (isset($translations[$original][self::DEFAULT_LNG])) {
                    $translated = $translations[$original][self::DEFAULT_LNG];
                }
            }
        }

        return $translated;
    }

    /**
     * This method returns all Translations 
     * 
     * @return array translations
     */
    public static function getAllTranslations()
    {
        return self::translations();
    }


    /**
     * Contains all translation available for the plugin
     * 
     * @return array of translation
     */
    private static function translations()
    {
        $translations = [

            "Grouped Statistics (Addon) - Settings" => [
                "en" => "Grouped Statistics (Addon) - Settings",
                "de" => "Gruppierte Statistik (Erweiterung) - Einstellung",
                "fr" => "Statistiques publiques (Addon) - Paramètres"
            ],

            "This addon requires the PublicStatistics plugin on your system" => [
                "en" => "This addon required the PublicStatistics plugin on your system",
                "de" => "Für dieses Addon ist das PublicStatistics-Plugin auf Ihrem System erforderlich",
                "fr" => "Cet addon nécessite le plugin PublicStatistics sur votre système"
            ],

            "Please activate the PublicStatistics plugin first." => [
                "en" => "Please activate the PublicStatistics plugin first.",
                "de" => "Bitte aktivieren Sie zuerst das PublicStatistics Plugin.",
                "fr" => "Veuillez d'abord activer le plugin PublicStatistics."
            ],

            "Grouped Statistics (Addon)" => [
                "en" => "Grouped Statistics (Addon)",
                "de" => "Gruppierte Statistik (Erweiterung) ",
                "fr" => "Statistiques publiques (Addon"
            ],

            "Settings for grouped statistics" => [
                "en" => "Settings for grouped statistics",
                "de" => "",
                "fr" => ""
            ],

            "Save settings" => [
                "en" => "Save settings",
                "de" => "Einstellung speichern",
                "fr" => "Sauvegarder"
            ],

            "Setting saved." => [
                "en" => "Setting saved.",
                "de" => "Einstellung gespeichert",
                "fr" => "Paramètres sauvegardé"
            ],

            "Setting can't be saved." => [
                "en" => "Setting can't be saved.",
                "de" => "Einstellung konnte nicht gespeichert werden",
                "fr" => "Paramètres non sauvegardé"
            ],

            "Available logins" => [
                "en" => "Available logins",
                "de" => "Verfügbare Anmeldungen",
                "fr" => "Identifiants disponibles"
            ],

            "Action" => [
                "en" => "Action",
                "de" => "Aktion",
                "fr" => "Action"
            ],

            "Save" => [
                "en" => "Save",
                "de" => "Speichern",
                "fr" => "Sauvegarder"
            ],

            "Close" => [
                "en" => "Close",
                "de" => "Schließen",
                "fr" => "Fermer"
            ],


            "Please type in the participation token:" => [
                "en" => "Please type in the participation token:",
                "de" => "Bitte geben Sie den Teilnahme-Token ein:",
                "fr" => "Veuillez saisir le mot secret de participation:"
            ],

            "Submit" => [
                "en" => "Submit",
                "de" => "Senden",
                "fr" => "Envoyer"
            ],

            "You have no permission to enter this page." => [
                "en" => "You have no permission to enter this page.",
                "de" => "Sie haben keine Berechtigung, diese Seite zu betreten.",
                "fr" => "Vous n'êtes pas autorisé à accéder à cette page."
            ],


            "Generate" => [
                "en" => "Generate",
                "de" => "Generieren",
                "fr" => "Générer"
            ],

            "Reset" => [
                "en" => "Reset",
                "de" => "Zurücksetzen",
                "fr" => "Réinitialiser"
            ],
            
            "Select surveys" => [
                "en" => "Select surveys",
                "de" => "Umfragen auswählen",
                "fr" => "Sélectionnez questionaires"
            ],
             
            "Check" => [
                "en" => "Check",
                "de" => "Prüfen",
                "fr" => "Vérifier"
            ],
             
            "Reseted" => [
                "en" => "Reseted",
                "de" => "Zurückgesetzt",
                "fr" => "Réinitialisé"
            ],

            "Check successfully done" => [
                "en" => "Check successfully done",
                "de" => "Erfolgreich geprüft",
                "fr" => "Vérifié avec succès"
            ],

            "Initialized successfully" => [
                "en" => "Initialized successfully",
                "de" => "Erfolgreich initialisiert",
                "fr" => "Initialisé avec succès"
            ],

            "Initialisation failed" => [
                "en" => "Initialisation failed",
                "de" => "Initialisierung fehlgeschlagen",
                "fr" => "Erreurs lors de l'initialisation"
            ],

            "Initialise" => [
                "en" => "Initialise",
                "de" => "Initialisieren",
                "fr" => "Initialiser"
            ],

            "Module not initialised." => [
                "en" => "Module not initialised.",
                "de" => "Modul nicht initialisiert.",
                "fr" => "Module non initialisé."
            ],

            "Survey not active" => [
                "en" => "Survey not active",
                "de" => "Umfrage nicht aktiv",
                "fr" => "Questionnaire non actif"
            ],

            "Public Statistics not active" => [
                "en" => "Public Statistics not active.",
                "de" => "Öffentliche Statistik nicht aktiviert",
                "fr" => "Statistiques publiques non actives"
            ],

            "Please activate the public statistics first!" => [
                "en" => "Please activate the public statistics first!",
                "de" => "Bitte aktivieren Sie zuerst die öffentlichen Statistiken!",
                "fr" => "Veuillez d'abord activer les statistiques publiques!"
            ],

            "This addon is only available for an activated survey!" => [
                "en" => "This addon is only available for an activated survey!",
                "de" => "Diese Erweiterung ist nur für eine aktivierte Umfrage verfügbar!",
                "fr" => "Cet addon n'est disponible que pour un questionnaire activé!"
            ],

            "Title" => [
                "en" => "Title",
                "de" => "Titel",
                "fr" => "Titre"
            ],

            "Common questions" => [
                "en" => "Common questions",
                "de" => "Gemeinsame Fragen",
                "fr" => "Questions courantes"
            ],            

        ];

        return $translations;
    }
}
