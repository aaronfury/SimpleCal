const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const configs = Array.isArray( defaultConfig ) ? defaultConfig : [ defaultConfig ];

module.exports = configs.map( ( config, index ) => {
    // Only add our extra entry to the FIRST (scripts) config.
    // The second config (if present) handles view modules via --experimental-modules
    // and shouldn't be touched.
    if ( index !== 0 ) {
        return config;
    }

    const originalEntry = config.entry;

    return {
        ...config,
        entry: async ( ...args ) => {
            const resolved =
                typeof originalEntry === 'function'
                    ? await originalEntry( ...args )
                    : originalEntry;

            return {
                ...resolved,
                'query-block-variation/index': path.resolve(
                    process.cwd(),
                    'src/query-block-variation',
                    'index.js'
                ),
            };
        },
    };
} );