/**
 * External Dependencies
 */
import { forwardRef } from "react";
import clsx from "clsx";
import {
	TreeItem2Provider,
	useTreeItem2,
	UseTreeItem2Parameters,
	TreeItem2Icon,
	TreeItem2DragAndDropOverlay,
} from "@mui/x-tree-view";
import {
	TreeItem2Checkbox,
	TreeItem2IconContainer,
} from "@mui/x-tree-view/TreeItem2";
import {
	FolderRounded as FolderRoundedIcon,
	Article as ArticleIcon,
	Power as SocketIcon,
	FilePresent as LinkIcon,
	Quiz as UnknownIcon,
	FolderOpen as FolderEmptyIcon,
} from "@mui/icons-material";

/**
 * Internal Dependencies
 */
import TreeItemContent from "./TreeItemContent";
import StyledTreeItemRoot from "./StyledTreeItemRoot";
import TransitionComponent from "./TransitionComponent";
import CustomLabel from "./CustomLabel";
import { FileType } from ".";

/**
 * Check if the children are expandable
 *
 * @param {React.ReactNode} reactChildren
 *
 * @returns {boolean}
 */
const isExpandable = (reactChildren: React.ReactNode): boolean => {
	if (Array.isArray(reactChildren)) {
		return reactChildren.length > 0 && reactChildren.some(isExpandable);
	}
	return Boolean(reactChildren);
};

/**
 * Get icon from file type
 *
 * @param {FileType} fileType
 */
const getIconFromFileType = (fileType: FileType) => {
	switch (fileType) {
		case "file":
			return ArticleIcon;
		case "dir":
			return FolderRoundedIcon;
		case "link":
			return LinkIcon;
		case "socket":
			return SocketIcon;
		default:
			return UnknownIcon;
	}
};

/**
 * Tree Item Props
 */
interface TreeItemProps
	extends Omit<UseTreeItem2Parameters, "rootRef">,
		Omit<React.HTMLAttributes<HTMLLIElement>, "onFocus"> {}

/**
 * Custom Tree Item
 *
 * @param {Object} props
 * @param {Object} ref
 *
 * @returns {JSX.Element}
 */
const TreeItem = forwardRef(function TreeItem(
	props: TreeItemProps,
	ref: React.Ref<HTMLLIElement>
) {
	const { id, itemId, label, disabled, children, ...other } = props;

	const {
		getRootProps,
		getContentProps,
		getIconContainerProps,
		getCheckboxProps,
		getLabelProps,
		getGroupTransitionProps,
		getDragAndDropOverlayProps,
		status,
		publicAPI,
	} = useTreeItem2({ id, itemId, children, label, disabled, rootRef: ref });

	const item = publicAPI.getItem(itemId);
	const expandable = isExpandable(children);
	let icon;
	if (!expandable && item.fileType === "dir" && !item.children) {
		icon = FolderEmptyIcon;
	} else if (item.fileType) {
		icon = getIconFromFileType(item.fileType);
	}

	return (
		<TreeItem2Provider itemId={itemId}>
			<StyledTreeItemRoot {...getRootProps(other)}>
				<TreeItemContent
					{...getContentProps({
						className: clsx("content", {
							"Mui-expanded": status.expanded,
							"Mui-selected": status.selected,
							"Mui-focused": status.focused,
							"Mui-disabled": status.disabled,
						}),
					})}
				>
					<TreeItem2IconContainer {...getIconContainerProps()}>
						<TreeItem2Icon status={status} />
					</TreeItem2IconContainer>
					<TreeItem2Checkbox {...getCheckboxProps()} />
					<CustomLabel
						{...getLabelProps({
							icon,
							expandable: expandable && status.expanded,
						})}
					/>
					<TreeItem2DragAndDropOverlay {...getDragAndDropOverlayProps()} />
				</TreeItemContent>
				{children && <TransitionComponent {...getGroupTransitionProps()} />}
			</StyledTreeItemRoot>
		</TreeItem2Provider>
	);
});

export default TreeItem;
