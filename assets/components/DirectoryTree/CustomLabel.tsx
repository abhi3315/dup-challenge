/**
 * External dependencies
 */
import { TreeItem2Label } from "@mui/x-tree-view";
import { Box, Typography } from "@mui/material";
import { styled } from "@mui/material/styles";

/**
 * Internal dependencies
 */
import DotIcon from "./DotIcon";

const StyledTreeItemLabelText = styled(Typography)({
	color: "inherit",
	fontFamily: "General Sans",
	fontWeight: 500,
}) as unknown as typeof Typography;

interface CustomLabelProps {
	children: React.ReactNode;
	icon?: React.ElementType;
	expandable?: boolean;
}

/**
 * CustomLabel component
 *
 * @param {Object} props
 *
 * @returns {JSX.Element}
 */
const CustomLabel = ({
	icon: Icon,
	expandable,
	children,
	...other
}: CustomLabelProps) => {
	return (
		<TreeItem2Label
			{...other}
			sx={{
				display: "flex",
				alignItems: "center",
			}}
		>
			{Icon && (
				<Box
					component={Icon}
					className="labelIcon"
					color="inherit"
					sx={{ mr: 1, fontSize: "1.2rem" }}
				/>
			)}

			<StyledTreeItemLabelText variant="body2">
				{children}
			</StyledTreeItemLabelText>
			{expandable && <DotIcon />}
		</TreeItem2Label>
	);
};

export default CustomLabel;