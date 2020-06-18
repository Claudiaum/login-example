window.addEventListener("load", () => {
  const form = document.querySelector(".form-signin");
  const form_data = {};
  form.onsubmit = async (event) => {
    event.preventDefault();
    const inputs = Array.from(form.querySelectorAll("input"));
    inputs.forEach((element) => {
      element.disabled = true;
      form_data[element.name] = element.type.includes("check")
        ? element.checked
        : element.value;
    });
    await $.post(
      "functions/login_verification.php",
      form_data,
      (returned_data) => {
        if (returned_data.confirmation) {
          location.reload();
        } else {
          $("#login-message").show();
          inputs.forEach((element) => {
            element.disabled = false;
          });
        }
      },
      "json"
    );
  };
});
