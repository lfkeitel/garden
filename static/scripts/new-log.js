(() => {
  // The width and height of the captured photo. We will set the
  // width to the value defined here, but the height will be
  // calculated based on the aspect ratio of the input stream.

  const width = 720; // We will scale the photo width to this
  let height = 0; // This will be computed based on the input stream

  // |streaming| indicates whether or not we're currently streaming
  // video from the camera. Obviously, we start at false.

  let streaming = false;

  // The various HTML elements we need to configure or control. These
  // will be set by the startup() function.

  let video = null;
  let canvas = null;
  let photo = null;
  let take_photo_btn = null;
  let start_camera_btn = null;
  let stop_camera_btn = null;
  let upload_cam_btn = null;
  let upload_img_btn = null;
  let image_files = null;
  let garden_image = null;

  function startcam() {
    navigator.mediaDevices
      .getUserMedia({
        video: {
          facingMode: 'environment',
        },
        audio: false
      })
      .then((stream) => {
        video.srcObject = stream;
        video.play();
      })
      .catch((err) => {
        console.error(`An error occurred: ${err}`);
      });
  }

  function stopcam() {
    video.srcObject.getVideoTracks().forEach(track => track.stop());
    streaming = false;
  }

  function uploadImage() {
    document.getElementById("upload_msg").innerHTML = "Uploading image...";
    const data = photo.getAttribute("src");
    const formData = new URLSearchParams();
    formData.set('image', data);

    fetch("/upload", {
      method: "POST",
      body: formData,
    })
      // .then((response) => response.text())
      // .then((t) => alert(t))
      .then((response) => response.json())
      .then((json) => {
        if (json.error !== '') {
          document.getElementById("upload_msg").innerHTML = `ERROR: ${json.error}`;
          return;
        }

        const filename = json.filename;

        let current_files = [];
        const image_files_val = image_files.value;

        if (image_files_val !== '') {
          current_files = image_files_val.split(";");
        }

        console.log(current_files);
        current_files.push(filename);
        image_files.value = current_files.join(";");

        document.getElementById("upload_msg").innerHTML = `Image #${current_files.length} uploaded.`;
      })
      .catch((e) => {
        alert(e);
      });
  }

  function uploadFileImage() {
    document.getElementById("upload_msg").innerHTML = "Uploading image...";
    const data = garden_image.files[0];

    const formData = new FormData();
    formData.set('image_file', data);

    fetch("/upload", {
      method: "POST",
      body: formData,
    })
      // .then((response) => response.text())
      // .then((t) => console.log(t))
      .then((response) => response.json())
      .then((json) => {
        if (json.error !== '') {
          document.getElementById("upload_msg").innerHTML = `ERROR: ${json.error}`;
          return;
        }

        const filename = json.filename;

        let current_files = [];
        const image_files_val = image_files.value;

        if (image_files_val !== '') {
          current_files = image_files_val.split(";");
        }

        current_files.push(filename);
        image_files.value = current_files.join(";");

        document.getElementById("upload_msg").innerHTML = `Image #${current_files.length} uploaded.`;
      })
      .catch((e) => {
        alert(e);
      });
  }

  function startup() {
    video = document.getElementById("video");
    canvas = document.getElementById("canvas");
    photo = document.getElementById("photo");
    take_photo_btn = document.getElementById("take-photo-btn");
    start_camera_btn = document.getElementById("start-video-btn");
    stop_camera_btn = document.getElementById("stop-video-btn");
    upload_cam_btn = document.getElementById("upload-cam-btn");
    upload_img_btn = document.getElementById("upload-img-btn");
    image_files = document.getElementById("image_files");
    garden_image = document.getElementById("garden_image");

    video.addEventListener(
      "canplay",
      (ev) => {
        if (!streaming) {
          height = video.videoHeight / (video.videoWidth / width);

          // Firefox currently has a bug where the height can't be read from
          // the video, so we will make assumptions if this happens.

          if (isNaN(height)) {
            height = width / (4 / 3);
          }

          video.setAttribute("width", width);
          video.setAttribute("height", height);
          canvas.setAttribute("width", width);
          canvas.setAttribute("height", height);
          streaming = true;

          const context = canvas.getContext("2d");
          context.fillStyle = "#AAA";
          context.fillRect(0, 0, canvas.width, canvas.height);
          context.fillStyle = "#000";
          context.font = "20px serif";
          context.fillText("Take a pic", 10, 50);

          const data = canvas.toDataURL("image/png");
          photo.setAttribute("src", data);
        }
      },
      false,
    );

    take_photo_btn.addEventListener(
      "click",
      (ev) => {
        takepicture();
        ev.preventDefault();
      },
      false,
    );

    start_camera_btn.addEventListener(
      "click",
      (ev) => {
        startcam();
        ev.preventDefault();
      },
      false,
    );

    stop_camera_btn.addEventListener(
      "click",
      (ev) => {
        stopcam();
        ev.preventDefault();
      },
      false,
    );

    upload_cam_btn.addEventListener(
      "click",
      (ev) => {
        uploadImage();
        ev.preventDefault();
      },
      false,
    );

    upload_img_btn.addEventListener(
      "click",
      (ev) => {
        uploadFileImage();
        ev.preventDefault();
      },
      false,
    );

    clearphoto();
  }

  // Fill the photo with an indication that none has been
  // captured.

  function clearphoto() {
    const context = canvas.getContext("2d");
    context.fillStyle = "#AAA";
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.fillStyle = "#000";
    context.font = "20px serif";
    context.fillText("Start cam then take a pic", 10, 50);

    const data = canvas.toDataURL("image/png");
    photo.setAttribute("src", data);
  }

  // Capture a photo by fetching the current contents of the video
  // and drawing it into a canvas, then converting that to a PNG
  // format data URL. By drawing it on an offscreen canvas and then
  // drawing that to the screen, we can change its size and/or apply
  // other changes before drawing it.

  function takepicture() {
    const context = canvas.getContext("2d");
    if (width && height) {
      canvas.width = width;
      canvas.height = height;
      context.drawImage(video, 0, 0, width, height);

      const data = canvas.toDataURL("image/png");
      photo.setAttribute("src", data);
    } else {
      clearphoto();
    }
  }

  // Set up our event listener to run the startup process
  // once loading is complete.
  window.addEventListener("load", startup, false);
})();
