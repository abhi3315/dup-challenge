/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";

/**
 * External dependencies
 */
import { useState, useEffect } from "react";
import { useQuery, useMutation } from "react-query";
import {
	Button,
	Switch,
	TextField,
	LinearProgress,
	CircularProgress,
	Box,
	Skeleton,
} from "@mui/material";

/**
 * Internal dependencies
 */
import {
	fetchScanStatus,
	startScan,
	getCronData,
	updateCronData,
} from "../utils";

/**
 * ScanPage component
 *
 * @returns {JSX.Element}
 */
const ScanPage = (): JSX.Element => {
	const [cronEnabled, setCronEnabled] = useState<boolean>(false);
	const [cronInterval, setCronInterval] = useState<number>(0);

	const scanQuery = useQuery("scanStatus", fetchScanStatus, {
		refetchInterval: (data) => (data?.status === "STARTED" ? 5000 : false), // Polling every 5 seconds if scan is started
	});

	const cronQuery = useQuery("cronData", getCronData);

	const startScanMutation = useMutation(startScan, {
		onSuccess: () => {
			scanQuery.refetch();
		},
	});

	const updateCronMutation = useMutation(updateCronData, {
		onSuccess: () => {
			cronQuery.refetch();
		},
	});

	useEffect(() => {
		if (cronQuery.data) {
			setCronEnabled(!!cronQuery.data.enabled);
			setCronInterval(cronQuery.data.interval);
		}
	}, [cronQuery.data]);

	return (
		<>
			<h2 className="mb-4 text-3xl font-bold text-gray-800">
				{__("Settings", "dup-challenge")}
			</h2>
			<div className="bg-white border-gray-200 px-6 shadow-md py-6 rounded-lg my-8 inline-block relative overflow-hidden">
				{scanQuery?.data?.status === "STARTED" && (
					<LinearProgress
						color="secondary"
						sx={{
							position: "absolute",
							top: 0,
							left: 0,
							width: "100%",
						}}
					/>
				)}
				{scanQuery.isLoading || cronQuery.isLoading ? (
					<Box sx={{ width: 300 }}>
						<Skeleton />
						<Skeleton animation="wave" />
						<Skeleton animation={false} />
					</Box>
				) : (
					<table className="table-auto text-lg">
						<tr>
							<td className="px-4 py-2 pr-12 font-bold">
								{__("Status", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								{scanQuery?.data?.status === "NOT_STARTED" && (
									<span className="text-gray-400 italic">
										{__("Not Started", "dup-challenge")}
									</span>
								)}

								{scanQuery?.data?.status === "STARTED" && (
									<span className="text-blue-600">
										{__("Scanning...", "dup-challenge")}
									</span>
								)}

								{scanQuery?.data?.status === "COMPLETED" && (
									<span className="text-green-600">
										{__("Completed", "dup-challenge")}
									</span>
								)}
							</td>
						</tr>
						<tr>
							<td className="px-4 py-2 pr-12 font-bold">
								{__("Started At", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								{scanQuery?.data?.startedAt ? (
									new Date(scanQuery?.data?.startedAt * 1000).toLocaleString()
								) : (
									<span className="text-gray-400 italic">N/A</span>
								)}
							</td>
						</tr>
						<tr>
							<td className="px-4 py-2 pr-12 font-bold">
								{__("Completed At", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								{scanQuery?.data?.finishedAt ? (
									new Date(scanQuery?.data?.finishedAt * 1000).toLocaleString()
								) : (
									<span className="text-gray-400 italic">N/A</span>
								)}
							</td>
						</tr>
						<tr>
							<td className="px-4 py-2 pr-12 font-bold">
								{__("Total Scanned Items", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								{scanQuery?.data?.totalScannedItems || 0}
							</td>
						</tr>
						<tr className="border-b-2">
							<td />
							<td className="px-4 py-2 pl-12 pb-6">
								<Button
									variant="contained"
									color="primary"
									disabled={scanQuery?.data?.status === "STARTED"}
									onClick={() => startScanMutation.mutate()}
									endIcon={
										scanQuery?.data?.status === "STARTED" && (
											<CircularProgress size={20} />
										)
									}
								>
									{scanQuery?.data?.status === "STARTED"
										? __("Scanning...", "dup-challenge")
										: __("Start Scan", "dup-challenge")}
								</Button>
							</td>
						</tr>
						<tr>
							<td className="px-4 py-2 pr-12 font-bold pt-6">
								{__("Enable Cron", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								<Switch
									checked={cronEnabled}
									onClick={() => setCronEnabled((prev) => !prev)}
								/>
							</td>
						</tr>
						<tr>
							<td className="px-4 py-2 pr-12 font-bold">
								{__("Cron Interval in Hours", "dup-challenge")}
							</td>
							<td className="px-4 py-2 pl-12">
								<TextField
									type="number"
									variant="standard"
									value={cronInterval}
									onChange={(e) => {
										const value = parseInt(e.target.value, 10);
										if (value < 1 || value > 24) return;

										setCronInterval(value);
									}}
								/>
							</td>
						</tr>
						<tr>
							<td />
							<td className="px-4 py-2 pl-12 pb-6">
								<Button
									variant="contained"
									color="primary"
									onClick={() =>
										updateCronMutation.mutate({
											enabled: cronEnabled,
											interval: cronInterval,
										})
									}
								>
									{__("Save", "dup-challenge")}
								</Button>
							</td>
						</tr>
					</table>
				)}
			</div>
		</>
	);
};

export default ScanPage;
