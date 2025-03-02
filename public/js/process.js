function disableButton() {
  let btn = document.getElementById("submit-btn");
  let btnText = document.getElementById("btn-text");
  let spinner = document.getElementById("loading-spinner");

  btn.disabled = true;
  btnText.textContent = "Processando...";
  spinner.classList.remove("d-none"); 
}