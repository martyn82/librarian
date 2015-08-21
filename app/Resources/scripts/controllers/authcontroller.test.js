QUnit.module('AuthController', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);

        this.$location = {
            search: function () {
                return {
                    code: '1234'
                };
            },
            path: function () {
                return '';
            }
        };

        this.$rootScope = serviceLocator.get('$rootScope');
        this.$q = serviceLocator.get('$q');

        var $q = this.$q;
        this.auth = {
            authorize: function () {
                return $q.reject({});
            }
        };

        this.routes = {};
        this.config = {
            allowedUserCompanies: [
                'FooInc'
            ]
        };
    }
});

QUnit.test('Authentication is done for users in allowed companies list.', function (assert) {
    var $q = this.$q;
    this.auth.authorize = function () {
        return $q.resolve({
            organizations: [
                {
                    login: 'FooInc'
                }
            ]
        });
    };

    var done = assert.async();

    this.$rootScope.$on('userSignedIn', function () {
        assert.ok(true, 'Expected userSignedIn event to be fired.');
        done();
    });

    AuthController(
        this.$location,
        this.$rootScope,
        this.auth,
        this.routes,
        this.config
    );
});

QUnit.test('Authentication is not done for users outside allowed companies list.', function (assert) {
    var $q = this.$q;
    this.auth.authorize = function () {
        return $q.resolve({
            organizations: [
                {
                    login: 'BarInc'
                }
            ]
        });
    };

    var done = assert.async();

    this.$rootScope.$on('userSignedIn', function () {
        assert.ok(false, 'Expected userSignedIn event NOT to be fired.');
        done();
    });

    AuthController(
        this.$location,
        this.$rootScope,
        this.auth,
        this.routes,
        this.config
    );

    setTimeout(function () {
        assert.ok(true, 'Expected authentication to fail.');
        done();
    });
});

QUnit.test('Redirect to login page if authentication fails.', function (assert) {
    this.routes = {
        login: 'login'
    };

    var done = assert.async();
    var routes = this.routes;

    this.$location.path = function (route) {
        assert.equal(route, routes.login);
        done();
    };

    AuthController(
        this.$location,
        this.$rootScope,
        this.auth,
        this.routes,
        this.config
    );
});