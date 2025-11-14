// Escolha de temas - claro ou escuro
const toggleBtn = document.getElementById("themeToggle");
const themeLink = document.getElementById("themeStylesheet");

if (toggleBtn && themeLink) {
    const DARK_THEME = "/css/style7.css";
    const LIGHT_THEME = "/css/style5.css";

    const setTheme = (href) => {
        themeLink.setAttribute("href", href);
        localStorage.setItem("theme", href);
        toggleBtn.textContent = href === DARK_THEME ? "Tema: Escuro" : "Tema: Claro";
    };

    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === DARK_THEME || savedTheme === LIGHT_THEME) {
        setTheme(savedTheme);
    } else {
        toggleBtn.textContent = themeLink.getAttribute("href") === DARK_THEME ? "Tema: Escuro" : "Tema: Claro";
    }

    toggleBtn.addEventListener("click", () => {
        const nextTheme = themeLink.getAttribute("href") === DARK_THEME ? LIGHT_THEME : DARK_THEME;
        setTheme(nextTheme);
    });
}
