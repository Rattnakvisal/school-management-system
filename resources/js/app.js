import "./bootstrap";
import "./admin/dashboard";
import "./Teacher/Notifications";
import "./Student/Notifications";
import { bootHomeAnimations } from "./website/home";
import { bootShellLoader, hideShellLoader } from "./app/shell-loader";

const bootApp = (options = {}) => {
    bootShellLoader();
    bootHomeAnimations(options);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => bootApp());
} else {
    bootApp();
}

window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        hideShellLoader({ minVisible: false });
        bootHomeAnimations({ replayPageBlocks: true, replayReveal: true });
    }
});
