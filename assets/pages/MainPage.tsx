/**
 * Internal dependencies
 */
import SearchBar from "../components/SearchBar";
import DirectoryTree from "../components/DirectoryTree";

/**
 * Main Page component
 *
 * @returns {JSX.Element}
 */
const MainPage = (): JSX.Element => {
	return (
		<>
			<SearchBar />
			<DirectoryTree parentId={1} />
		</>
	);
};

export default MainPage;
