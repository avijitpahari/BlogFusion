const roleTextDisplay = document.getElementById("role-text");
const btnUser = document.getElementById("btn-user");
const btnAuthor = document.getElementById("btn-author");
const highlight = document.getElementById("role-highlight");
const roleInput = document.getElementById("role-input");

function setRole(role) {
  if (role === "user") {
    roleTextDisplay.textContent = "User";
    roleInput.value = "user";
    highlight.style.transform = "translateX(0)";
    btnUser.classList.add("text-primary");
    btnUser.classList.remove("text-gray-500");
    btnAuthor.classList.add("text-gray-500");
    btnAuthor.classList.remove("text-primary");
  } else {
    roleTextDisplay.textContent = "Author";
    roleInput.value = "author";
    highlight.style.transform = "translateX(100%)";
    btnAuthor.classList.add("text-primary");
    btnAuthor.classList.remove("text-gray-500");
    btnUser.classList.add("text-gray-500");
    btnUser.classList.remove("text-primary");
  }
}

btnUser.addEventListener("click", () => setRole("user"));
btnAuthor.addEventListener("click", () => setRole("author"));

// Handle file upload preview or name display
const fileInput = document.getElementById("file-upload");
if (fileInput) {
  fileInput.addEventListener("change", function () {
    if (this.files && this.files[0]) {
      console.log("File selected:", this.files[0].name);
    }
  });
}

function togglePasswordVisibility(inputId, button) {
  const input = document.getElementById(inputId);
  const icon = button.querySelector(".material-symbols-outlined");
  if (input.type === "password") {
    input.type = "text";
    icon.textContent = "visibility_off";
  } else {
    input.type = "password";
    icon.textContent = "visibility";
  }
}

function handleImagePreview(event) {
  const input = event.target;
  const placeholder = document.getElementById("upload-placeholder");
  const previewContainer = document.getElementById("preview-container");
  const previewImg = document.getElementById("image-preview");

  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
      placeholder.classList.add("hidden");
      previewContainer.classList.remove("hidden");
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function removeImage() {
  const input = document.getElementById("file-upload");
  const placeholder = document.getElementById("upload-placeholder");
  const previewContainer = document.getElementById("preview-container");
  const previewImg = document.getElementById("image-preview");

  input.value = "";
  previewImg.src = "";
  previewContainer.classList.add("hidden");
  placeholder.classList.remove("hidden");
}
let interval;

function startTimer(seconds) {
  clearInterval(interval);

  let timer = document.getElementById("timer");

  interval = setInterval(() => {
    let min = Math.floor(seconds / 60);
    let sec = seconds % 60;

    timer.innerText =
      String(min).padStart(2, "0") + ":" + String(sec).padStart(2, "0");

    seconds--;

    if (seconds < 0) {
      clearInterval(interval);
      timer.innerText = "Expired";
    }
  }, 1000);
}

startTimer(600);
