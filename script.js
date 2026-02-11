document.addEventListener("DOMContentLoaded", () => {
    // Cookie management functions
    const setCookie = (cName, cValue, expDays) => {
        const date = new Date();
        date.setTime(date.getTime() + expDays * 24 * 60 * 60 * 1000);
        const expires = "expires=" + date.toUTCString();
        document.cookie = `${cName}=${cValue}; ${expires}; path=/`;
    };

    const getCookie = (cName) => {
        const name = cName + "=";
        const cDecoded = decodeURIComponent(document.cookie);
        const cArr = cDecoded.split("; ");
        for (let val of cArr) {
            if (val.indexOf(name) === 0) {
                return val.substring(name.length);
            }
        }
        return null;
    };

    // Handle cookie consent button click
    document.querySelector("#cookies-btn").addEventListener("click", () => {
        document.querySelector("#cookies").style.display = "none";
        setCookie("cookieConsent", "true", 2); // Cookie name updated for clarity
    });

    // Show the cookie consent message if the cookie is not set
    if (!getCookie("cookieConsent")) {
        document.querySelector("#cookies").style.display = "block";
    }
});
