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
  errorBox?.classList.remove("is-visible");

  const mobile = mobileInput?.value.trim() ?? "";
  const password = passwordInput?.value.trim() ?? "";

  if (mobileInput && mobileError) {
    clearError(mobileInput, mobileError);
  }
  if (passwordInput && passwordError) {
    clearError(passwordInput, passwordError);
  }

  if (mobileInput && mobileError && !/^01\d{9}$/.test(mobile)) {
    showError(
      mobileInput,
      mobileError,
      "মোবাইল নম্বরটি ১১ ডিজিটের হতে হবে এবং 01 দিয়ে শুরু হবে।"
    );
    valid = false;
  }

  if (passwordInput && passwordError && password.length < 6) {
    showError(
      passwordInput,
      passwordError,
      "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।"
    );
    valid = false;
  }

  if (!valid && errorBox) {
    errorBox.classList.add("is-visible");
    errorBox.textContent =
      "দয়া করে সঠিক তথ্য দিন। সব ঘর পূরণ করে আবার চেষ্টা করুন।";
  }

  return valid;
};

form?.addEventListener("submit", (event) => {
  if (!validate()) {
    event.preventDefault();
  }
});

mobileInput?.addEventListener("input", () => {
  if (mobileError) {
    clearError(mobileInput, mobileError);
  }
});
passwordInput?.addEventListener("input", () => {
  if (passwordError) {
    clearError(passwordInput, passwordError);
  }
});

toggleButton?.addEventListener("click", () => {
  if (!passwordInput) {
    return;
  }
  const isHidden = passwordInput.type === "password";
  passwordInput.type = isHidden ? "text" : "password";
  toggleButton.setAttribute(
    "aria-label",
    isHidden ? "পাসওয়ার্ড দেখান" : "পাসওয়ার্ড লুকান"
  );
  toggleButton.textContent = isHidden ? "দেখান" : "লুকান";
});
