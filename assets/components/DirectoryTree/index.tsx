/**
 * WordPress Dependencies
 */
import { __ } from "@wordpress/i18n";

/**
 * External Dependencies
 */
import { useQuery } from "react-query";
import { RichTreeView } from "@mui/x-tree-view";
import { CircularProgress } from "@mui/material";

/**
 * Internal Dependencies
 */
import TreeItem from "./TreeItem";
import { getTreeViewData } from "../../utils";

// File types
export type FileType = "file" | "dir" | "link" | "socket" | "unknown";

// RichTreeItem interface
interface RichTreeItem {
	id: string;
	label: string;
	fileType: FileType;
	children?: RichTreeItem[];
}

/**
 * CustomTreeItem Component
 */
const parseTreeItems = (item: TreeItem): RichTreeItem => {
	const { size, nodeCount, name } = item;

	let label = `${name} (${nodeCount} items)`;

	if (size) {
		label = `${label} - ${size} bytes`;
	}

	return {
		id: String(item.id),
		label: label,
		fileType: item.type as FileType,
		children: item.children?.map(parseTreeItems),
	};
};

/**
 * DirectoryTree Component
 */
const DirectoryTree = ({ parent }: { parent: TreeItem|null }) => {
	const treeViewQuery = useQuery(["treeViewData", parent?.id], () =>
		getTreeViewData(parent?.id)
	);

	if (treeViewQuery.isLoading) {
		return <CircularProgress />;
	}

	let items: RichTreeItem[] = [];

	if (treeViewQuery.data) {
		items = [parseTreeItems(treeViewQuery.data)];
	} else if(parent) {
		items = [parseTreeItems(parent)];
	}

	return items ? (
		<RichTreeView
			items={items}
			slots={{ item: TreeItem }}
		/>
	) : (
		<p className="text-center text-gray-500 font-bold text-2xl">
			{__("No data found", "dup-challenge")}
		</p>
	);
};

export default DirectoryTree;
