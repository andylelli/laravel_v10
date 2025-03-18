<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ $eventimage }}">
    <title>Permission Request</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Dark grayscale theme */
        body {
            background: #121212;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: filter 0.3s ease;
            position: relative;
        }

        /* Background blur effect */
        .background-blur {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(6px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        /* Modal styling */
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(to bottom, #222, #1a1a1a);
            padding: 25px;
            width: 90%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            z-index: 1000;
            text-align: center;
            color: #f0f0f0;
            border: 1px solid #444;
        }

        /* Image styling (75px x 75px square with orange background, centered) */
        .modal img {
            width: 75px;
            height: 75px;
            background-color: orange;
            border-radius: 8px; /* Rounded corners */
            margin-bottom: 15px; /* Space between image and content */
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .modal h2 {
            margin-bottom: 15px;
            color: #ffffff;
            font-size: 20px;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
        }

        .modal p {
            font-size: 14px;
            margin-bottom: 15px;
            text-align: left;
            color: #ccc;
        }

        .modal ul {
            text-align: left;
            margin-bottom: 15px;
            padding-left: 20px;
        }

        .modal li {
            font-size: 14px;
            color: #bbb;
            margin-bottom: 5px;
        }

        /* Styled buttons */
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 15px;
        }

        .install-btn {
            padding: 12px 18px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.2s;
            font-weight: bold;
            background: #666;
            color: #fff;
            border: 1px solid #888;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.1);
            text-decoration: none; /* Remove underline */
        }

        .install-btn:hover {
            background: #777;
        }

        .install-btn:active,
        .install-btn.pressed {
            background: #555; /* Darker when pressed */
        }

        /* Back button aligned to the left */
        .back-btn {
            background: transparent;
            color: #bbb;
            border: none;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 5px;
            font-size: 14px;
            margin-top: 10px;
            cursor: pointer;
            width: 100%;
        }

        .back-btn:hover {
            color: #ddd;
        }

        .back-btn::before {
            content: "←";
            font-size: 16px;
        }

        /* Centering the image */
        .image-container {
            width: 100px;  /* Size of the square */
            height: 100px; /* Size of the square */
            overflow: hidden; /* Hide parts of the image outside the square */
            position: relative;
            margin: 20px auto;
        }

        .image-container img {
            width: 100%; /* Make the image fill the container */
            height: 100%;
            object-fit: cover; /* Crop and center the image */
            object-position: center; /* Ensure the central part of the image is shown */
        }
    </style>
</head>
<body>

    <div class="background-blur"></div>

    <div class="modal image-container" id="permissionModal">
        <!-- Orange square image (75x75px, centered) -->
        <!-- Base64 image added here (50x50px) -->
        <img width="75" height="75" src="{{ $eventimage }}" alt="App Icon"/>

        <h2>{{ $eventname }} Needs Your Permission</h2>
        <p>{{ $eventname }} is a **Progressive Web App (PWA)**—a web-based app that works like a regular app but installs directly from your browser. It saves data on your device so you can use the app offline and load it faster.</p>
        
        <p><strong>What is stored on your device?</strong></p>
        <ul>
            <li><strong>App Files</strong> – The app itself is stored for quick access.</li>
            <li><strong>Cached Data</strong> – Loads content faster and works offline.</li>
            <li><strong>Local Storage & IndexedDB</strong> – Saves settings and app data.</li>
        </ul>

        <p><strong>Optional Access:</strong></p>
        <ul>
            <li><strong>Location</strong> – For location-based features.</li>
            <li><strong>Device Motion</strong> – For navigation or motion-based controls.</li>
        </ul>

        <p>For the best experience, once installed, add the app to your home screen. You can uninstall it anytime from the app’s settings or by clearing your browser data.</p>
        
        <div class="button-container">
            <a href="{{ $url }}" class="install-btn" id="installBtn">Install</a>
            <button class="back-btn" onclick="goBack()">Back</button>
        </div>
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            }
        }

        // Handle button press effect
        const installBtn = document.getElementById("installBtn");

        installBtn.addEventListener("mousedown", () => {
            installBtn.classList.add("pressed");
        });

        installBtn.addEventListener("mouseup", () => {
            installBtn.classList.remove("pressed");
        });

        installBtn.addEventListener("touchstart", () => {
            installBtn.classList.add("pressed");
        });

        installBtn.addEventListener("touchend", () => {
            installBtn.classList.remove("pressed");
        });
    </script>

</body>
</html>
