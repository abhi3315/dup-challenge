/**
 * External dependencies
 */
import { createRoot } from "react-dom/client";

/**
 * Internal dependencies
 */
import App from "./App";

// Import Tailwind + SCSS
import "./styles.scss";

// Create a root React element
const root = createRoot(document.getElementById("dup-challenge-root")!);
root.render(App());
