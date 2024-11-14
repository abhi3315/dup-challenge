/**
 * External dependencies
 */
import { useState } from "react";

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
	const [parent, setParent] = useState<TreeItem|null>(null);

	return (
		<>
			<SearchBar setParent={setParent} />
			<DirectoryTree parent={parent} />
		</>
	);
};

export default MainPage;
