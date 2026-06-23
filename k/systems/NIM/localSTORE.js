
document.addEventListener("DOMContentLoaded", () => {
  const pagekey = window.location.pathname;
  const PERSIST_FIELDS = [
    "soper_section",
    "POST__TAGS",
    "POST__EVENT_UNIX",
    "agent"
  ];

  // LOAD saved values
  PERSIST_FIELDS.forEach(name => {
    const saved = localStorage.getItem("sopr_" + name);
    if (saved !== null) {
      if (name === "agent") {
        const radio = document.querySelector(`input[name="agent"][value="${saved}"]`);
        if (radio) radio.checked = true;
      } else {
        const field = document.querySelector(`[name="${name}"]`);
        if (field) field.value = saved;
      }
    }
  });

  // SAVE on input/change
  PERSIST_FIELDS.forEach(name => {
    const fields = document.querySelectorAll(`[name="${name}"]`);

    fields.forEach(field => {
      field.addEventListener("input", () => {
        if (field.type === "radio") {
          if (field.checked) {
            localStorage.setItem("sopr_" + name, field.value);
          }
        } else {
          localStorage.setItem("sopr_" + name, field.value);
        }
      });

      field.addEventListener("change", () => {
        if (field.type === "radio" && field.checked) {
          localStorage.setItem("sopr_" + name, field.value);
        }
      });
    });
  });

  // CLEAR fragment after submit
  const form = document.querySelector("form");
  form.addEventListener("submit", () => {
    localStorage.removeItem("sopr_soper_leaf");
  });

});
