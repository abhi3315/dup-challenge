/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";

/**
 * External dependencies
 */
import { ThemeProvider, CssBaseline, createTheme } from "@mui/material";
import { QueryClient, QueryClientProvider } from "react-query";

/**
 * Internal dependencies
 */
import SettingsPage from "./pages/SettingsPage";

const theme = createTheme({
	palette: {
		primary: {
			main: "#556cd6",
		},
		secondary: {
			main: "#19857b",
		},
		error: {
			main: "#f44336",
		},
		background: {
			default: "#ffffff",
		},
	},
});


const { currentPage } = window.dupChallengeData;

const App = () => (
	<QueryClientProvider client={new QueryClient()}>
		<ThemeProvider theme={theme}>
			<CssBaseline />
			{currentPage === "duplicator-challenge-settings" && <SettingsPage />}
		</ThemeProvider>
	</QueryClientProvider>
);

export default App;
