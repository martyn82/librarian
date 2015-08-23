/**
 * @param $cookies
 * @param $q
 * @param gitHubClient
 * @param users
 * @param config
 * @constructor
 */
var Auth = function ($cookies, $q, gitHubClient, users, config) {
    var STORAGE_KEY = 'auth_access_token';

    var storage = $cookies;
    var promise = $q;
    var client = gitHubClient;

    var accessToken = null;
    var currentUser = null;
    var allowedCompanies = config.allowedUserCompanies;

    /**
     * @param githubUser
     * @returns {boolean}
     */
    var isAllowed = function (githubUser) {
        for (var i in githubUser.organizations) {
            if (allowedCompanies.indexOf(githubUser.organizations[i].login) > -1) {
                return true;
            }
        }

        return false;
    };

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
        currentUser = userValue;
    };

    /**
     * @returns {String}
     */
    var getToken = function() {
        if (accessToken === null) {
            accessToken = storage.get(STORAGE_KEY);
        }

        return accessToken || '';
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
        return getToken() !== '';
    };

    /**
     * @returns {{then: Function}}
     */
    this.getUser = function () {
        if (currentUser !== null) {
            return promise.resolve(currentUser);
        }

        var accessToken = getToken();

        return client.getUser(accessToken).then(
            function (githubUser) {
                return client.getOrganizations(accessToken).then(
                    function (organizations) {
                        githubUser.organizations = organizations;

                        if (!isAllowed(githubUser)) {
                            return promise.reject({
                                error: 'unauthorized',
                                description: 'User is not allowed.'
                            });
                        }

                        return users.create(githubUser.login, githubUser.email, githubUser.name).then(
                            function (user) {
                                setUser(user);
                                return promise.resolve(user);
                            },
                            function () {
                                return promise.reject({
                                    error: 'create_failed'
                                });
                            }
                        );
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