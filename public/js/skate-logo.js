const skateboard = document.getElementById('a-skateboard');
const skateboardWrapper = skateboard.querySelector('.skateboard-wrapper');

skateboard.addEventListener('click', function () {
  // Désactive temporairement l'animation de hover
  skateboard.classList.add('no-animation');

  // Ajoute la classe pour le 360 flip
  skateboardWrapper.classList.add('flip-360');

  // Ajoute un délai de 2 secondes (2000 millisecondes) avant la redirection
  setTimeout(function () {
    // Réactive l'animation de hover
    skateboard.classList.remove('no-animation');

    // Redirige vers {{ path('app_home') }} après le délai
    window.location.href = '{{ path("app_home") }}';
  }, 2000); // 2000 ms = 2 secondes de délai
});
