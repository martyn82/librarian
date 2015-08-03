/**
 * @param $http
 * @param $cookies
 * @param gitHub
 * @param queryParser
 * @constructor
 */
var GitHubClient = function ($http, $cookies, gitHub, queryParser) {
    this.$http = $http;
    this.storage = $cookies;
    this.config = gitHub;
    this.accessToken = null;
    this.queryParser = queryParser;

    /**
     * @returns {Boolean}
     */
    this.authenticated = function () {
        return this.getToken() != null;
    };

    /**
     * @param {String} code
     * @returns {{then: Function}}
     */
    this.authenticate = function (code) {
        var self = this;
        var promise = {
            success: function (user) {
            },
            failure: function (error) {
            },
            execute: function () {
                this.$http.post(
                    this.config.authUrl,
                    {
                        client_id: this.config.clientId,
                        client_secret: this.config.secret,
                        code: code
                    })
                    .then(function (response) {
                        var params = self.queryParser.parse(response.data);

                        if (params.error != undefined) {
                            promise.failure({
                                error: params.error,
                                description: params.error_description
                            });
                            return;
                        }

                        self.setToken(params.access_token);
                        self.getUser().then(function (user) {
                            promise.success(user);
                        }, function (error) {
                            promise.failure(error);
                        });
                    }, function (response) {
                        if (response.data == null) {
                            promise.failure({
                                error: null,
                                description: null
                            });
                            return;
                        }

                        var params = self.queryParser.parse(response.data);
                        promise.failure({
                            error: params.error,
                            description: params.error_description
                        });
                    })
                ;
            }
        };

        return {
            then: function (success, failure) {
                promise.success = success;
                promise.failure = failure;
                promise.execute.call(self);
            }
        };
    };

    /**
     * @param {String} token
     */
    this.setToken = function (token) {
        this.accessToken = token;
        this.storage.put('oauth_token', token);
    };

    /**
     * @returns {String}
     */
    this.getToken = function() {
        if (this.accessToken == null) {
            this.accessToken = this.storage.get('oauth_token');
        }

        return this.accessToken;
    };

    /**
     */
    this.revokeToken = function () {
        this.storage.remove('oauth_token');
    };

    /**
     * @returns {{then: Function}}
     */
    this.getUser = function () {
        var self = this;
        var promise = {
            success: function (user) {
            },
            failure: function (error) {
            },
            execute: function () {
                if (self.getToken() == null) {
                    promise.failure({});
                    return;
                }

                var request = {
                    method: 'GET',
                    url: self.config.apiUrl + '/user',
                    headers: {
                        'Authorization': 'token ' + self.accessToken
                    }
                };

                this.$http(request)
                    .then(function (response) {
                        if (response.data == null) {
                            promise.failure({
                                error: null,
                                description: null
                            });
                            return;
                        }
                        promise.success({
                            email: response.data.email,
                            name: response.data.name,
                            username: response.data.login,
                            '_links': {
                                organizations: response.data.organizations_url
                            }
                        });
                    }, function (response) {
                        self.revoke();
                        promise.failure({
                            error: null,
                            description: null
                        });
                    }
                );
            }
        };

        return {
            then: function (success, failure) {
                promise.success = success;
                promise.failure = failure;
                promise.execute.call(self);
            }
        };
    };

    /**
     */
    this.revoke = function () {
        this.revokeToken();
    };
};