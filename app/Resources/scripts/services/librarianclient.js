/**
 * @param $http
 * @param librarian
 * @constructor
 */
var LibrarianClient = function ($http, librarian) {
    this.$http = $http;

    /**
     * @returns {{then: Function}}
     */
    this.getAllBooks = function () {
        var self = this;
        var promise = {
            success: function (books) {
            },
            failure: function (error) {
            },
            execute: function () {
                var request = {
                    method: 'GET',
                    url: librarian.apiUrl + '/books'
                };

                this.$http(request).then(
                    function (response) {
                        promise.success(response.data);
                    },
                    function (response) {
                        promise.failure({});
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
     * @param {String} bookId
     * @returns {{then: Function}}
     */
    this.getBook = function (bookId) {
        var self = this;
        var promise = {
            success: function (book) {
            },
            failure: function (error) {
            },
            execute: function () {
                var request = {
                    method: 'GET',
                    url: librarian.apiUrl + '/books/' + bookId
                };

                this.$http(request).then(
                    function (response) {
                        promise.success(response.data);
                    },
                    function (response) {
                        promise.failure({});
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
};
