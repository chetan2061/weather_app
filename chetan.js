// Define API Key and Base URL
const apiKey = "13595f2a401633aa3a9a3ab66b55bb8d"; // API key 
const apiBaseUrl = "https://api.openweathermap.org/data/2.5/"; // Base URL for weather data

// Fetch Weather Data for a City
async function fetchWeatherData(cityName) {

    var data;
    if (navigator.onLine) {
      const response = await fetch(`http://chetan-weatherapp.kesug.com/chetan/chetan.php?q=${cityName}`);
      data = await response.json();
      
      localStorage.setItem(cityName, JSON.stringify(data));
  }
     else {
        // Offline: Retrieve data from localStorage
        data = JSON.parse(localStorage.getItem(cityName));
    }
    console.log(data)
   
    
    displayWeather(data); // Display the weather data
  } 


function displayWeather(data) {
  if (Array.isArray(data) && data.length > 0) {
    const weather = data[0]; // Access the first object in the array which is weather report

// Update city name and weather details in the HTML
    document.getElementById("city").innerHTML = `Weather in ${weather.city}`;
    document.getElementById("temp").innerHTML = `${weather.temp}°C`;
    document.getElementById("humidity").innerHTML = `Humidity: ${weather.humidity}%`;
    document.getElementById("wind-speed").innerHTML = `Wind Speed: ${weather.wind} m/s`;
    document.getElementById("pressure").innerHTML = `Pressure: ${weather.pressure} hPa`;
  } else {
    // Handle case where data is invalid or empty
    alert("No weather data found for the specified city.");
    console.error("Invalid or empty data:", data);
  }
}


// Add Event Listener for Search Button
document.getElementById("searchButton").addEventListener("click", () => {
  const city = document.getElementById("cityInput").value.trim(); // Get user input
  if (city) {
    fetchWeatherData(city); // Fetch weather data for the entered city
    document.getElementById("cityInput").value = ""; // Clear the input field
  } else {
    alert("Please enter a valid city name."); // Alert if input is empty
  }
});

// Fetch default city weather data on page load
fetchWeatherData("Bharatpur");