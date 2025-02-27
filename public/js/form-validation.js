document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");

  if (form) {
      form.addEventListener("submit", function (event) {
          let isValid = true;

          document.querySelectorAll(".form-control").forEach(input => {
              const errorMessage = input.nextElementSibling;

              if (input.value.trim() === "") {
                  errorMessage.style.display = "block";
                  isValid = false;
              } else {
                  errorMessage.style.display = "none";
              }
          });

          if (!isValid) {
              event.preventDefault();
          }
      });
  }
});