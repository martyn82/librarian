/**
 * @constructor
 */
var QueryParser = function () {
    /**
     * @param {String} queryString
     * @returns {Object}
     */
    this.parse = function (queryString) {
        queryString = queryString || '';
        var parameters = queryString.split('&');
        var params = {};
        for (var k in parameters) {
            var keyValue = parameters[k].split('=');
            params[keyValue[0]] = keyValue[1];
        }
        return params;
    };
};
