<?php

	require '/usr/local/bin/vendor/autoload.php'; // Include the SDK using Composer

	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;

	// Set the expiration time (in seconds)
	$expiration = 60; // 1 minute

	// Replace with your own access key, secret key, and region
	$access_key = 'AKIA5MFAAP5YCGIHN2XR';
	$secret_key = 'fw3uCxitsOtNYPtEiTeQKNqyGjfpre5+xwTzU2Yo';
	$region = 'eu-central-1';

	// Replace with your S3 bucket name and object key
	$bucket_name = 'sam-app-s3uploadbucket-1ut0y5lkfg694';
	$object_key = 'Roberto/test.jpg';

	// Create a new S3 client
	$s3 = new S3Client([
		'version' => 'latest',
		'region' => $region,
		'credentials' => [
			'key' => $access_key,
			'secret' => $secret_key,
		],
	]);

	try {
		// Generate a signed URL for the S3 object with an expiration time
		$url = $s3->getObjectUrl($bucket_name, $object_key, "+{$expiration} seconds");
		echo $url;
	} catch (S3Exception $e) {
		echo "Error: " . $e->getMessage();
	}

?>