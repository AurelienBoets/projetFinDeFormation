var isAnimating = false;
function scrollLeft(elem, item) {
  if (!elem || isAnimating) {
    // si element introuvable ou si l'animation est lancé
    return;
  }
  var time = 300; //duréé de l'animation
  var from = elem.scrollLeft;
  var aframe = 10; //fps
  isAnimating = true;
  var start = new Date().getTime(),
    timer = setInterval(function () {
      var step = Math.min(1, (new Date().getTime() - start) / time);
      elem.scrollLeft = step * item + from;
      if (step === 1) {
        clearInterval(timer);
        isAnimating = false;
      }
    }, aframe);
}

function initCarrousel(carrouselID) {
  var target = document.querySelector("#" + carrouselID + " .carrousel");
  var cardWidth;
  var maxScroll;
  function updateCarrousel() {
    cardWidth = document.querySelector(
      "#" + carrouselID + " .card"
    ).offsetWidth;
    maxScroll =
      document.querySelectorAll("#" + carrouselID + " .card").length *
        cardWidth -
      document.querySelector("#" + carrouselID + " .carrousel").clientWidth;
  }
  document
    .querySelector("#" + carrouselID + " .scroll-left")
    .addEventListener("click", function () {
      updateCarrousel();
      if (target.scrollLeft > 0) {
        scrollLeft(target, -cardWidth * 2);
      }
    });
  document
    .querySelector("#" + carrouselID + " .scroll-right")
    .addEventListener("click", function () {
      updateCarrousel();
      if (target.scrollLeft < maxScroll) {
        scrollLeft(target, cardWidth * 2);
      }
    });
}
initCarrousel("slider-container");
