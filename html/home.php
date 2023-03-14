<?php include "../inc/dbinfo.inc"; ?>
<?php
	$_SESSION['name'] = "test";
	$currusername = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="Roberto Gentilini" />
        <title>Nuvola Cloud Storage</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons -->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS -->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body onload="javascript:show_contents();">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.html">Università di Pavia</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto py-4 py-lg-0">
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="home.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="logout.php">Logout</a></li>
                        <li class="nav-item"><a class="nav-link px-lg-3 py-3 py-lg-4" href="profile.php"><?php echo $_SESSION['name'];?></a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Page Header-->
        <header class="masthead" style="background-image: url('assets/img/home-bg.jpg')">
            <div class="container position-relative px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-7">
                        <div class="site-heading">
                            <h1>Nuvola</br>Cloud Storage</h1>
                            <span class="subheading"></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content-->
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-7">
                    <hr class="my-4" />
                    <div class="post-preview">
						<h2 class="post-title">Contents</h2>
						<br><p id="msgpar"></p>	
                    </div>
                    <!-- Divider-->
                    <hr class="my-4" />
                    <!-- Upload-->
                    <div class="post-preview" id="app">
						<div>
							<a><h2>Upload file</h2></a>
							<input type="file" class="btn btn-primary text-uppercase" id="fileInput" />
						</div>
						<div>
							<button onclick="uploadFile()" class="btn btn-primary text-uppercase">Upload file</button>
						</div>
                    </div>
                    <!-- Divider-->
                    <hr class="my-4" />
                </div>
            </div>
        </div>
        <!-- Footer-->
        <footer class="border-top">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-7">
                        <ul class="list-inline text-center">
                            <li class="list-inline-item">
                                <a href="#!">
                                    <span class="fa-stack fa-lg">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#!">
                                    <span class="fa-stack fa-lg">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#!">
                                    <span class="fa-stack fa-lg">
                                        <i class="fas fa-circle fa-stack-2x"></i>
                                        <i class="fab fa-github fa-stack-1x fa-inverse"></i>
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="small text-center text-muted fst-italic">Copyright &copy; Nuvola Cloud Storage 2023</div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
		
		<script>
			function uploadFile() {
				const file = document.getElementById('fileInput').files[0];
				const chunkSize = 1024 * 1024; // 1 MB chunks
				let start = 0;
				let end = chunkSize;

				while (start < file.size) {
					const chunk = file.slice(start, end);
					const reader = new FileReader();

					reader.onload = function(event) {
						const chunkData = new Uint8Array(event.target.result);
						const xhr = new XMLHttpRequest();
						xhr.open('POST', 'https://n6pjsuhak0.execute-api.eu-central-1.amazonaws.com/default/upload-item', true);
						xhr.setRequestHeader('Content-Type', 'application/json');

						xhr.onreadystatechange = function() {
							if (xhr.readyState === 4) {
								if (xhr.status === 200) {
									console.log(`Chunk uploaded successfully: ${xhr.responseText}`);
									window.location.replace("home.php");
								} else {
									console.error(`Error uploading chunk: ${xhr.responseText}`);
								}
							}
						};

						filePath = "<?php echo $currusername; ?>/" + file.name;
						const payload = JSON.stringify({
							fileName: filePath,
							fileData: Array.from(chunkData),
						});

						xhr.send(payload);
					};

					reader.readAsArrayBuffer(chunk);

					start = end;
					end = start + chunkSize;
				}
			}
		  
			

			function show_contents() {

				var requestOptions = {
				  method: 'GET',
				  redirect: 'follow'
				};

				name = "<?php echo $currusername ?>";

				fetch("https://j08lhrjnlk.execute-api.eu-central-1.amazonaws.com/default/get-items-in-bucket?searchedname=" + name, requestOptions)
				  .then(response => response.text())
				  
				  .then(result => {
					console.log(result);
					var files = result.toString().split('"filename":"');
					for (let i = files.length - 1; i >= 1; i--) {
						filename = files[i].split('","')[0];
						document.getElementById("msgpar").innerHTML = document.getElementById("msgpar").innerHTML + '<br><h4><a href="accessimg.php?' + name + "/" + filename + '">' + filename + '</a></h4> <input type="submit" value="Delete" id="delbtn" onclick="delete_file(' + "'" + name + "', '" + filename + "'" + ')"></br></br>';
					}
				  })
				  
				  .catch(error => console.log('error', error));

			}
			
			
			
			function delete_file(name, filename) {
						
				var requestOptions = {
					method: 'DELETE',
					redirect: 'follow'
				};

				fetch("https://47ttwbrs8f.execute-api.eu-central-1.amazonaws.com/default/deleteitem?itemKey=" + name + "/" + filename, requestOptions)
				  .then(response => response.text())
				  .then(result => console.log(result))
				  .catch(error => console.log('error', error));
				
				window.location.replace("home.php");
			}
		</script>		
    </body>
</html>

