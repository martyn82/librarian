QUnit.module('LoginController', {
    beforeEach: function () {
        this.$scope = {};
        this.gitHubConfig = {
            clientId: 'foobar',
            scope: ['foo', 'bar']
        };
    }
});

QUnit.test('LoginController sets GitHub configuration in scope.', function (assert) {
    LoginController(
        this.$scope,
        this.gitHubConfig
    );

    assert.equal(this.$scope.gitHubClientId, this.gitHubConfig.clientId);
    assert.equal(this.$scope.gitHubScope, this.gitHubConfig.scope);
});
