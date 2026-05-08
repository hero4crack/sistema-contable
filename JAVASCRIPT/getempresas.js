const cbxplanta = document.getElementById("planta");
cbxplanta.addEventListener("change", getprocesos);

const cbxproceso = document.getElementById("proceso");

function fetchandsetdata(url, formData, targetElement) {
  return fetch(url, {
    method: "POST",
    body: formData,
    mode: "cors",
  })
    .then((Response) => Response.json())
    .then((data) => {
      targetElement.innerHTML = data;
    })
    .catch((err) => console.log(err));
}

function getprocesos() {
  let planta = cbxplanta.value;
  let url = "config/getprocesos.php";
  let formData = new FormData();
  formData.append("id_planta", planta);

  fetchandsetdata(url, formData, cbxproceso);
}


