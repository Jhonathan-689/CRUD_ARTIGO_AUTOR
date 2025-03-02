function disableButton() {
  let btn = document.getElementById("submit-btn");
  let btnText = document.getElementById("btn-text");
  let spinner = document.getElementById("loading-spinner");

  btn.disabled = true; // Desativa o botão
  btnText.textContent = "Processando..."; // Altera o texto do botão
  spinner.classList.remove("d-none"); // Exibe o spinner
}