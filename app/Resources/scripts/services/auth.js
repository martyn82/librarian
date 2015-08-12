/**
 * @param $cookies
 * @param $q
 * @param gitHubClient
 * @constructor
 */
var Auth = function ($cookies, $q, gitHubClient) {
    var STORAGE_KEY = 'auth_access_token';

    var storage = $cookies;
    var promise = $q;
    var client = gitHubClient;

    var accessToken = null;
    var user = null;

    /**
     * @param {String} token
     */
    var setToken = function (token) {
        accessToken = token;
        storage.put(STORAGE_KEY, token);
    };

    /**
     * @param userValue
     */
    var setUser = function (userValue) {
        user = userValue;
    };

    /**
     * @returns {String}
     */
    var getToken = function() {
        if (accessToken === null) {
            accessToken = storage.get(STORAGE_KEY);
        }

        return accessToken;
    };

    /**
     */
    var revokeToken = function () {
        storage.remove(STORAGE_KEY);
    };

    /**
     */
    this.revokeAccess = function () {
        revokeToken.call(this);
    };

    /**
     * @param {String} authToken
     * @returns {{then: Function}}
     */
    this.authorize = function (authToken) {
        var self = this;
        return client.authenticate(authToken).then(
            function (accessToken) {
                setToken(accessToken);
                return self.getUser();
            },
            function (error) {
                self.revokeAccess();
                return promise.reject(error);
            }
        );
    };

    /**
     * @returns {Boolean}
     */
    this.authorized = function () {
        return getToken() !== null;
    };

    /**
     * @returns {{then: Function}}
     */
    this.getUser = function () {
        if (user !== null) {
            return promise.resolve(user);
        }

        var accessToken = getToken();

        return client.getUser(accessToken).then(
            function (user) {
                return client.getOrganizations(accessToken).then(
                    function (organizations) {
                        user.organizations = organizations;
                        setUser(user);
                        return promise.resolve(user);
                    },
                    function (error) {
                        return promise.reject(error);
                    }
                );
            },
            function (error) {
                return promise.reject(error);
            }
        );
    };
};