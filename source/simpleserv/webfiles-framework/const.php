<?php

/*
 * #########################################################
 * ######################### webDEV - develop your webapps
 * #########################################################
 * ########################### copyrights by simpleserv.de
 * ########################## ###  ##      (c) 2007 - 2013
 * #########################################################
 * 
 * @author: Sebastian Monzel,
 *
 */ 

$webdevPath = "webfiles_framework/source/php";

define('FOLDER_SEPERATOR','/');

//-- system folders
define('ESSENTIAL_FUNCTIONS_FOLDER',$basePath . 'essential' . FOLDER_SEPERATOR . 'functions');
define('ESSENTIAL_CONFIGURATION_FOLDER',$basePath . 'essential' . FOLDER_SEPERATOR . 'configuration');

//-- custom folders
define('CUSTOM_FOLDER','./custom');
define('CUSTOM_TEMPLATE_FOLDER',CUSTOM_FOLDER . FOLDER_SEPERATOR . 'template');
define('CUSTOM_SITE_FOLDER',CUSTOM_FOLDER . FOLDER_SEPERATOR . 'site');
define('CUSTOM_BATCHJOB_FOLDER',CUSTOM_FOLDER . FOLDER_SEPERATOR . 'batchjob');

define('CONTENT_VAR_PREFIX','_con__');

