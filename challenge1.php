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

// Set what flavor and size you want.
$desiredImage = "CentOS 6.3";
$desiredFlavor = "512MB Standard Instance";
$serverAmount = "1";

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

// first, find the image
$imageList = $compute->ImageList();
$found = FALSE;

//Copy this while loop and flavor while loop later to build array and make interactive menu for ability to choose OS and flavor
while (!$found && $image = $imageList->Next())
{
	if ($image->name == $desiredImage)
	{
		$found = TRUE;
		$myImage = $image;
	}
}

if($found == FALSE)
{
	print("Image $desiredImage not found!!\n");
	die;
}

// next, find the flavor
$flavorList = $compute->FlavorList();
$found = FALSE;

while (!$found && $flavor = $flavorList->Next())
{
	if ($flavor->name == $desiredFlavor)
	{
		$myFlavor = $flavor;
		$found = TRUE;
	}
}

if($found == FALSE)
{
        print("Flavor $desiredFlavor not found!\n");
        die;
}

for($i=0;$i<$serverAmount;$i++)
{
	// let's create the server
	$server = $compute->Server();
	$server->name = "bradm-web$serverAmount";
	$server->Create(array(
			'image' => $myImage,
			'flavor' => $myFlavor));
	$server->WaitFor("ACTIVE", 600);
	print_r($server);
	
	echo "Server {$server->name} Details - IP: {$server->accessIPv4} - Root Password: {$server->adminPass}\n";
}
?>
