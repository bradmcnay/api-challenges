#!/usr/bin/php
<?php
// Author: Brad McNay (assistance from samples in php-opencloud, https://github.com/rackspace/php-opencloud)
// Ver: 0.1

namespace OpenCloud;

ini_set('include_path', './lib:'. $_SERVER["HOME"] . ':'.ini_get('include_path'));

require_once('.rackspace_cloud_credentials');
require_once('rackspace.php');
require_once('compute.php');

define('AUTHURL', RACKSPACE_US);
define('USERNAME', $_ENV['api_user']);
define('TENANT', $_ENV['tenant']);
define('APIKEY', $_ENV['api_key']);

// Setup the server details we need to make a clone 
$desiredImageName = "bradm-test-clone-web1";
$desiredServerToClone = "bradm-web1";

// establish our credentials
$connection = new Rackspace
(
	AUTHURL,
	array
	(
		'username' => USERNAME,
		'apiKey' => APIKEY
	)
);

// now, connect to the compute service
$compute = $connection->Compute('cloudServersOpenStack', 'DFW');

// Find the server ID
$serverList = $compute->ServerList(FALSE, array('name'=>'bradm-web1'));
if($serverList->Size() == 1)
{
	$server = $serverList->Next();
}
else
{
	echo "We didn't find exactly one server named $desiredServerToClone.\n";
	die;
}

// first, create the image
$imageDetail = $server->CreateImage($desiredImageName);
echo "imagedetail is\n";
print_r($imageDetail);
die;
if($server->CreateImage($desiredImageName) == TRUE)
{
}
else
{
	echo "There was a problem creating the image\n";
}
/*$server->name = "bradm-web$serverAmount";
$server->Create(array(
		'image' => $myImage,
		'flavor' => $myFlavor));
$server->WaitFor("ACTIVE", 600);
*/
?>
