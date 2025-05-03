document.addEventListener("DOMContentLoaded", () => {
  // --- Gestion du formulaire d'inscription ---
  const registerForm = document.getElementById("register-form");
  if (registerForm) {
    const message = document.getElementById("message");

    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value;

      // ✅ Envoi de la requête d'inscription
      $.ajax({
        url: "/back/index.php?action=register",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ username, password }),
        success: function (result) {
          message.style.color = "green";
          message.textContent = "Inscription réussie ! Redirection...";

          // ✅ Connexion automatique après inscription (optionnel)
          setTimeout(() => {
            window.location.href = "/front/dashboard.html";
          }, 1500);
        },
        error: function (xhr) {
          message.style.color = "red";
          message.textContent = "Erreur : " + xhr.responseText;
        }
      });
    });
  }

  // --- Gestion du formulaire de connexion ---
  const loginForm = document.getElementById("login-form");
  if (loginForm) {
    const message = document.getElementById("message");

    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value;

      // ✅ Envoi de la requête de connexion
      $.ajax({
        url: "/back/index.php?action=login",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ username, password }),
        success: function (result) {
          if (result.success) {
            // ✅ Normaliser les rôles en minuscules (plus facile à traiter)
            const roles = result.user.roles;

            // ✅ Stocker les données utilisateur dans sessionStorage
            const userData = {
              username: result.user.username,
              roles: roles
            };
            sessionStorage.setItem("user", JSON.stringify({
              username: result.user.username,
              roles: roles
            }));

            message.style.color = "green";
            message.textContent = "Connexion réussie ! Redirection...";

            // 🔁 Redirection vers tableau de bord unique
            setTimeout(() => {
              window.location.href = "/front/dashboard.html";
            }, 1500);
          } else {
            message.style.color = "red";
            message.textContent = result.message;
          }
        },
        error: function (xhr) {
          message.style.color = "red";
          message.textContent = "Erreur : " + xhr.responseText;
        }
      });
    });
  }
});