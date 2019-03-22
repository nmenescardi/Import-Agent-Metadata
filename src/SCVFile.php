<?php

namespace IAM;

class SCVFile {

  private $path_scv_file;

  public function __construct( string $path_scv_file ){
    $this->path_scv_file = $path_scv_file;

    $this->processFile();
  }

  private function processFile(){

  }

  public function getRows(){

  }

}
