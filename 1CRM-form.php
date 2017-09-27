<?php
/**
 * Plugin Name: 1CRM Universal Forms
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Easy form generation for 1CRM / Lead Guerrilla / Mautic
 * Version: 1.2
 * Author: 1CRM Corp.
 * License: MY_OWN
 */

define( 'OCRMF_VERSION', '1.2' );
define('OCRMF_PLIGIN_DIR', dirname(__FILE__));
define ('OCRMF_INCLUDES_DIR', OCRMF_PLIGIN_DIR . '/includes');
define ('OCRMF_ADMIN_DIR', OCRMF_PLIGIN_DIR . '/admin');

define('OCRMF_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__ )));

define ('OCRMF_TEXTDOMAIN', 'onecrm_forms');

include OCRMF_PLIGIN_DIR . '/hooks.php';

