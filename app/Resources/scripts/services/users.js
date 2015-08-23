/**
 * @param $http
 * @param $q
 * @param usersConfig
 * @constructor
 */
var Users = function ($http, $q, usersConfig) {
    var promise = $q;
    var config = usersConfig;
    var CONTENT_TYPE = 'application/json;charset=utf-8';

    /**
     * @param response
     * @returns user
     */
    var mapUser = function (response) {
        return {
            _id: response.data._id,
            _version: response.headers('ETag'),
            userName: response.data.user_name,
            emailAddress: response.data.email_address,
            fullName: response.data.full_name
        }
    };

    /**
     * @param {String} id
     * @param {String} version [optional]
     * @returns {{then: Function}}
     */
    this.get = function (id, version) {
        var request = {
            method: 'GET',
            url: config.baseUrl + '/' + id,
            headers: {
                'Accept': CONTENT_TYPE
            }
        };

        if (version) {
            request.headers['If-None-Match'] = version;
        }

        return $http(request).then(
            function (response) {
                return promise.resolve(mapUser(response));
            },
            function (response) {
                return promise.reject(response.data);
            }
        );
    };

    /**
     * @param {String} userName
     * @param {String} version [optional]
     * @returns {{then: Function}}
     */
    this.getByUserName = function (userName, version) {
        var request = {
            method: 'GET',
            url: config.baseUrl + '?user_name=' + encodeURIComponent(userName),
            headers: {
                'Accept': CONTENT_TYPE
            }
        };

        if (version) {
            request.headers['If-None-Match'] = version;
        }

        return $http(request).then(
            function (response) {
                return promise.resolve(mapUser(response));
            },
            function (response) {
                return promise.reject(response.data);
            }
        );
    };

    /**
     * @param {String} userName
     * @param {String} emailAddress
     * @param {String} fullName
     * @returns {{then: Function}}
     */
    this.create = function (userName, emailAddress, fullName) {
        var newUser = {
            'user_name': userName,
            'email_address': emailAddress,
            'full_name': fullName
        };

        var request = {
            method: 'PUT',
            url: config.baseUrl,
            data: newUser,
            headers: {
                'Accept': CONTENT_TYPE,
                'Content-Type': CONTENT_TYPE + ';domain-model=create-user'
            }
        };

        return $http(request).then(
            function (response) {
                return promise.resolve({
                    _id: response.data._id,
                    _version: response.headers('ETag'),
                    userName: response.data.user_name,
                    emailAddress: response.data.email_address,
                    fullName: response.data.full_name
                });
            },
            function (response) {
                return promise.reject(response.data);
            }
        );
    };
};
