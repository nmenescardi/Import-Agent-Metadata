<?php

namespace IAM;

use \IAM\IAMLogger;
use \IAM\CSVFile;

class IAM {

  const TRANSIENT_CSV_ROWS = 'csv_rows_processed';
  const TRANSIENT_EXPIRATION_CSV = 'HOUR_IN_SECONDS';

  private $plugin_path;
  private $plugin_file_path;

  private $logger;

  private $csv_file;

  public function __construct(string $plugin_path, string $plugin_file_path ){
    $this->plugin_path = $plugin_path;
    $this->plugin_file_path = $plugin_file_path ;

    $this->logger = new IAMLogger( $plugin_path ); 

    $this->csv_file = new CSVFile( $plugin_path . 'csv/to-process.csv');

    register_activation_hook( $this->plugin_file_path , array( $this, 'process' ) );
  }

  public function process(){
    $rows = $this->processCSVFile();

    if ( false !== $rows ) {
      $this->updateDB( $rows );
    } else {
      $this->logger->logInfo('the DB was already updated.');
    }

    //deactivate_plugins( $this->plugin_file_path );
  }
  
  private function processCSVFile(){
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
    
      if ( false !== $id && '' !== $id ) {

        if( add_post_meta( $id, 'our_ao-userid', $userId, true ) ) {

          $this->logger->logInfo( 'User ID Inserted for user: ' . $userId . ' - ' . $id . ' - ' . $email . ' - ' . $name);
          
        } else {
          
          update_post_meta ( $id, 'our_ao-userid', $userId );

          $this->logger->logInfo( 'User ID updated for user: ' . $userId . ' - ' . $id . ' - ' . $email . ' - ' . $name);
        }
      }
    }
  }

  private function getTransientCSV(){
    return get_transient( self::TRANSIENT_CSV_ROWS );
  }

  private function setTransientCSV( $payload ){
    set_transient( self::TRANSIENT_CSV_ROWS, $payload, self::TRANSIENT_EXPIRATION_CSV );
  }

  public static function getLogger(){
    return $this->logger;
  }
}
