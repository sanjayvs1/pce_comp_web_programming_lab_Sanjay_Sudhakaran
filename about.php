<?php 
session_start()
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About CircleFit</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="css/main.css">
    <style>
        #map {
            height: 400px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
        <h1>About CircleFit</h1>
            <h2>Our Mission</h2>
            <p>CircleFit is committed to promoting sustainable fashion practices through the resale of clothes. We aim
                to reduce textile waste and minimize the environmental impact of the fashion industry.</p>

            <h2>What is Circular Fashion?</h2>
            <p>Circular fashion is a concept that focuses on creating a closed-loop system for clothing production,
                consumption, and disposal. Instead of following the traditional linear model of 'take-make-dispose,'
                circular fashion aims to minimize waste and maximize the lifespan of garments by encouraging reuse,
                recycling, and upcycling.</p>

            <h2>Why Choose CircleFit?</h2>
            <p>At CircleFit, we provide a platform for individuals to buy and sell pre-owned clothing items. By
                extending the lifecycle of clothes, we contribute to the circular economy and help reduce the carbon
                footprint of the fashion industry.</p>

            <h2>Find Us</h2>
            <p>Visit our headquarters at the following address:</p>

            <div id="map"></div>

            <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
            <script>
                var map = L.map('map').setView([51.505, -0.09], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([18.99030241453974, 73.12760572779439]).addTo(map)
                    .bindPopup('CircleFit Headquarters')
                    .openPopup();
            </script>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>