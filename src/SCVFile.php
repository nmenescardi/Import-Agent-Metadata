<?php

namespace IAM;

class CSVFile {

  private $path_csv_file;

  private $rows;

  public function __construct( string $path_csv_file ) {
    $this->path_csv_file = $path_csv_file;
  }

  private function processFile() {
    
    if ( !isset( $this->rows ) || empty( $this->rows ) ) {

      $file = fopen( $this->path_csv_file ,"r");
      
      while( !feof( $file ) ) {
        $this->rows[] = fgetcsv($file);
      }
      
      fclose($file);
      
    }

  }

  public function getRows(){
    $this->processFile();
    
    return $this->rows;
  }

}
