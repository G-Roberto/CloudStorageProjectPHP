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
			echo 'Username exists, please choose another!';
		} else {
			// Username doesn't exist, insert new account
			if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, activation_code) VALUES (?, ?, ?, ?)')) {
				
				// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$uniqid = uniqid();
				$stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $uniqid);
				$stmt->execute();
				$from    = 'noreply@yourdomain.com';
				$subject = 'Account Activation Required';
				$headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
				// Update the activation variable below
				$activate_link = 'http://yourdomain.com/phplogin/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
				
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
					// You can also add CC, BCC, and additional To recipients here.

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
				echo 'Please check your email to activate your account!';
			} else {
				// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
				echo 'Could not prepare statement!';
			}
		}
		$stmt->close();
	} else {
		// Something is wrong with the SQL statement, so you must check to make sure your accounts table exists with all 3 fields.
		echo 'Could not prepare statement!';
	}
	$con->close();
?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Register</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
		<link href="css/authentication.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="register">
			<h1>Register</h1>
			<form action="register.php" method="post" autocomplete="off">
				<label for="username">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="username" placeholder="Username" id="username" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Password" id="password" required>
				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" placeholder="Email" id="email" required>
				<input type="submit" value="Register">
			</form>
		</div>
	</body>
</html>