let weatherResult = document.getElementById("result");
let searchButton = document.getElementById("search-btn");
let cityInput = document.getElementById("city");

// Function to fetch weather details from the API and display them
let getWeatherDetails = async () => {
  let cityValue = cityInput.value;
  
  if (cityValue.length == 0) {
    // If the input field is empty, display a message
    weatherResult.innerHTML = `<h3 class="msg">Enter the name of the city</h3>`;
  } else {
    let url = `https://api.openweathermap.org/data/2.5/weather?q=${cityValue}&appid=bdd64c736f51fb2ffa936eaf1baa8bb3&units=metric`;

    // Clear the input field
    cityInput.value = "";

    try {
      const resp = await fetch(url);
      const data = await resp.json();
      console.log(data)
      
      // If the city name is valid, display the weather details
      weatherResult.innerHTML = `
      <div class="weather-dsc">
        <h2>${data.name}</h2>
        <h4 class="weather">${data.weather[0].main}</h4>
        <h4 class="desc">${data.weather[0].description}</h4>
        </div>
        <br>
        <div class="temp-container">
        <img src="./images/${data.weather[0].icon}.png">
        <h1>${Math.round(data.main.temp)}&#176C</h1>
        <div>
        <h4 class="title">min</h4>
        <h4 class="temp">${data.main.temp_min}&#176;</h4>
        </div>
          <div>
            <h4 class="title">max</h4>
            <h4 class="temp">${data.main.temp_max}&#176;</h4>
          </div>
        </div>
      `;
    } catch (error) {
      // If the city name is not valid, display a message
      weatherResult.innerHTML = `<h3 class="msg">City not found</h3>`;
    }
  }
};

// Add event listeners to the search button and window load event
searchButton.addEventListener("click", getWeatherDetails);
cityInput.addEventListener("keypress", (event) => {
  // Check if the Enter key was pressed
  if (event.key === "Enter") {
    getWeatherDetails();
  }
});
window.addEventListener("load", getWeatherDetails);

