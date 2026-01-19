const selectedInput = document.querySelector("[data-selected-option]");

document.querySelectorAll("[data-option]").forEach((option) => {
  option.addEventListener("click", () => {
    document
      .querySelectorAll("[data-option]")
      .forEach((node) => node.classList.remove("selected"));
    option.classList.add("selected");
    if (selectedInput) {
      selectedInput.value = option.getAttribute("data-option-value") || "";
    }
  });
});

document.querySelectorAll("[data-copy]").forEach((button) => {
  button.addEventListener("click", async () => {
    const targetId = button.getAttribute("data-copy");
    const target = document.getElementById(targetId);
    if (!target) {
      return;
    }
    try {
      await navigator.clipboard.writeText(target.textContent.trim());
      button.textContent = "কপি হয়েছে";
      setTimeout(() => {
        button.textContent = "কপি";
      }, 1500);
    } catch (error) {
      button.textContent = "কপি হয়নি";
    }
  });
});

const timerEl = document.querySelector("[data-quiz-timer]");
if (timerEl) {
  const totalSeconds = Number.parseInt(timerEl.getAttribute("data-seconds"), 10);
  const timeoutHref = timerEl.getAttribute("data-timeout-href");
  if (!Number.isNaN(totalSeconds) && totalSeconds > 0) {
    let remaining = totalSeconds;
    const pad = (value) => String(value).padStart(2, "0");
    const tick = () => {
      const minutes = Math.floor(remaining / 60);
      const seconds = remaining % 60;
      timerEl.textContent = `${pad(minutes)}:${pad(seconds)}`;
      if (remaining <= 0) {
        clearInterval(timerId);
        if (timeoutHref) {
          window.location.href = timeoutHref;
        }
      }
      remaining -= 1;
    };
    tick();
    const timerId = window.setInterval(tick, 1000);
  }
}
