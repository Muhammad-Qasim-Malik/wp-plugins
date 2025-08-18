jQuery(function($){
  let units = 'metric';

  $(document).on('submit', '.mqwr-widget .searchRow', function(e){
    e.preventDefault();
    const city = $(this).find('.searchInput').val();
    fetchWeather(city);
  });

  $(document).on('click', '.mqwr-widget .toggle-units', function(){
    units = units === 'metric' ? 'imperial' : 'metric';
    $(this).text(units === 'metric' ? '°C' : '°F');
    const city = $('.mqwr-widget .searchInput').val();
    if(city) fetchWeather(city);
  });

  function fetchWeather(city){
    const $out = $('.mqwr-widget .mqwr-output');
    $out.html('<div class="card pulse">Loading weather…</div>');

    $.post(MQWeather.ajaxurl, {
      action: 'mqwr_get_weather',
      nonce: MQWeather.nonce,
      city: city,
      units: units
    }, function(resp){
      if(!resp.success){
        $out.html('<div class="alert alertError">'+resp.data+'</div>');
        return;
      }

      const d = resp.data;
      const icon = d.weather?.[0]?.icon;
      const desc = d.weather?.[0]?.description;
      const cityName = d.name + (d.sys?.country ? ', '+d.sys.country : '');
      const temp = Math.round(d.main.temp);
      const feels = Math.round(d.main.feels_like);
      const hum = d.main.humidity;
      const wind = Math.round(d.wind.speed);

      $out.html(`
        <div class="card">
          <div class="weatherRow">
            ${icon ? `<img src="https://openweathermap.org/img/wn/${icon}@2x.png" width="80" height="80" alt="${desc}"/>` : ''}
            <div style="flex:1">
              <h2 class="city">${cityName}</h2>
              <p class="desc">${desc}</p>
            </div>
            <div class="temp">${temp}<span class="unit">${units==='metric'?'°C':'°F'}</span></div>
          </div>
          <div class="stats">
            <div class="stat"><div class="statLabel">Feels like</div><div class="statValue">${feels}${units==='metric'?'°C':'°F'}</div></div>
            <div class="stat"><div class="statLabel">Humidity</div><div class="statValue">${hum}%</div></div>
            <div class="stat"><div class="statLabel">Wind</div><div class="statValue">${wind} ${units==='metric'?'m/s':'mph'}</div></div>
            <div class="stat"><div class="statLabel">Pressure</div><div class="statValue">${d.main.pressure} hPa</div></div>
          </div>
        </div>`);
    });
  }
});
