const MAX_IMAGE_SIZE = 100000000
const API_ENDPOINT = 'https://ciaxap8z77.execute-api.eu-central-1.amazonaws.com/uploads?filename=';
const WEBSITE = 'https://sam-app-s3uploadbucket-1ut0y5lkfg694.s3.eu-central-1.amazonaws.com/';

new Vue({
	el: "#app",
	data: {
		image: '',
		uploadURL: '',
		filename: ''
	},
	methods: {
		onFileChange (e) {
			let files = e.target.files || e.dataTransfer.files
			//if (!files.length) return
			this.createImage(files[0])
		},
		createImage (file) {
			let reader = new FileReader()
			reader.onload = (e) => {
				console.log('length: ')
				// Check if the uploaded file is too big
				if (e.target.result.length > MAX_IMAGE_SIZE) {
					return alert('File is loo large.')
				}
				// Check if the uploaded file is an image
				let extensionarray = ("gif jpg jpeg png");
				let nameextension = file.name.split('.').pop();
				if (extensionarray.includes(nameextension) == false) {
					return
				}
				this.image = e.target.result
				this.filename = file.name
			}
			reader.readAsDataURL(file)
		},
		removeImage: function (e) {
			console.log('Remove clicked')
			this.image = ''
			this.filename = ''
		},
		uploadImage: async function (e) {
			console.log('Upload clicked')
			// Get the pre-signed URL
			const response = await axios({
				method: 'GET',
				url: API_ENDPOINT + "<?php echo $_SESSION['name'];?>/" + this.filename
			})
			console.log('Response: ', response)
			console.log('Uploading: ', this.image)
			let binary = atob(this.image.split(',')[1])
			let array = []
			for (var i = 0; i < binary.length; i++) {
				array.push(binary.charCodeAt(i))
			}
			let blobData = new Blob([new Uint8Array(array)])
			console.log('Uploading to: ', response.uploadURL)
			const result = await fetch(response.uploadURL, {
				method: 'PUT',
				body: blobData
			})
			console.log('Result: ', result)
			// Final URL for the user (doesn't need the query string parameters)
			this.uploadURL = response.uploadURL.split('?')[0]
			window.location.replace("home.php");
  }
}
})



const new_website = 'https://ui03hlfiv0.execute-api.eu-central-1.amazonaws.com/test/showitemsresource'
	  
function show_contents() {

	// Send the GET request
	var requestOptions = {
	  method: 'GET',
	  redirect: 'follow'
	};

	// Take the values returned from the lambda
	// function (i.e. the list of files) and print
	// them on the web page in a readable format
	fetch(new_website, requestOptions)
	  .then(response => response.text())
	  .then(result => {
		var files = result.toString().split('\\\",\\\"');
		const lastval = files.length - 1;
		files[lastval] = files[lastval].split('\\\"]\"')[0];
		for (let i = lastval; i >= 1; i--) {
			var lmnt = files[i].split('/');
			if (lmnt[0] == "<?php echo $_SESSION['name'];?>") {
				document.getElementById("msgpar").innerHTML = document.getElementById("msgpar").innerHTML + '<img src="' + WEBSITE + files[i] + '" width="300"/>'
				document.getElementById("msgpar").innerHTML = document.getElementById("msgpar").innerHTML + '<br><h3><a href="' + WEBSITE + files[i] + '">' + lmnt[1] + '</a> </h3>' + '<input type="submit" value="Delete" id="delbtn" onclick="delete_file(' + "'" + files[i] + "'" + ')"></br></br>';
			}
		}				
	  })
	  .catch(error => console.log('error', error));
	
	btn = document.getElementById('btn');
	btn.style.display = 'none';

}


function delete_file(filename) {
	var requestOptions = {
		method: 'DELETE',
		redirect: 'follow'
	};

	fetch("https://47ttwbrs8f.execute-api.eu-central-1.amazonaws.com/default/deleteitem?itemKey=" + filename, requestOptions)
	  .then(response => response.text())
	  .then(result => console.log(result))
	  .catch(error => console.log('error', error));
	
	window.location.replace("home.php");
}