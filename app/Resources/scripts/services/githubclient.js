/**
 * @param $http
 * @param $cookies
 * @param $q
 * @param config
 * @param queryParser
 * @constructor
 */
var GitHubClient = function ($http, $cookies, $q, config, queryParser) {
    var accessToken = null;
    var storage = $cookies;
    var storageKey = 'oauth_token';
    var promise = $q;

    /**
     * @param {String} token
     */
    var setToken = function (token) {
        accessToken = token;
        storage.put(storageKey, token);
    };

    /**
     * @returns {String}
     */
    var getToken = function() {
        if (accessToken == null) {
            accessToken = storage.get(storageKey);
        }

        return accessToken;
    };

    /**
     */
    var revokeToken = function () {
        storage.remove(storageKey);
    };

    /**
     * @returns {Boolean}
     */
    this.authenticated = function () {
        return getToken.call(this) != null;
    };

    /**
     * @param {String} code
     * @returns {{then: Function}}
     */
    this.authenticate = function (code) {
        var self = this;
        return $http.post(config.authUrl, {
                client_id: config.clientId,
                client_secret: config.secret,
                code: code
            }
        ).then(
            function (response) {
                var params = queryParser.parse(response.data);

                if (params.error != undefined) {
                    return promise.reject({
                        error: params.error,
                        description: params.error_description
                    });
                }

                setToken.call(self, params.access_token);
                return self.getUser();
            },
            function (response) {
                var err = {
                    error: null,
                    description: null
                };

                if (response.data != null) {
                    var params = queryParser.parse(response.data);
                    err.error = params.error;
                    err.description = params.error_description;
                }

                return promise.reject(err);
            }
        );
    };

    /**
     * @returns {{then: Function}}
     */
    this.getUser = function () {
        var self = this;
        var request = {
            method: 'GET',
            url: config.apiUrl + '/user',
            headers: {
                'Authorization': 'token ' + accessToken
            }
        };

        if (getToken.call(self) == null) {
            return promise.reject({});
        }

        return $http(request).then(
            function (response) {
                var err = {
                    error: null,
                    description: null
                };

                if (response.data == null) {
                    return promise.reject(err);
                }

                return promise.resolve({
                    email: response.data.email,
                    name: response.data.name,
                    username: response.data.login,
                    '_links': {
                        organizations: response.data.organizations_url
                    }
                });
            },
            function (response) {
                self.revoke();
                return promise.reject({
                    error: 'revoked',
                    description: null
                });
            }
        );
    };

    /**
     */
    this.revoke = function () {
        revokeToken.call(this);
    };
};