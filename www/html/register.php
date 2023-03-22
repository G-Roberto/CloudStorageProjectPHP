<?php include "../inc/dbinfo.inc"; ?>
<?php
	// Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require '/usr/local/bin/vendor/autoload.php';

	// Try and connect using the info above.
	$con = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
	if (mysqli_connect_errno()) {
		// If there is an error with the connection, stop the script and display the error.
		exit('Failed to connect to MySQL: ' . mysqli_connect_error());
	}

	// Now we check if the data was submitted, isset() function will check if the data exists.
	if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
		// Could not get the data that should have been sent.
		exit('Please complete the registration form!');
	}
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
		// One or more values are empty.
		exit('Please complete the registration form');
	}

	// We need to check if the account with that username exists.
	if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
		// Validating form data
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			exit('Email is not valid!');
		}
		if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
			exit('Username is not valid!');
		}
		if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
			exit('Password must be between 5 and 20 characters long!');
		}
		// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		$stmt->store_result();
		// Store the result so we can check if the account exists in the database.
		if ($stmt->num_rows > 0) {
			// Username already exists
			$_SESSION['errorname'] = 'Username exists, please choose another!';
			header('Location: error.php');
		} else {
			// Username doesn't exist, insert new account
			if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, activation_code) VALUES (?, ?, ?, ?)')) {
				
				// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$uniqid = uniqid();
				$stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $uniqid);
				$stmt->execute();
				
				
				$activate_link = 'http://d3iq8lftgsccg6.cloudfront.net/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
				$sender = 'nuvolacloudstorage@gmail.com';
				$senderName = 'No Reply';
				$recipient = $_POST['email'];
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
					
				$mail = new PHPMailer(true);
					
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
					echo "Email not sent! {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
				}				
				
				$_SESSION['username'] = $_POST['username'];
				header('Location: success.html');
			} else {
				// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
				$con->close();
				$_SESSION['errorname'] = 'Error: could not prepare statement.';
				header('Location: error.php');
			}
		}
		$stmt->close();
	} else {
		// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
		$con->close();
		$_SESSION['errorname'] = 'Error: could not prepare statement.';
		header('Location: error.php');
	}
	$con->close();
	
?>