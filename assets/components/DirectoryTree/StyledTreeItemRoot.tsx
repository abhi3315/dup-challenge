/**
 * External dependencies
 */
import { styled } from "@mui/material/styles";
import { TreeItem2Root } from "@mui/x-tree-view/TreeItem2";
import { treeItemClasses } from "@mui/x-tree-view/TreeItem";

/**
 * StyledTreeItemRoot
 *
 * @param {Object} theme
 *
 * @returns {JSX.Element}
 */
const StyledTreeItemRoot = styled(TreeItem2Root)(({ theme }) => ({
	color: theme.palette.grey[400],
	position: "relative",
	[`& .${treeItemClasses.groupTransition}`]: {
		marginLeft: theme.spacing(3.5),
	},
	...theme.applyStyles("light", {
		color: theme.palette.grey[800],
	}),
})) as unknown as typeof TreeItem2Root;

export default StyledTreeItemRoot;
