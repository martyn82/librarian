QUnit.module('Librarian', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);
        this.$rootScope = serviceLocator.get('$rootScope');
        this.$q = serviceLocator.get('$q');

        this.$location = {
            path: function (route) {
                return route;
            }
        };

        this.auth = {
            authorized: function () {
                return false;
            }
        };

        this.routes = {
            home: 'home',
            login: 'login',
            logout: 'logout',
            authenticate: 'authenticate'
        };

        this.user = {
            login: 'foo'
        };

        Librarian(this.$rootScope, this.$location, this.auth, this.routes);
    }
});

QUnit.test('Set user on userSignedIn event', function (assert) {
    this.$rootScope.$emit('userSignedIn', this.user);
    assert.equal(this.$rootScope.user, this.user);
});

QUnit.test('Redirect to home on userSignedIn event', function (assert) {
    assert.expect(1);

    var routes = this.routes;
    var done = assert.async();

    this.$location.path = function (route) {
        assert.equal(route, routes.home);
        done();
    };

    this.$rootScope.$emit('userSignedIn', this.user);
});

QUnit.test('Redirect to login on userSignedOut event', function (assert) {
    assert.expect(1);

    var routes = this.routes;
    var done = assert.async();

    this.$location.path = function (route) {
        assert.equal(route, routes.login);
        done();
    };

    this.$rootScope.$emit('userSignedOut');
});

QUnit.test('Unauthorized access will redirect to login', function (assert) {
    assert.expect(1);

    var routes = this.routes;
    var done = assert.async();

    this.$location.path = function (route) {
        assert.equal(route, routes.login);
        done();
    };

    var next = {
        '$$route': {
            originalPath: null
        }
    };

    this.$rootScope.$emit('$routeChangeStart', next);
});

QUnit.test('Authorized access will set user to scope if not already present', function (assert) {
    assert.expect(1);

    this.auth.authorized = function () {
        return true;
    };

    var $q = this.$q;
    var user = this.user;
    this.auth.getUser = function () {
        return $q.resolve(user);
    };

    this.$rootScope.$emit('$routeChangeStart', null);

    var $rootScope = this.$rootScope;
    var done = assert.async();

    setTimeout(function () {
        assert.equal($rootScope.user, user);
        done();
    }, 100);
});

QUnit.test('Unauthorized access to login page just goes to login page.', function (assert) {
    assert.expect(0);

    this.$location.path = function (route) {
        assert.ok(false, 'Expected not to be redirected.');
    };

    var next = {
        '$$route': {
            originalPath: this.routes.login
        }
    };

    this.$rootScope.$emit('$routeChangeStart', next);

    var done = assert.async();
    setTimeout(function () {
        done();
    }, 100);
});
