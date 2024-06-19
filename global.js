const nav = document.getElementById("nav");

fetch("nav.html")
  .then((res) => res.text()) // Invoke text() to get the text content
  .then((data) => {
    nav.innerHTML = data;
  })
  .catch((error) => {
    console.error("Error fetching nav bar content:", error);
  });

const footer = document.getElementById("footer");
fetch("footer.html")
  .then((res) => res.text()) // Invoke text() to get the text content
  .then((data) => {
    footer.innerHTML = data;
  })
  .catch((error) => {
    console.error("Error fetching footer content:", error);
  });

// Loader
function showLoader() {
  const loader = document.getElementById("loader");
  loader.style.display = "flex";
  document.body.style.overflow = "hidden";
}
//showLoader();
function hideLoader() {
  const loader = document.getElementById("loader");
  loader.style.display = "none";
  document.body.style.overflowY = "auto";
}

function delay(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function showAndLoad(event, element) {
  event.preventDefault();
  showLoader();
  // await delay(200);
  window.location.href = element.href;
}

async function showAndLoadForm(event, element) {
  event.preventDefault();
  showLoader();
  await delay(1200); // Assuming you have a delay function defined elsewhere
  element.submit();
}
