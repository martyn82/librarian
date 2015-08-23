QUnit.module('HomeController', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);
        var $q = serviceLocator.get('$q');

        this.$scope = {};
    }
});

QUnit.test('HomeController', function (assert) {
    assert.expect(0);
    HomeController(this.$scope);
});
