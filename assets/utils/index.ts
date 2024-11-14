/**
 * External Dependencies
 */
import axios, { AxiosInstance } from "axios";

const { restRootUrl, rootDir, nonce } = window.dupChallengeData;

/**
 * Fetch instance
 *
 * @type {AxiosInstance}
 */
const fetch: AxiosInstance = axios.create({
	baseURL: restRootUrl,
	headers: {
		"X-WP-Nonce": nonce,
	},
});

/**
 * Fetch scan status
 *
 * @returns {Promise<any>} Scan status
 */
export const fetchScanStatus = async (): Promise<any> => {
	const { data } = await fetch.get(`${restRootUrl}/scan-status`);

	return data;
};

/**
 * Start scan
 *
 * @returns {Promise<any>} Scan status
 */
export const startScan = async (): Promise<any> => {
	const { data } = await fetch.post(`${restRootUrl}/start-scan`);

	return data;
};

/**
 * Get cron data
 *
 * @returns {Promise<any>} Cron data
 */
export const getCronData = async (): Promise<any> => {
	const { data } = await fetch.get(`${restRootUrl}/scanner-cron`);

	return data;
};

/**
 * Update cron
 *
 * @param {any} data Cron data
 *
 * @returns {Promise<any>} Updated cron data
 */
export const updateCronData = async (data: any): Promise<any> => {
	const { data: response } = await fetch.post(
		`${restRootUrl}/scanner-cron`,
		data
	);

	return response;
};

/**
 * Search files and folders
 *
 * @param {string} searchValue Search value
 * @param {boolean} exactMatch Exact match
 *
 * @returns {Promise<any>} Search results
 */
export const searchFilesAndFolders = async (
	searchValue: string,
	exactMatch: boolean
): Promise<any> => {
	const { data } = await fetch.get(`${restRootUrl}/search`, {
		params: {
			query: searchValue,
			exact: exactMatch,
		},
	});

	return data;
};

/**
 * Get tree view data
 *
 * @param {string} parentId Parent ID
 *
 * @returns {Promise<any>} Tree view data
 */
export const getTreeViewData = async (parentId: number): Promise<any> => {
	const { data } = await fetch.get(`${restRootUrl}/tree-view`, {
		params: {
			id: parentId,
			view: "nested",
		},
	});

	return data;
};
