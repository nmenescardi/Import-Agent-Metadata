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

  $scvFile = new \IAM\SCVFile(); 
