/**
 * External dependencies
 */
import { styled, alpha } from "@mui/material/styles";
import { TreeItem2Content } from "@mui/x-tree-view/TreeItem2";

/**
 * TreeItemContent component
 * 
 * @param {Object} theme
 *
 * @returns {JSX.Element}
 */
const TreeItemContent = styled(TreeItem2Content)(({ theme }) => ({
	flexDirection: "row-reverse",
	borderRadius: theme.spacing(0.7),
	marginBottom: theme.spacing(0.5),
	marginTop: theme.spacing(0.5),
	padding: theme.spacing(0.5),
	paddingRight: theme.spacing(1),
	fontWeight: 500,
	[`&.Mui-expanded `]: {
		"&:not(.Mui-focused, .Mui-selected, .Mui-selected.Mui-focused) .labelIcon":
			{
				color: theme.palette.primary.dark,
				...theme.applyStyles("light", {
					color: theme.palette.primary.main,
				}),
			},
		"&::before": {
			content: '""',
			display: "block",
			position: "absolute",
			left: "16px",
			top: "44px",
			height: "calc(100% - 48px)",
			width: "1.5px",
			backgroundColor: theme.palette.grey[700],
			...theme.applyStyles("light", {
				backgroundColor: theme.palette.grey[300],
			}),
		},
	},
	"&:hover": {
		backgroundColor: alpha(theme.palette.primary.main, 0.1),
		color: "white",
		...theme.applyStyles("light", {
			color: theme.palette.primary.main,
		}),
	},
	[`&.Mui-focused, &.Mui-selected, &.Mui-selected.Mui-focused`]: {
		backgroundColor: theme.palette.primary.dark,
		color: theme.palette.primary.contrastText,
		...theme.applyStyles("light", {
			backgroundColor: theme.palette.primary.main,
		}),
	},
}));

export default TreeItemContent;
