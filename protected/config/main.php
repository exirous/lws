<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name'       => 'Школа виртуальных пилотов LuftwaffeSchule',

    // preloading 'log' component
    'preload'    => array('log'),

    // autoloading model and component classes
    'import'     => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.helpers.*',
        'ext.giix-components.*', // giix components
        'ext.yii-mail.YiiMailMessage'
    ),

    'modules'    => array(
        'gii' => array(
            'class'          => 'system.gii.GiiModule',
            'generatorPaths' => array(
                'ext.giix-core', // giix generators
            ),
            'password'       => 'qwe123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'      => array('*'),
        ),

    ),
    // application components
    'components' => array(
        'ts'=>[
            'class'  => 'application.components.TeamSpeak',
        ],
        'user'         => array(
            'class'=>'application.components.AWebUser',
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            /*'session'        => array(
                'autoStart' => true,
            ),*/
        ),

        'image'        => array(
            'class'  => 'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver' => 'GD',
            // ImageMagick setup path
            'params' => array('directory' => '/opt/local/bin'),
        ),
        'request' => [
            'class' => 'AHttpRequest'
        ],
        'urlManager'   => array(
            'urlFormat'      => 'path',
            'showScriptName' => false,
            'rules'          => require(dirname(__FILE__) . '/path.php'),
        ),

        'db'           => array(
            'connectionString' => 'mysql:host=localhost;dbname=lws',
            'emulatePrepare'   => true,
            'username'         => 'lws',
            'password'         => 'lwsrulez',
            'charset'          => 'utf8',
            'enableProfiling'  => true,
        ),

        'clientScript' => array(
            'packages' => array(
                'jquery' => array(
                    'baseUrl' => '/scripts/',
                    'js'      => array('jquery.js'),
                )
            ),
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),

        'cache'        => array(
            'class' => 'CFileCache'
        ),

        'log'          => array(
            'class'  => 'CLogRouter',
            'routes' => array(array(
                    'class' => 'CWebLogRoute',
                    'levels' => 'error, warning, notice',
                ),
                array(
                    'class' => 'CProfileLogRoute',
                    'enabled' => false,
                    'report' => 'summary',
                )
                // uncomment the following to show log messages on web pages

/*                    array(
                        'class'=>'CWebLogRoute',
                    ),*/

            ),
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'     => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'language'   => 'ru'
    ),
);