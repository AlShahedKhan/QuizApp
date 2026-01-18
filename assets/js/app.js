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
