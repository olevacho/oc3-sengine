<?php
/*
  Plugin Name: OC3 Search engine
  Plugin URI:
  Description: Semantic search of your website content.
  Author: olevacho
  Author URI: https://github.com/olevacho/
  Text Domain: oc3-sengine
  Domain Path: /lang
  Version: 1.0.1
  License:  GPL-2.0+
  License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if (version_compare('5.3', phpversion(), '>')) {
/* translators: placeholder mean current version of PHP */
    die(sprintf(esc_html__('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s) - please upgrade or contact your system administrator.','oc3-sengine'), esc_html(phpversion())));
}

//Define constants

define('OC3SENGINE_PREFIX', 'OC3SENGINE_');
define('OC3SENGINE_PREFIX_LOW', 'oc3sengine_');
define('OC3SENGINE_PREFIX_SHORT', 'oc3se_');
define('OC3SENGINE_CLASS_PREFIX', 'Oc3Sengine_');
define('OC3SENGINE_PATH', plugin_dir_path(__FILE__));
define('OC3SENGINE_URL', plugins_url('', __FILE__));
define('OC3SENGINE_PLUGIN_FILE', __FILE__);
define('OC3SENGINE_TEXT_DOMAIN', 'oc3-sengine');
define( 'OC3SENGINE_CHATGPT_BOT_PREFIX', 'oc3sengine_chatbot_' );
define( 'OC3SENGINE_CHATGPT_BOT_OPTIONS_PREFIX', 'oc3sengine_chatbot_opt_' );
define('OC3SENGINE_VERSION', '1.0.1');
//Init the plugin
require_once OC3SENGINE_PATH . '/lib/helpers/Utils.php';
require_once OC3SENGINE_PATH . '/lib/Oc3Sengine.php';
require_once OC3SENGINE_PATH . '/lib/controllers/BaseController.php';
require_once OC3SENGINE_PATH . '/lib/controllers/AdminController.php';
require_once OC3SENGINE_PATH . '/lib/dispatchers/FrontendDispatcher.php';

register_activation_hook(__FILE__, array('Oc3Sengine', 'install'));
register_deactivation_hook(__FILE__, array('Oc3Sengine', 'deactivate'));
register_uninstall_hook(__FILE__, array('Oc3Sengine', 'uninstall'));
new Oc3Sengine();
