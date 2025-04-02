document.addEventListener("DOMContentLoaded", function () {
  const emailInput = document.getElementById("email");
  const emailError = document.getElementById("emailError");
  const form = document.getElementById("registerForm");

  // 禁用浏览器默认弹窗提示
  emailInput.addEventListener("invalid", function (event) {
    event.preventDefault();
  });

  // 每次输入时清空提示
  emailInput.addEventListener("input", function () {
    emailError.textContent = "";
    emailInput.setCustomValidity("");
  });

  form.addEventListener("submit", async function (event) {
    if (!emailInput.validity.valid) {
      event.preventDefault();
      if (emailInput.validity.valueMissing) {
        emailError.textContent = "Email is required!";
      } else if (emailInput.validity.typeMismatch) {
        emailError.textContent = "Please enter a valid email address!";
      }
      emailInput.setCustomValidity(" ");
      emailInput.reportValidity();
      return;
    }

    event.preventDefault();
    const data = {
      username: document.getElementById("username").value,
      email: emailInput.value,
      password: document.getElementById("password").value,
    };

    try {
      const res = await fetch("http://localhost:8080/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await res.json();
      const msg = document.getElementById("resultMessage");

      if (res.ok) {
        msg.style.color = "green";
        msg.textContent = "✅ Inscription réussie !";
      } else {
        msg.style.color = "red";
        msg.textContent = "❌ " + result.error;
      }
    } catch (error) {
      document.getElementById("resultMessage").textContent = "Erreur réseau.";
    }
  });
});
