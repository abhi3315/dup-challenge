/**
 * External Dependencies
 */
import axios, {AxiosInstance} from "axios";

const {restRootUrl, rootDir, nonce} = window.dupChallengeData;

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
	const {data} = await fetch.get(`${restRootUrl}/scan-status`);

	return data;
}

/**
 * Start scan
 *
 * @returns {Promise<any>} Scan status
 */
export const startScan = async (): Promise<any> => {
	const {data} = await fetch.post(`${restRootUrl}/start-scan`);

	return data;
}

/**
 * Get cron data
 * 
 * @returns {Promise<any>} Cron data
 */
export const getCronData = async (): Promise<any> => {
	const {data} = await fetch.get(`${restRootUrl}/scanner-cron`);

	return data;
}

/**
 * Update cron
 */
export const updateCronData = async (data: any): Promise<any> => {
	const {data: response} = await fetch.post(`${restRootUrl}/scanner-cron`, data);

	return response;
}
