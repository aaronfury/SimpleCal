const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const configs = Array.isArray( defaultConfig ) ? defaultConfig : [ defaultConfig ];

const [ mainConfig, ...otherConfigs ] = configs;

const resolveEntry = ( entry ) => {
	typeof entry === 'function' ? entry() : entry;
};

module.exports = [
	{
		...mainConfig,
		entry: async () => {
			const existing = await resolveEntry( mainConfig.entry );
			return {
				...existing,
				'query-block-variation/index': path.resolve(
					process.cwd(),
					'src/query-block-variation',
					'index.js'
				),
			};
		},
	},
	...otherConfigs,
];