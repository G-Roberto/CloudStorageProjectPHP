<input type="file" id="fileInput" />
<button onclick="uploadFile()">Upload file</button>

<script src="https://sdk.amazonaws.com/js/aws-sdk-2.100.0.min.js"></script>
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
            } else {
              console.error(`Error uploading chunk: ${xhr.responseText}`);
            }
          }
        };

        const payload = JSON.stringify({
          fileName: file.name,
          fileData: Array.from(chunkData),
        });

        xhr.send(payload);
      };

      reader.readAsArrayBuffer(chunk);

      start = end;
      end = start + chunkSize;
    }
  }
</script>