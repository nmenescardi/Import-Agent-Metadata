<?php

namespace IAM;

use \IAM\IAMLogger;

class IAM {

  private $plugin_path;

  private $logger;

  public function __construct(string $plugin_path){
    $this->plugin_path = $plugin_path;

    $this->logger = new IAMLogger( $plugin_path ); 

    //$this->logger->logError('Test');
  } 


  public static function getLogger(){

  }
}