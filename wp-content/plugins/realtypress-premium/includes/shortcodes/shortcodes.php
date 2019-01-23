<?php 
foreach( glob( dirname(__FILE__) . "/shortcode_rps-*.php" ) as $filename ) {
  require_once( $filename );
}