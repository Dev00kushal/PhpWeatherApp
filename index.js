// Get references to HTML elements
let cityInput = document.getElementById("city");
let searchButton = document.getElementById("search-btn");
let weatherResult = document.getElementById("result");
let weekContainer = document.querySelector(".week-container");

// Function to save weather data to local storage
let saveWeatherDataToLocalStorage = (cityName, data) => {
  const weatherData = JSON.stringify({
    cityName: cityName,
    data: data
  });

  localStorage.setItem("weatherData", weatherData);
};

// Function to load weather data from local storage
let loadWeatherDataFromLocalStorage = () => {
  const weatherData = localStorage.getItem("weatherData");
  if (weatherData) {
    const parsedData = JSON.parse(weatherData);
    const cityName = parsedData.cityName;
    const data = parsedData.data;
    renderWeatherData(cityName, data);
    allOftheWeatherData(cityName);
  }
};

// Function to render weather data to HTML elements
let renderWeatherData = (cityName, data) => {
  weatherResult.innerHTML = `
    <div class="weather-dsc">
      <h2>${cityName}</h2>
      <h4 class="weather">${data.weather[0].main}</h4>
      <h4 class="desc">${data.weather[0].description}</h4>
    </div>
    <br>
    <div class="temp-container">
      <img src="./images/${data.weather[0].icon}.png">
      <h1>${Math.round(data.main.temp)}&#176C</h1>
      <div>
        <h4 class="title">min</h4>
        <h4 class="temp">${data.main.temp_min}&#176</h4>
      </div>
      <div>
        <h4 class="title">max</h4>
        <h4 class="temp">${data.main.temp_max}&#176</h4>
      </div>
    </div>
  `;
};

// Function to get weather details from API
let getWeatherDetails = async () => {
  let cityValue = cityInput.value;

  if (cityValue.length === 0) {
    weatherResult.innerHTML = `<h3 class="msg">Enter the name of the city</h3>`;
  } else {
    let url = `https://api.openweathermap.org/data/2.5/weather?q=${cityValue}&appid=bdd64c736f51fb2ffa936eaf1baa8bb3&units=metric`;

    cityInput.value = ""; 

    try {
      const resp = await fetch(url);
      const data = await resp.json();

      renderWeatherData(data.name, data);
      saveWeatherDataToLocalStorage(data.name, data);
      allOftheWeatherData(data.name);
    } catch (error) {
      weatherResult.innerHTML = `<h3 class="msg">City not found</h3>`;
    }
  }
};

// Function to render historical weather data
let renderHistoricalWeatherData = (data) => {
  let weekBoxHTML = "";
  data.forEach((weather) => {
    weekBoxHTML += `
    <div class="week-box">
      <div class="week-content">
        <p>${weather.Week_Day}</p>
        <figure><img src="./images/${weather.Weather_Icon}.png" alt="weather-icon" /></figure>
        <p>${weather.Temperature} &deg C</p>
        <p>${weather.Pressure} Pa</p>
        <p>${weather.Wind_Speed} m/s</p>
        <p>${weather.Humidity} %</p>
      </div>
    </div>`
  });

  weekContainer.innerHTML = weekBoxHTML;
};

let allOftheWeatherData = async () => {
  try {
    let url = `http://localhost/Weather-App-master/nextIndex.php`;
    let response = await fetch(url);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    } else {
      let data = await response.json();
      renderHistoricalWeatherData(data);
    }
  } catch (error) {
    console.error('Error fetching historical weather data:', error);
    console.log('Response status:', response.status); // Log HTTP response status
    console.log('Response text:', await response.text()); // Log the response text for debugging
  }
};


// Add event listeners
searchButton.addEventListener("click", getWeatherDetails);
cityInput.addEventListener("keypress", (event) => {
  if (event.key === "Enter") {
    getWeatherDetails();
  }
});

// Load weather data from local storage and historical data on page load
window.addEventListener("load", () => {
  loadWeatherDataFromLocalStorage();
  allOftheWeatherData(); // Load historical weather data
  getWeatherDetails();
});
