const toggleBtn = document.getElementById("themeToggle");
    const html = document.documentElement;
    const icon = document.getElementById("themeIcon");

    // Step A: Load saved theme
    if (localStorage.getItem("theme") === "dark") {
        html.classList.add("dark");
        icon.textContent = "dark_mode";
    }

    // Step B: Toggle theme
    toggleBtn.addEventListener("click", () => {

        // animation start
        icon.classList.add("scale-0", "rotate-180");

        setTimeout(() => {

            // switch theme
            html.classList.toggle("dark");

            // save theme
            if (html.classList.contains("dark")) {
                localStorage.setItem("theme", "dark");
                icon.textContent = "dark_mode";
            } else {
                localStorage.setItem("theme", "light");
                icon.textContent = "light_mode";
            }

            // animation end
            icon.classList.remove("scale-0", "rotate-180");

        }, 200);

});

        

    