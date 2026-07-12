<?php

class IntentLoader
{
    public static function load($intentName)
    {
        $basePrivatePath = dirname(__DIR__, 3);

        $intentFile = $basePrivatePath.'/intents/'.$intentName.'.php';

        if (!is_file($intentFile)) {
            throw new Exception("Intent '{$intentName}' non trovato in: ".$intentFile);
        }

        global $app;

        if (!isset($app) || !is_object($app)) {
            $app = new stdClass();
        }

        if (!isset($app->private_root)) {
            $app->private_root = $basePrivatePath;
        }

        $intent = null;

        require $intentFile;

        if (!$intent || !is_object($intent)) {
            throw new Exception("Intent '{$intentName}' non valido.");
        }

        return $intent;
    }
}