exports.config = {
    baseUrl: 'http://localhost:8081',
    seleniumAddress: 'http://127.0.0.1:4444/wd/hub',
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
