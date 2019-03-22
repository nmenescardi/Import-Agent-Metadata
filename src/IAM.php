<?php

namespace IAM;

use \IAM\IAMLogger;
use \IAM\CSVFile;

class IAM {

  const TRANSIENT_CSV_ROWS = 'csv_rows_processed';
  const TRANSIENT_EXPIRATION_CSV = 'HOUR_IN_SECONDS';

  const TRANSIENT_DB_UPDATED = 'agent_id_updated';
  const TRANSIENT_EXPIRATION_DB_UPDATED = 'HOUR_IN_SECONDS';
  const TRANSIENT_DB_UPDATED_LABEL = 'AGENT_ID_UPDATED';

  private $plugin_path;

  private $logger;

  private $csv_file;

  public function __construct(string $plugin_path){
    $this->plugin_path = $plugin_path;

    $this->logger = new IAMLogger( $plugin_path ); 

    $this->csv_file = new CSVFile( $plugin_path . 'csv/to-process.csv');
    
  }

  public function process(){
    $rows = $this->processCSVFile();

    if ( false !== $rows ) {
      $this->updateDB( $rows );
    } else {
      $this->logger->logInfo('the DB was already updated.');
    }
  }
  
  private function processCSVFile(){

    // IF the DB was updated it doesn't repeat the process.
    if( self::TRANSIENT_DB_UPDATED_LABEL !== $this->getTransientDB() ) {

      $rows = $this->getTransientCSV();

      if( empty( $rows ) ) {

        $rows = $this->csv_file->getRows();
        $this->setTransientCSV($rows);

        $this->logger->logInfo('Transient Expired. Reading CSV successfully');
      } else {

        $this->logger->logInfo('Returning rows in Transient');
      }

      return $rows;

    }

    return false;
  }

  
  private function get_post_id_by_meta_key_and_value($key, $value) {
	  global $wpdb;
  
    $meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape($key)."' AND meta_value='".$wpdb->escape($value)."'");
  
    if (is_array($meta) && !empty($meta) && isset($meta[0])) {
	  	$meta = $meta[0];
		}	
    
    if ( is_object($meta) ) {
      
      $this->logger->logInfo( 'Agent Found for email: ' . $value );
      return $meta->post_id;
      
		}	else {
      
      $this->logger->logWarning( 'Agent NOT Found for email: ' . $value );
		  return false;
		}
	}


  private function updateDB( $rows ){
    $header = array_shift( $rows);
    
    foreach( $rows as $agent ){

      $email = $agent[2];
      $name = $agent[1];
      $userId = $agent[0];
      $id = $this->get_post_id_by_meta_key_and_value( 'our_ao-aema', $email );
    
      if ( false !== $id && '' !== $id && self::TRANSIENT_DB_UPDATED_LABEL !== $this->getTransientDB() ) {

        if( add_post_meta( $id, 'our_ao-userid', $userId, true ) ) {

          $this->logger->logInfo( 'User ID Inserted for user: ' . $userId . ' - ' . $id . ' - ' . $email . ' - ' . $name);
          
        } else {
          
          update_post_meta ( $id, 'our_ao-userid', $userId );

          $this->logger->logInfo( 'User ID updated for user: ' . $userId . ' - ' . $id . ' - ' . $email . ' - ' . $name);
        }

        $this->setTransientDB( self::TRANSIENT_DB_UPDATED_LABEL );

      }
    }
  }

  private function getTransientCSV(){
    return get_transient( self::TRANSIENT_CSV_ROWS );
  }

  private function setTransientCSV( $payload ){
    set_transient( self::TRANSIENT_CSV_ROWS, $payload, self::TRANSIENT_EXPIRATION_CSV );
  }

  private function getTransientDB(){
    return get_transient( self::TRANSIENT_DB_UPDATED );
  }

  private function setTransientDB( $payload ){
    set_transient( self::TRANSIENT_DB_UPDATED, $payload, self::TRANSIENT_EXPIRATION_DB_UPDATED );
  }


  public static function getLogger(){
    return $this->logger;
  }
}