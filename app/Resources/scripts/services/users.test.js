QUnit.module('Users', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ngMock']);
        this.$httpBackend = serviceLocator.get('$httpBackend');

        this.usersConfig = {
            baseUrl: '/api/users'
        };

        this.client = new Users(
            serviceLocator.get('$http'),
            serviceLocator.get('$q'),
            this.usersConfig
        );
    },

    afterEach: function () {
        this.$httpBackend.verifyNoOutstandingExpectation();
        this.$httpBackend.verifyNoOutstandingRequest();
    }
});

QUnit.test('Get finds User by ID', function (assert) {
    var id = '1234';
    var tag = 'abcdef';
    var userData = {
        '_id': id,
        'user_name': 'foo',
        'email_address': 'bar'
    };

    this.$httpBackend.expectGET(this.usersConfig.baseUrl + '/' + id);
    this.$httpBackend.whenGET(this.usersConfig.baseUrl + '/' + id).respond(200, userData, {
        'ETag': tag
    });

    assert.expect(4);

    this.client.get(id).then(
        function (user) {
            assert.equal(user._id, userData._id);
            assert.equal(user._version, tag);
            assert.equal(user.userName, userData.user_name);
            assert.equal(user.emailAddress, userData.email_address);
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Get finds User by ID and version', function (assert) {
    var id = '1234';
    var tag = 'abcdef';
    var userData = {
        '_id': id,
        'user_name': 'foo',
        'email_address': 'bar'
    };

    this.$httpBackend.expectGET(this.usersConfig.baseUrl + '/' + id, {
        'Accept': 'application/json;charset=utf-8',
        'If-None-Match': tag
    });
    this.$httpBackend.whenGET(this.usersConfig.baseUrl + '/' + id).respond(200, userData, {
        'ETag': tag
    });

    assert.expect(4);

    this.client.get(id, tag).then(
        function (user) {
            assert.equal(user._id, userData._id);
            assert.equal(user._version, tag);
            assert.equal(user.userName, userData.user_name);
            assert.equal(user.emailAddress, userData.email_address);
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Get rejects promise on error', function (assert) {
    var id = '1234';

    this.$httpBackend.expectGET(this.usersConfig.baseUrl + '/' + id, {
        'Accept': 'application/json;charset=utf-8'
    });
    this.$httpBackend.whenGET(this.usersConfig.baseUrl + '/' + id).respond(404);

    assert.expect(1);

    this.client.get(id).then(
        function () {
        },
        function (data) {
            assert.ok(true);
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Create puts user to service', function (assert) {
    var userData = {
        'user_name': 'foo',
        'email_address': 'bar'
    };

    this.$httpBackend.expectPUT(this.usersConfig.baseUrl);
    this.$httpBackend.whenPUT(this.usersConfig.baseUrl, userData).respond(201, {
        '_id': '1234',
        'user_name': userData.user_name,
        'email_address': userData.email_address
    });

    assert.expect(2);

    this.client.create(userData.user_name, userData.email_address).then(
        function (user) {
            assert.equal(user.userName, userData.user_name);
            assert.equal(user.emailAddress, userData.email_address);
        }
    );

    this.$httpBackend.flush();
});

QUnit.test('Create rejects promise on error', function (assert) {
    var userData = {
        'user_name': 'foo',
        'email_address': 'bar'
    };

    this.$httpBackend.expectPUT(this.usersConfig.baseUrl);
    this.$httpBackend.whenPUT(this.usersConfig.baseUrl, userData).respond(400);

    assert.expect(1);

    this.client.create(userData.user_name, userData.email_address).then(
        function () {
        },
        function () {
            assert.ok(true);
        }
    );

    this.$httpBackend.flush();
});
