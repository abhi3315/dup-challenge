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
	const [parentId, setParentId] = useState<number>(0);

	return (
		<>
			<SearchBar setParentId={setParentId} />
			<DirectoryTree parentId={parentId} />
		</>
	);
};

export default MainPage;
