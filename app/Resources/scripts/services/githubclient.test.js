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

        this.$cookies = {
            get: function () {
                return null;
            },
            put: function () {
            }
        };

        this.queryParser = new QueryParser();

        this.client = new GitHubClient(
            serviceLocator.get('$http'),
            this.$cookies,
            this.gitHubConfig,
            this.queryParser
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
    this.$httpBackend.expectGET(this.gitHubConfig.apiUrl + '/user', {
        'Authorization': 'token ' + accessToken,
        'Accept': 'application/json, text/plain, */*'
    });

    this.$httpBackend.whenPOST(this.gitHubConfig.authUrl, data).respond(200, 'access_token=' + accessToken);
    this.$httpBackend.whenGET(this.gitHubConfig.apiUrl + '/user').respond(200, {
        name: 'foo',
        email: 'bar@baz.com',
        login: 'boo',
        organizations_url: 'url://'
    });

    this.client.authenticate(code).then(
        function (user) {
            assert.equal('foo', user.name);
            assert.equal('bar@baz.com', user.email);
            assert.equal('boo', user.username);
            assert.equal('url://', user._links.organizations);
        },
        function (error) {
        }
    );

    this.$httpBackend.flush(2);
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
        function (user) {
            assert.ok(false, 'Did not expect to successfully return an erroneous response.');
        },
        function (error) {
            assert.equal('foo', error.error);
            assert.equal('bar', error.description);
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
        function (user) {
            assert.ok(false, 'Did not expect to successfully return an erroneous response.');
        },
        function (error) {
            assert.equal('foo', error.error);
            assert.equal('bar', error.description);
        }
    );

    this.$httpBackend.flush();
});
