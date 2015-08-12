/**
 * @param $http
 * @param $q
 * @param config
 * @param queryParser
 * @constructor
 */
var GitHubClient = function ($http, $q, config, queryParser) {
    var accessToken = null;
    var promise = $q;

    /**
     * @param {String} code
     * @returns {{then: Function}}
     */
    this.authenticate = function (code) {
        return $http.post(config.authUrl, {
                client_id: config.clientId,
                client_secret: config.secret,
                code: code
            }
        ).then(
            function (response) {
                var params = queryParser.parse(response.data);

                if (params.error) {
                    return promise.reject({
                        error: params.error,
                        description: params.error_description
                    });
                }

                return promise.resolve(params.access_token);
            },
            function (response) {
                var err = {
                    error: null,
                    description: null
                };

                if (response.data !== null) {
                    var params = queryParser.parse(response.data);
                    err.error = params.error;
                    err.description = params.error_description;
                }

                return promise.reject(err);
            }
        );
    };

    /**
     * @param {String} accessToken
     * @returns {{then: Function}}
     */
    this.getUser = function (accessToken) {
        var self = this;
        var request = {
            method: 'GET',
            url: config.apiUrl + '/user',
            headers: {
                'Authorization': 'token ' + accessToken
            }
        };

        return $http(request).then(
            function (response) {
                var err = {
                    error: null,
                    description: null
                };

                if (response.data === null) {
                    return promise.reject(err);
                }

                return promise.resolve(response.data);
            },
            function (response) {
                return promise.reject({
                    error: null,
                    description: null
                });
            }
        );
    };

    /**
     * @param {String} accessToken
     * @returns {{then: Function}}
     */
    this.getOrganizations = function (accessToken) {
        var request = {
            method: 'GET',
            url: config.apiUrl + '/user/orgs',
            headers: {
                'Authorization': 'token ' + accessToken
            }
        };

        return $http(request).then(
            function (response) {
                if (response.data === null) {
                    return promise.reject({
                        error: 'nodata',
                        description: null
                    });
                }

                return promise.resolve(response.data);
            },
            function (response) {
                return promise.reject({
                    error: null,
                    description: null
                });
            }
        );
    };
};