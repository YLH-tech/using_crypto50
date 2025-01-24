document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("dark-switch");
  const themeStyleSheet = document.getElementById("themeStyleSheet");
  const bodyMode = document.getElementsByName("body");

  // Check local storage for the preferred theme
  const preferredTheme = localStorage.getItem("theme");
  if (preferredTheme) {
    themeStyleSheet.setAttribute("href", `./style/${preferredTheme}-mode2.css`);
    // toggleButton.textContent =
    //   preferredTheme === "dark"
    //     ? "Switch to Light Mode"
    //     : "Switch to Dark Mode";
  }

  let mode = document.getElementById("mode-btn");
  let phone_bg = document.getElementById("phone-bg");

  toggleButton.addEventListener("click", () => {
    // Toggle the theme
    if (themeStyleSheet.getAttribute("href") === "./style/light-mode2.css") {
      themeStyleSheet.setAttribute("href", "./style/dark-mode2.css");
      mode.classList.remove("fa-moon");
      mode.classList.add("fa-sun");
      phone_bg.setAttribute("src", "./assets/images/download_bg_night.webp");
      localStorage.setItem("theme", "dark");
    } else {
      themeStyleSheet.setAttribute("href", "./style/light-mode2.css");
      mode.classList.remove("fa-sun");
      mode.classList.add("fa-moon");
      phone_bg.setAttribute("src", "./assets/images/download_bg.webp");
      // toggleButton.textContent = '<i class="fa-solid fa-moon"></i>';
      localStorage.setItem("theme", "light");
    }
  });
});
