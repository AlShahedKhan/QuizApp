document.querySelectorAll("[data-option]").forEach((option) => {
  option.addEventListener("click", () => {
    document
      .querySelectorAll("[data-option]")
      .forEach((node) => node.classList.remove("selected"));
    option.classList.add("selected");
  });
});
