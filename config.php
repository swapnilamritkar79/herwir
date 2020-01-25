<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
//$CFG->dbname    = 'wirehouse';
$CFG->dbname    = 'wirehouse_stage';
$CFG->dbuser    = 'root';
$CFG->dbpass    = '';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

/* $CFG->wwwroot   = 'http://10.0.11.49';
$CFG->dataroot  = '/var/www/wirehousedata'; */
$CFG->wwwroot   = 'http://localhost/svn_wirehouse/wirehouse';
$CFG->dataroot  = 'D:\wamp64\wirehousedata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 02777;
$CFG->token = 'af3ca7a24352447ea6d342926301443a';

$CFG->hasinstructions = false;
$CFG->tax = 20;
$CFG->discount = 0;
$CFG->directCompanyRole = 16;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
