QUnit.module('LogOutController', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);
        this.$rootScope = serviceLocator.get('$rootScope');
    }
});

QUnit.test('LogOutController revokes authentication access.', function (assert) {
    assert.expect(2);

    var auth = {
        revokeAccess: function () {
            assert.ok(true, 'Expected authentication access to be revoked.');
        }
    };

    this.$rootScope.$on('userSignedOut', function () {
        assert.ok(true, 'Expected userSignedOut event to be fired.');
    });

    LogOutController(this.$rootScope, auth);
});
