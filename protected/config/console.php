<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'   => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name'       => 'Lws Console',

    // preloading 'log' component
    'preload'    => array('log'),


    'import'     => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.helpers.*',
        'ext.giix-components.*', // giix components
    ),

    // application components
    'components' => array(
        'ts'  => [
            'class'      => 'application.components.TeamSpeak',
            'isBlocking' => false
        ],

        'db'  => array(
            'connectionString' => 'mysql:host=localhost;dbname=lws',
            'emulatePrepare'   => true,
            'username'         => 'lws',
            'password'         => 'lwsrulez',
            'charset'          => 'utf8',
            'enableProfiling'  => true,
        ),
        // uncomment the following to use a MySQL database
        /*
          'db'=>array(
              'connectionString' => 'mysql:host=localhost;dbname=testdrive',
              'emulatePrepare' => true,
              'username' => 'root',
              'password' => '',
              'charset' => 'utf8',
          ),
          */
        'log' => array(
            'class'  => 'CLogRouter',
            'routes' => array(
                array(
                    'class'  => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),
);