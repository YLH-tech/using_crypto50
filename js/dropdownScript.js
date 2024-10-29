/* When the user clicks on the button, 
        toggle between hiding and showing the dropdown content */
function clickdown() {
  document.getElementById("myDropdown").classList.toggle("show");
  if(document.getElementById("resp-myDropdown").classList.contains("show")){
    document.getElementById("resp-myDropdown").classList.remove("show");
  }
}

function resp_clickdown(){
  document.getElementById("resp-myDropdown").classList.toggle("show");
  if(document.getElementById("myDropdown").classList.contains("show")){
    document.getElementById("myDropdown").classList.remove("show");
  }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
  if (!event.target.matches(".clickbtn")) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains("show")) {
        openDropdown.classList.remove("show");
      }
    }
  }
};
