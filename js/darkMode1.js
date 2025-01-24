document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("dark-switch");
  const themeStylesheet = document.getElementById("themeStylesheet");
  const bodyMode = document.getElementsByName("body");

  // Check local storage for the preferred theme
  const preferredTheme = localStorage.getItem("theme");
  if (preferredTheme) {
    themeStylesheet.setAttribute("href", `./style/${preferredTheme}-mode1.css`);
    // toggleButton.textContent =
    //   preferredTheme === "dark"
    //     ? "Switch to Light Mode"
    //     : "Switch to Dark Mode";
  }

  let mode = document.getElementById("mode-btn");
  let phone_bg = document.getElementById("phone-bg");

  toggleButton.addEventListener("click", () => {
    // Toggle the theme
    if (themeStylesheet.getAttribute("href") === "./style/light-mode1.css") {
      themeStylesheet.setAttribute("href", "./style/dark-mode1.css");
      mode.classList.remove("fa-moon");
      mode.classList.add("fa-sun");
      phone_bg.setAttribute("src", "./assets/images/download_bg_night.webp");
      localStorage.setItem("theme", "dark");
    } else {
      themeStylesheet.setAttribute("href", "./style/light-mode1.css");
      mode.classList.remove("fa-sun");
      mode.classList.add("fa-moon");
      phone_bg.setAttribute("src", "./assets/images/download_bg.webp");
      // toggleButton.textContent = '<i class="fa-solid fa-moon"></i>';
      localStorage.setItem("theme", "light");
    }
  });
});
