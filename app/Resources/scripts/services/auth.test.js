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

        var $q = this.$q;
        this.users = {
            create: function () {
                return $q.resolve();
            }
        };

        this.config = {
            allowedUserCompanies: [
                'FooInc'
            ]
        };

        this.auth = new Auth(
            this.cookies,
            this.$q,
            this.gitHubClient,
            this.users,
            this.config
        );
    }
});

QUnit.test('Authorize authorizes, authenticates, and registers user', function (assert) {
    var expectedAuthToken = '1234';
    var expectedAccessToken = 'abcdef';
    var expectedGitHubUser = {
        login: 'foo',
        email: 'bar',
        organizations_url: 'url://'
    };
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar'
    };

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedGitHubUser);
    };

    this.gitHubClient.getOrganizations = function (gitHubUser) {
        return $q.resolve([
            {
                login: 'FooInc'
            }
        ]);
    };

    this.users.create = function (login, email) {
        assert.equal(login, expectedGitHubUser.login);
        assert.equal(email, expectedGitHubUser.email);
        return $q.resolve(expectedUser);
    };

    var done = assert.async();
    this.auth.authorize(expectedAuthToken).then(
        function (user) {
            assert.equal(user.userName, expectedUser.userName);
            assert.equal(user.emailAddress, expectedUser.emailAddress);
            done();
        }
    );

    assert.expect(6);
});

QUnit.test('Authorize authorizes, authenticates, and registers user once.', function (assert) {
    var expectedAuthToken = '1234';
    var expectedAccessToken = 'abcdef';
    var expectedGitHubUser = {
        login: 'foo',
        email: 'bar',
        organizations_url: 'url://'
    };
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar'
    };

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedGitHubUser);
    };

    this.gitHubClient.getOrganizations = function (gitHubUser) {
        return $q.resolve([
            {
                login: 'FooInc'
            }
        ]);
    };

    this.users.create = function (login, email) {
        assert.equal(login, expectedGitHubUser.login);
        assert.equal(email, expectedGitHubUser.email);
        return $q.resolve(expectedUser);
    };

    var done = assert.async();
    var self = this;

    this.auth.authorize(expectedAuthToken).then(
        function (user) {
            assert.equal(user.userName, expectedUser.userName);
            assert.equal(user.emailAddress, expectedUser.emailAddress);

            self.auth.authorize(expectedAuthToken).then(
                function (sameUser) {
                    assert.equal(sameUser, user);
                    done();
                }
            );
        }
    );

    assert.expect(8);
});

QUnit.test('Authorize rejects promise if user is not allowed', function (assert) {
    var expectedAuthToken = '1234';
    var expectedAccessToken = 'abcdef';
    var expectedGitHubUser = {
        login: 'foo',
        email: 'bar',
        organizations_url: 'url://'
    };
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar'
    };

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedGitHubUser);
    };

    this.gitHubClient.getOrganizations = function (gitHubUser) {
        return $q.resolve([
            {
                login: 'AcmeInc' // unallowed company
            }
        ]);
    };

    var done = assert.async();
    this.auth.authorize(expectedAuthToken).then(
        function () {},
        function () {
            assert.ok(true);
            done();
        }
    );

    assert.expect(3);
});

QUnit.test('Authorize rejects promise if registration fails', function (assert) {
    var expectedAuthToken = '1234';
    var expectedAccessToken = 'abcdef';
    var expectedGitHubUser = {
        login: 'foo',
        email: 'bar',
        organizations_url: 'url://'
    };
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar'
    };

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedGitHubUser);
    };

    this.gitHubClient.getOrganizations = function (gitHubUser) {
        return $q.resolve([
            {
                login: 'FooInc'
            }
        ]);
    };

    this.users.create = function (login, email) {
        assert.equal(login, expectedGitHubUser.login);
        assert.equal(email, expectedGitHubUser.email);
        return $q.reject();
    };

    var done = assert.async();
    this.auth.authorize(expectedAuthToken).then(
        function () {},
        function () {
            assert.ok(true);
            done();
        }
    );

    assert.expect(5);
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
        return $q.reject({});
    };

    var done = assert.async();

    this.auth.authorize(expectedAuthToken).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected');
            done();
        }
    );

    assert.expect(4);
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
            done();
        }
    );

    assert.expect(3);
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
            done();
        }
    );

    assert.expect(2);
});

QUnit.test('Authorized retrieves authorization status', function (assert) {
    var expectedAuthToken = 'foobar';
    var expectedAccessToken = 'bazboo';
    var expectedGitHubUser = {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo'
    };
    var expectedUser = {
        userName: 'foo',
        emailAddress: 'bar@baz.com'
    };
    var expectedOrganizations = [
        {
            login: 'FooInc'
        }
    ];

    var $q = this.$q;

    this.gitHubClient.authenticate = function (authToken) {
        assert.equal(authToken, expectedAuthToken);
        return $q.resolve(expectedAccessToken);
    };

    this.gitHubClient.getUser = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedGitHubUser);
    };

    this.gitHubClient.getOrganizations = function (accessToken) {
        assert.equal(accessToken, expectedAccessToken);
        return $q.resolve(expectedOrganizations);
    };

    this.users.create = function () {
        return $q.resolve(expectedUser);
    };

    var done = assert.async();
    var self = this;

    assert.ok(!this.auth.authorized());

    this.auth.authorize(expectedAuthToken).then(
        function () {
            assert.ok(self.auth.authorized());
            done();
        },
        function () {}
    );

    assert.expect(5);
});

QUnit.test('RevokeAccess revokes access', function (assert) {
    assert.expect(1);

    this.cookies.remove = function () {
        assert.ok(true);
    };

    this.auth.revokeAccess();
});
