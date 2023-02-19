<?php include "../inc/dbinfo.inc"; ?>
<?php

	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require '/usr/local/bin/vendor/autoload.php';
	
	$sender = 'nuvolacloudstorage@gmail.com';
	$senderName = 'No Reply';
	$recipient = 'roby.gent@gmail.com';
	$usernameSmtp = 'AKIA5MFAAP5YNACHDRDI';
	$passwordSmtp = 'BFY2mgH6631k5IXd+fTdzttK9lNsofJ5O0KgyM+s595n';
	$configurationSet = '';
	$host = 'email-smtp.eu-central-1.amazonaws.com';
	$port = 587;
	$subject = 'User code';
	$bodyText =  "Nuvola Cloud Storage\r\nPlease click the following link to activate your account: " . $activate_link;
	$bodyHtml = '<h1>Nuvola Cloud Storage</h1>
		<p>Please click the following link to activate your account: 
		<a href="' . $activate_link . '">' . $activate_link . '</a></p>';
		
	try {
		// Specify the SMTP settings.
		$mail->isSMTP();
		$mail->setFrom($sender, $senderName);
		$mail->Username   = $usernameSmtp;
		$mail->Password   = $passwordSmtp;
		$mail->Host       = $host;
		$mail->Port       = $port;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'tls';
		$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

		// Specify the message recipients.
		$mail->addAddress($recipient);

		// Specify the content of the message.
		$mail->isHTML(true);
		$mail->Subject    = $subject;
		$mail->Body       = $bodyHtml;
		$mail->AltBody    = $bodyText;
		$mail->Send();
		echo "Email sent!" , PHP_EOL;
	} catch (phpmailerException $e) {
		echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
	} catch (Exception $e) {
		echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
	}
?>