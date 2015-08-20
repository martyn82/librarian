exports.config = {
    baseUrl: 'http://localhost:8081',
    seleniumAddress: 'http://localhost:4444/wd/hub',
    capabilities: {
        browserName: 'chrome'
    },
    framework: 'cucumber',
    specs: [
        'tests/features/**/*.feature'
    ],
    cucumberOpts: {
        require: 'tests/steps/*.js',
        format: 'pretty'
    }
};
