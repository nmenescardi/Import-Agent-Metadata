<?php

namespace IAM;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class IAMLogger {

  const LOG_FILE_RELATIVE_PATH = 'logs/IAM.log';

  private $logger;

  public function __construct(string $logs_path){

    $this->logger = new Logger('IAM-Plugin');
    $this->logger->pushHandler(new StreamHandler($logs_path . self::LOG_FILE_RELATIVE_PATH, Logger::DEBUG));
    $this->logger->pushHandler(new FirePHPHandler());

  }

  public function logWarning( $message ){
    $this->logger->warning( $message );
  }

  public function logError( $message ){
    $this->logger->error( $message );
  }

  public function logInfo( $message ){
    $this->logger->info( $message );
  }

}