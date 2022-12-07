<?php
  require './vendor/autoload.php';

  $dotenv =  Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
  $dotenv->load();

  echo "hi ".getenv('DEMO')." bro.".PHP_EOL;
  putenv('DEMO=666666');
  echo "hi ".getenv('DEMO')." bro.".PHP_EOL;
  
