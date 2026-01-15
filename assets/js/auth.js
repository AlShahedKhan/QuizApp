const form = document.querySelector("[data-auth-form]");
const mobileInput = document.querySelector("[data-mobile]");
const passwordInput = document.querySelector("[data-password]");
const errorBox = document.querySelector("[data-error-box]");
const mobileError = document.querySelector("[data-mobile-error]");
const passwordError = document.querySelector("[data-password-error]");
const toggleButton = document.querySelector("[data-toggle-password]");

const showError = (input, messageEl, message) => {
  input.classList.add("is-invalid");
  messageEl.textContent = message;
};

const clearError = (input, messageEl) => {
  input.classList.remove("is-invalid");
  messageEl.textContent = "";
};

const validate = () => {
  let valid = true;
  errorBox.classList.remove("is-visible");

  const mobile = mobileInput.value.trim();
  const password = passwordInput.value.trim();

  clearError(mobileInput, mobileError);
  clearError(passwordInput, passwordError);

  if (!/^01\d{9}$/.test(mobile)) {
    showError(
      mobileInput,
      mobileError,
      "মোবাইল নম্বর ১১ ডিজিটের হতে হবে এবং ০১ দিয়ে শুরু করতে হবে।"
    );
    valid = false;
  }

  if (password.length < 6) {
    showError(
      passwordInput,
      passwordError,
      "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।"
    );
    valid = false;
  }

  if (!valid) {
    errorBox.classList.add("is-visible");
    errorBox.textContent = "ভুল তথ্য পাওয়া গেছে। অনুগ্রহ করে ঠিক করে দিন।";
  }

  return valid;
};

form?.addEventListener("submit", (event) => {
  if (!validate()) {
    event.preventDefault();
  }
});

mobileInput?.addEventListener("input", () =>
  clearError(mobileInput, mobileError)
);
passwordInput?.addEventListener("input", () =>
  clearError(passwordInput, passwordError)
);

toggleButton?.addEventListener("click", () => {
  const isHidden = passwordInput.type === "password";
  passwordInput.type = isHidden ? "text" : "password";
  toggleButton.setAttribute(
    "aria-label",
    isHidden ? "পাসওয়ার্ড লুকান" : "পাসওয়ার্ড দেখান"
  );
  toggleButton.textContent = isHidden ? "লুকান" : "দেখান";
});
