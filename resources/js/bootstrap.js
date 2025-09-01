import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Set CSRF token
// Get CSRF token from meta tag or cookie
const getCsrfToken = () => {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        return metaTag.content;
    }

    // Fallback to reading from cookie
    const cookieValue = document.cookie
        .split("; ")
        .find((row) => row.startsWith("XSRF-TOKEN="))
        ?.split("=")[1];

    return cookieValue ? decodeURIComponent(cookieValue) : null;
};

const csrfToken = getCsrfToken();
if (csrfToken) {
    console.log("CSRF token found:", csrfToken);
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
}

// Import jQuery
import $ from "jquery";
window.$ = window.jQuery = $;
