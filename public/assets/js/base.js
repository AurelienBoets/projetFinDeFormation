if (document.cookie === "dark") {
  document.body.classList.add("dark");
  document.querySelector(".checkDark").checked = true;
}
if (document.cookie === "light") {
  if (document.body.classList.contains("dark")) {
    document.body.classList.remove("dark");
    document.querySelector(".checkDark").checked = false;
  }
}
function burgerAnimated(x) {
  x.classList.toggle("change"); // active l'animation
  var menu = document.querySelector(".categorie");
  var modal = document.querySelector(".modal");
  var body = document.querySelector("body");
  menu.classList.toggle("mobile"); // fait apparaitre les catégorie
  if (x.classList.contains("change")) {
    // si le menu est ouvert alors
    body.style.overflow = "hidden"; //on bloque le scroll de la page
    menu.style.overflow = "scroll"; // on active le scroll pour le menu
    modal.style.display = "block"; // on assombrit l'arrière plan
    if (document.querySelector("#slider-container")) {
      //si il existe
      document.querySelector("#slider-container").style.position = "unset";
    }
  } else {
    //sinon on met le css comme il était
    body.style.overflow = "scroll";
    modal.style.display = "none";
    document.querySelector("#slider-container").style.position = "unset";
  }
}
function darkMode() {
  var element = document.body;
  element.classList.toggle("dark");
  if (element.classList.contains("dark")) {
    document.cookie = "dark;path=/";
  } else {
    document.cookie = "light;path=/";
  }
}
