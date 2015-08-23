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

        this.users = {
            create: function () {
                return $q.resolve({});
            }
        };

        this.routes = {};
    }
});

QUnit.test('Authentication authorizes user.', function (assert) {
    var expectedAuthToken = '1234';
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar'
    };
    var $q = this.$q;

    this.auth.authorize = function (code) {
        assert.equal(code, expectedAuthToken);
        return $q.resolve(expectedUser);
    };

    this.$location.search = function () {
        return {
            code: expectedAuthToken
        };
    };

    var done = assert.async();

    this.$rootScope.$on('userSignedIn', function (_, user) {
        assert.equal(user.userName, expectedUser.userName);
        assert.equal(user.emailAddress, expectedUser.emailAddress);
        done();
    });

    assert.expect(3);

    AuthController(
        this.$location,
        this.$rootScope,
        this.auth,
        this.routes
    );
});

QUnit.test('Redirect to login page if authentication fails.', function (assert) {
    this.routes = {
        login: 'login'
    };

    var done = assert.async();
    var routes = this.routes;

    assert.expect(1);

    this.$location.path = function (route) {
        assert.equal(route, routes.login);
        done();
    };

    AuthController(
        this.$location,
        this.$rootScope,
        this.auth,
        this.routes
    );
});
