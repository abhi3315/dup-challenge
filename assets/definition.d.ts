/**
 * Window definition
 */
declare interface Window {
	/**
	 * Duplicator Challenge Plugin
	 */
	dupChallengeData: {
		restRootUrl: string;
		rootDir: string;
		nonce: string;
		currentPage: string;
	};
}

/**
 * Tree item definition
 */
declare interface TreeItem {
	id: number;
	name: string;
	path: string;
	type: string;
	nodeCount: number;
	parentId?: number;
	size?: number;
	lastModified?: string;
	depth?: number;
	children?: TreeItem[];
}