QUnit.module('Auth', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);
        this.$q = serviceLocator.get('$q');

        this.gitHubClient = {
            authenticate: function () {},
            getUser: function () {},
            getOrganizations: function () {}
        };

        this.cookies = {
            get: function () {},
            put: function () {},
            remove: function () {}
        };

        this.auth = new Auth(
            this.cookies,
            this.$q,
            this.gitHubClient
        );
    }
});

QUnit.test('Authorize will retrieve user with organizations', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var expectedUser = {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo'
    };
    var expectedOrganizations = [
        {
            login: 'Acme Inc.'
        }
    ];

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedUser);
    };

    this.gitHubClient.getOrganizations = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedOrganizations);
    };

    var done = assert.async();

    this.auth.authorize(expectedAuthToken).then(
        function (user) {
            assert.equal(user.name, expectedUser.name);
            assert.equal(user.email, expectedUser.email);
            assert.equal(user.login, expectedUser.login);
            assert.equal(user.organizations, expectedOrganizations);
        },
        function () {}
    );

    setTimeout(function () {
        assert.expect(7);
        done();
    });
});

QUnit.test('Authorize rejects promise on error retrieving organizations', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var expectedUser = {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo'
    };

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedUser);
    };

    this.gitHubClient.getOrganizations = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.reject();
    };

    var done = assert.async();

    this.auth.authorize(expectedAuthToken).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected');
        }
    );

    setTimeout(function () {
        assert.expect(4);
        done();
    });
});

QUnit.test('Authorize rejects promise on error retrieving user', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.reject();
    };

    var done = assert.async();

    this.auth.authorize(expectedAuthToken).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected');
        }
    );

    setTimeout(function () {
        assert.expect(3);
        done();
    });
});

QUnit.test('Authorize rejects promise on error on authentication', function (assert) {
    var expectedAuthToken = 'foobar';
    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.reject();
    };

    var done = assert.async();

    this.auth.authorize(expectedAuthToken).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected');
        }
    );

    setTimeout(function () {
        assert.expect(2);
        done();
    });
});

QUnit.test('Authorized retrieves authorization status', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var expectedUser = {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo'
    };
    var expectedOrganizations = [
        {
            login: 'Acme Inc.'
        }
    ];

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedUser);
    };

    this.gitHubClient.getOrganizations = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedOrganizations);
    };

    var done = assert.async();
    var self = this;

    assert.ok(!this.auth.authorized());

    this.auth.authorize(expectedAuthToken).then(
        function (user) {
            assert.ok(self.auth.authorized());
        },
        function () {}
    );

    setTimeout(function () {
        assert.expect(5);
        done();
    });
});

QUnit.test('RevokeAccess revokes access', function (assert) {
    assert.expect(1);

    this.cookies.remove = function () {
        assert.ok(true);
    };

    this.auth.revokeAccess();
});

QUnit.test('GetUser retrieves user with organizations', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var expectedUser = {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo'
    };
    var expectedOrganizations = [
        {
            login: 'Acme Inc.'
        }
    ];

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedUser);
    };

    this.gitHubClient.getOrganizations = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedOrganizations);
    };

    var done = assert.async();
    var self = this;

    this.auth.authorize(expectedAuthToken).then(
        function (user) {
            self.auth.getUser().then(
                function (savedUser) {
                    assert.equal(savedUser, user);
                }
            );
        },
        function () {}
    );

    setTimeout(function () {
        assert.expect(4);
        done();
    });
});
