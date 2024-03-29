<?php
/**
 * Plugin Name:     Import Agent Metadata
 * Plugin URI:      http://github.com/nmenescardi/Import-Agent-Metadata
 * Description:     This plugin is used to import specific metadata related with Agents CPT.
 * Author:          Nicolas Menescardi
 * Author URI:      http://github.com/nmenescardi
 * Text Domain:     import-agent-metadata
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Import_Agent_Metadata
 */

  require_once 'vendor/autoload.php';
  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  
  use \IAM\IAM;
 
  $iam_plugin = new IAM( plugin_dir_path( __FILE__ ), plugin_basename( __FILE__ )  ); 
  $iam_plugin->process();
