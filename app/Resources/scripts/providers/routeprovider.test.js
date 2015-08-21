QUnit.module('RouteProvider');

QUnit.test('RouteProvider initializes route configuration', function (assert) {
    var routeProvider = {
        when: function () {
            assert.ok(true);
            return this;
        },
        otherwise: function () {
            assert.ok(true);
            return true;
        }
    };

    RouteProvider(routeProvider, {}, '/foo');
});
