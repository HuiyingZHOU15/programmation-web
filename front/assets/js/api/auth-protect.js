// auth-protect.js

// Vérification que l'utilisateur est connecté
document.addEventListener("DOMContentLoaded", function () {
    const user = sessionStorage.getItem("user");
  
    if (!user) {
      // Si pas connecté, rediriger vers login
      window.location.href = "/front/auth/login.html";
    }
  
    // Gestion du bouton Déconnexion
    const logoutButton = document.getElementById("logoutButton");
  
    if (logoutButton) {
      logoutButton.addEventListener("click", function () {
        sessionStorage.clear(); // Vider toutes les données de session
        window.location.href = "/front/auth/login.html"; // Redirection vers login
      });
    }
  });
  