<?php

// Shortcode
add_shortcode('mq_weather_report', 'mq_weather_report_shortcode');

function mq_weather_report_shortcode($atts) {
    ob_start(); ?>
    <div class="mqwr-widget">
        <div class="page">
            <div class="container">
                <header class="header">
                    <h1 class="title">MQ Weather Report</h1>
                    <p class="subtitle">Powered by OpenWeatherMap</p>
                </header>
                <form class="searchRow">
                    <input type="text" class="searchInput" placeholder="Search city (e.g., London, Tokyo)" />
                    <button type="submit" class="btn btnPrimary">Search</button>
                    <button type="button" class="btn btnSecondary toggle-units">°C</button>
                </form>
                <div class="mqwr-output"></div>
                <footer class="footer">Built with ❤️ by MQ Developers</footer>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}