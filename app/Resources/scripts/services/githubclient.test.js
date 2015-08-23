QUnit.module('GitHubClient', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ngMock']);
        this.$httpBackend = serviceLocator.get('$httpBackend');

        this.gitHubConfig = {
            clientId: 'abcdef',
            secret: '12ab34cd',
            scope: ['user'],
            authUrl: 'https://example.org/auth',
            apiUrl: 'https://api.example.org/v1'
        };

        this.client = new GitHubClient(
            serviceLocator.get('$http'),
            serviceLocator.get('$q'),
            this.gitHubConfig,
            new QueryParser()
        );
    },

    afterEach: function () {
        this.$httpBackend.verifyNoOutstandingExpectation();
        this.$httpBackend.verifyNoOutstandingRequest();
    }
});

QUnit.test('Authenticate will authenticate user.', function (assert) {
    var code = 'xyzuvw';
    var data = {
        client_id: this.gitHubConfig.clientId,
        client_secret: this.gitHubConfig.secret,
        code: code
    };
    var accessToken = 'foo';

    this.$httpBackend.expectPOST(this.gitHubConfig.authUrl, data);
    this.$httpBackend.whenPOST(this.gitHubConfig.authUrl, data).respond(200, 'access_token=' + accessToken);

    this.client.authenticate(code).then(
        function (accessToken) {
            assert.equal(accessToken, 'foo');
        },
        function () {
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Authenticate parses error in response body.', function (assert) {
    var code = 'xyzuvw';
    var data = {
        client_id: this.gitHubConfig.clientId,
        client_secret: this.gitHubConfig.secret,
        code: code
    };

    this.$httpBackend.whenPOST(this.gitHubConfig.authUrl, data).respond(200, 'error=foo&error_description=bar');

    this.client.authenticate(code).then(
        function () {
            assert.ok(false, 'Did not expect to successfully return an erroneous response.');
        },
        function (error) {
            assert.equal(error.error, 'foo');
            assert.equal(error.description, 'bar');
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Authenticate parses error in error response body.', function (assert) {
    var code = 'xyzuvw';
    var data = {
        client_id: this.gitHubConfig.clientId,
        client_secret: this.gitHubConfig.secret,
        code: code
    };

    this.$httpBackend.whenPOST(this.gitHubConfig.authUrl, data).respond(401, 'error=foo&error_description=bar');

    this.client.authenticate(code).then(
        function () {
            assert.ok(false, 'Did not expect to successfully return an erroneous response.');
        },
        function (error) {
            assert.equal(error.error, 'foo');
            assert.equal(error.description, 'bar');
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Authenticate rejects promise on error.', function (assert) {
    var code = 'xyzuvw';
    var data = {
        client_id: this.gitHubConfig.clientId,
        client_secret: this.gitHubConfig.secret,
        code: code
    };

    this.$httpBackend.whenPOST(this.gitHubConfig.authUrl, data).respond(401, null);

    this.client.authenticate(code).then(
        function () {
            assert.ok(false, 'Did not expect to successfully return an erroneous response.');
        },
        function () {
            assert.ok(true);
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetUser will get user information from github API', function (assert) {
    var accessToken = 'foo';

    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user').respond(200, {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo',
        organizations_url: 'url://'
    });

    assert.expect(3);

    this.client.getUser(accessToken).then(
        function (user) {
            assert.equal(user.name, 'foo');
            assert.equal(user.email, 'bar@baz.com');
            assert.equal(user.login, 'boo');
        },
        function () {
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetUser will reject promise if not authenticated', function (assert) {
    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user').respond(401);
    assert.expect(1);

    this.client.getUser(null).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected.');
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetUser will reject promise if response body empty', function (assert) {
    var accessToken = 'foo';

    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user').respond(200, null);

    this.client.getUser(accessToken).then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Expected promise to be rejected.');
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetOrganizations will retrieve user organizations', function (assert) {
    var accessToken = 'foo';

    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user/orgs').respond(200, [
        {
            description: '',
            login: 'Foo'
        },
        {
            description: '',
            login: 'Bar'
        }
    ]);

    this.client.getOrganizations(accessToken).then(
        function (organizations) {
            assert.equal(organizations.length, 2);
            assert.equal(organizations[0].login, 'Foo');
            assert.equal(organizations[1].login, 'Bar');
        },
        function () {
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetOrganizations will reject promise if response is empty', function (assert) {
    var accessToken = 'foo';

    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user/orgs').respond(200, null);

    this.client.getOrganizations().then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Promise was expected to be rejected.');
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('GetOrganizations will reject promise on error', function (assert) {
    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user/orgs').respond(401,[]);

    this.client.getOrganizations().then(
        function () {
        },
        function (error) {
            assert.ok(true, 'Promise was expected to be rejected.');
        }
    );

    this.$httpBackend.flush();
});
