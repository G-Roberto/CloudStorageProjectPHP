<?php

	require '/usr/local/bin/vendor/autoload.php'; // Include the SDK using Composer

	use Aws\S3\S3Client;
	use Aws\Exception\AwsException;

	// Set up the S3 client
	$client = new S3Client([
		'version' => 'latest',
		'region' => 'eu-central-1', // Replace with the appropriate region
		'credentials' => [
			'key' => 'AKIA5MFAAP5YCGIHN2XR',
			'secret' => 'fw3uCxitsOtNYPtEiTeQKNqyGjfpre5+xwTzU2Yo',
		],
	]);

	// Set the bucket name and object key
	$bucket = 'sam-app-s3uploadbucket-1ut0y5lkfg694';
	$key = 'Roberto/test.jpg';

	// Check if the access key and secret key have access to the bucket
	try {
		$client->headBucket(['Bucket' => $bucket]);
	} catch (AwsException $e) {
		echo "Error: " . $e->getMessage();
		exit();
	}

	// Generate a presigned URL with a 1-hour expiration time
	$cmd = $client->getCommand('GetObject', [
		'Bucket' => $bucket,
		'Key' => $key,
	]);
	$request = $client->createPresignedRequest($cmd, '+1 hour');
	$presignedUrl = (string) $request->getUri();

	// Output the presigned URL
	echo $presignedUrl;

?>