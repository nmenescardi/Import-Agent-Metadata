<?php

namespace IAM;

use \IAM\IAMLogger;
use \IAM\CSVFile;

class IAM {

  const TRANSIENT_CSV_ROWS = 'csv_rows_processed';
  const TRANSIENT_EXPIRATION = 'MINUTE_IN_SECONDS';

  private $plugin_path;

  private $logger;

  private $csv_file;

  public function __construct(string $plugin_path){
    $this->plugin_path = $plugin_path;

    $this->logger = new IAMLogger( $plugin_path ); 

    $this->csv_file = new CSVFile( $plugin_path . 'csv/to-process.csv');
    
  }

  public function process(){
    $this->processCSVFile();

    //$this->updateDB();
  }
  
  private function processCSVFile(){

    $rows = $this->getTransient();
    if( empty( $rows ) ) {
      $rows = $this->csv_file->getRows();
      $this->setTransient($rows);

      $this->logger->logInfo('Transient Expired. Reading CSV successfully');
    } else {
      
      $this->logger->logInfo('Returning rows in Transient');
    }
    //print_r( $rows );
  }

  private function getTransient(){
    return get_transient( self::TRANSIENT_CSV_ROWS );
  }

  private function setTransient( $payload ){
    set_transient( self::TRANSIENT_CSV_ROWS, $payload, self::TRANSIENT_EXPIRATION );
  }


  public static function getLogger(){
    return $this->logger;
  }
}