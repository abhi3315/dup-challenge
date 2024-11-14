/**
 * External dependencies
 */
import { animated, useSpring } from "@react-spring/web";
import { Collapse } from "@mui/material";
import { TransitionProps } from "@mui/material/transitions";

const AnimatedCollapse = animated(Collapse);

/**
 * TransitionComponent
 *
 * @param {Object} props
 *
 * @returns {JSX.Element}
 */
const TransitionComponent = (props: TransitionProps) => {
	const style = useSpring({
		to: {
			opacity: props.in ? 1 : 0,
			transform: `translate3d(0,${props.in ? 0 : 20}px,0)`,
		},
	});

	return <AnimatedCollapse style={style} {...props} />;
};

export default TransitionComponent;
