const form = document.querySelector("[data-auth-form]");
const mobileInput = document.querySelector("[data-mobile]");
const passwordInput = document.querySelector("[data-password]");
const confirmInput = document.querySelector("[data-password-confirm]");
const otpInput = document.querySelector("[data-otp]");
const errorBox = document.querySelector("[data-error-box]");
const mobileError = document.querySelector("[data-mobile-error]");
const passwordError = document.querySelector("[data-password-error]");
const confirmError = document.querySelector("[data-confirm-error]");
const otpError = document.querySelector("[data-otp-error]");
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
  const confirmPassword = confirmInput?.value.trim() ?? "";
  const otp = otpInput?.value.trim() ?? "";
  const isOtpOnlyStep = form?.hasAttribute("data-otp-step");

  if (mobileInput && mobileError) {
    clearError(mobileInput, mobileError);
  }
  if (passwordInput && passwordError) {
    clearError(passwordInput, passwordError);
  }
  if (confirmInput && confirmError) {
    clearError(confirmInput, confirmError);
  }
  if (otpInput && otpError) {
    clearError(otpInput, otpError);
  }

  if (mobileInput && mobileError && !/^01\d{9}$/.test(mobile)) {
    showError(mobileInput, mobileError, "Enter a valid 11-digit mobile number (01XXXXXXXXX).");
    valid = false;
  }

  if (!isOtpOnlyStep && passwordInput && passwordError && password.length < 6) {
    showError(passwordInput, passwordError, "Password must be at least 6 characters.");
    valid = false;
  }

  if (otpInput && otpError && !/^\d{6}$/.test(otp)) {
    showError(otpInput, otpError, "OTP must be exactly 6 digits.");
    valid = false;
  }

  if (
    confirmInput &&
    confirmError &&
    passwordInput &&
    passwordError &&
    !isOtpOnlyStep &&
    confirmPassword !== password
  ) {
    showError(confirmInput, confirmError, "Password and confirm password do not match.");
    valid = false;
  }

  if (!valid && errorBox) {
    errorBox.classList.add("is-visible");
    errorBox.textContent = "Please provide valid information and try again.";
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
confirmInput?.addEventListener("input", () => {
  if (confirmError) {
    clearError(confirmInput, confirmError);
  }
});
otpInput?.addEventListener("input", () => {
  if (otpError) {
    clearError(otpInput, otpError);
  }
});

toggleButton?.addEventListener("click", () => {
  if (!passwordInput) {
    return;
  }

  const isHidden = passwordInput.type === "password";
  passwordInput.type = isHidden ? "text" : "password";
  toggleButton.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
});
