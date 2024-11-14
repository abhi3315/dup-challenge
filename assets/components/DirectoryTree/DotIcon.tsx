/**
 * External dependencies
 */
import { Box } from "@mui/material";

/**
 * DotIcon component
 *
 * @returns {JSX.Element}
 */
const DotIcon = (): JSX.Element => {
	return (
		<Box
			sx={{
				width: 6,
				height: 6,
				borderRadius: "70%",
				bgcolor: "warning.main",
				display: "inline-block",
				verticalAlign: "middle",
				zIndex: 1,
				mx: 1,
			}}
		/>
	);
};

export default DotIcon;
