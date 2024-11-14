/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";

/**
 * External dependencies
 */
import { useState } from "react";
import { useQuery } from "react-query";
import { debounce } from "lodash";
import {
	TextField,
	Autocomplete,
	Switch,
	FormGroup,
	FormControlLabel,
	CircularProgress,
	Box,
	Grid2 as Grid,
} from "@mui/material";
import FolderIcon from "@mui/icons-material/Folder";
import FileIcon from "@mui/icons-material/InsertDriveFile";
import EmptyFolderIcon from "@mui/icons-material/FolderOpen";

/**
 * Internal dependencies
 */
import { searchFilesAndFolders } from "../utils";

// SearchBarProps interface
interface SearchBarProps {
	setParentId: (parentId: number) => void;
}

/**
 * Nav Component
 *
 * @param {Object} props
 * @param {Function} props.setParentId
 *
 * @returns {JSX.Element}
 */
const SearchBar = ({ setParentId }: SearchBarProps): JSX.Element => {
	const [searchValue, setSearchValue] = useState<string>("");
	const [exactMatch, setExactMatch] = useState<boolean>(false);

	const searchQuery = useQuery(
		["searchFilesAndFolders", searchValue, exactMatch],
		() => searchFilesAndFolders(searchValue, exactMatch),
		{
			enabled: searchValue.length > 2,
		}
	);

	return (
		<nav className="bg-white border-gray-200 px-10 shadow-md py-10 rounded-lg my-8">
			<h2 className="mb-4 text-3xl font-bold text-gray-800">
				{__("Search the files or folders", "dup-challenge")}
			</h2>
			<Autocomplete
				autoComplete
				size="medium"
				value={searchValue}
				options={searchQuery.data || []}
				noOptionsText={__("No results found", "dup-challenge")}
				onChange={(_, newValue: TreeItem) => {
					setParentId(newValue?.id);
				}}
				getOptionLabel={(option: any) =>
					typeof option === "string" ? option : option.path
				}
				onInputChange={debounce(
					(_: React.SyntheticEvent<Element, Event>, value: string) => {
						setSearchValue(value);
					},
					500
				)}
				renderInput={(params) => (
					<TextField
						{...params}
						label={__("Search", "dup-challenge")}
						slotProps={{
							input: {
								...params.InputProps,
								endAdornment: (
									<>
										{searchQuery.isLoading ? (
											<CircularProgress size={20} />
										) : null}
										{params.InputProps.endAdornment}
									</>
								),
							},
						}}
					/>
				)}
				renderOption={(props, option: any) => {
					if (!option || !option.path) {
						return null;
					}

					const { path } = option;
					const { key, ...optionProps } = props;

					const parts = path.split(
						new RegExp(`(${searchValue})`, "gi")
					) as string[];

					return (
						<li key={key} {...optionProps}>
							<Grid container sx={{ alignItems: "center" }}>
								<Grid sx={{ display: "flex", width: 44 }}>
									{option.type === "dir" ? (
										option.nodeCount === 0 ? (
											<EmptyFolderIcon sx={{ color: "text.secondary" }} />
										) : (
											<FolderIcon sx={{ color: "text.secondary" }} />
										)
									) : (
										<FileIcon sx={{ color: "text.secondary" }} />
									)}
								</Grid>
								<Grid
									sx={{ width: "calc(100% - 44px)", wordWrap: "break-word" }}
								>
									{}
									{parts.map((part, index) => (
										<Box
											key={index}
											component="span"
											sx={{
												fontWeight: part === searchValue ? "bold" : "regular",
											}}
										>
											{part}
										</Box>
									))}
								</Grid>
							</Grid>
						</li>
					);
				}}
			/>
			<FormGroup>
				<FormControlLabel
					control={
						<Switch
							checked={exactMatch}
							onChange={() => setExactMatch(!exactMatch)}
						/>
					}
					label="Exact match"
				/>
			</FormGroup>
		</nav>
	);
};

export default SearchBar;
