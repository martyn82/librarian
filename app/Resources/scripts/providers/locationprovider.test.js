QUnit.module('LocationProvider');

QUnit.test('LocationProvider set html 5 mode', function (assert) {
    var locationProvider = {
        html5Mode: function (value) {
            assert.ok(value);
        }
    };

    LocationProvider(locationProvider);
});