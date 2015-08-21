/**
 * @param $http
 * @param $q
 * @param librarian
 * @constructor
 */
var LibrarianClient = function ($http, $q, librarian) {
    var config = librarian;
    var promise = $q;

    /**
     * @param {Number} page
     * @param {Number} size
     * @returns {{then: Function}}
     */
    this.getAllBooks = function (page, size) {
        page = Math.max(page || 1, 1);
        size = Math.max(size || 10, 1);

        return $http.get(config.apiUrl + '/books?page=' + page + '&size=' + size).then(
            function (response) {
                return promise.resolve(response.data);
            },
            function () {
                return promise.reject();
            }
        );
    };

    /**
     * @param {String} query
     * @param {String} filters
     * @param {Number} page
     * @param {Number} size
     * @returns {{then: Function}}
     */
    this.searchBooks = function (query, filters, page, size) {
        page = Math.max(page || 1, 1);
        size = Math.max(size || 10, 1);
        // /books?query=&title=&author=...&page=&size=
    };

    /**
     * @param {String} bookId
     * @returns {{then: Function}}
     */
    this.getBook = function (bookId) {
        return $http.get(config.apiUrl + '/book/' + bookId).then(
            function (response) {
                return promise.resolve(response.data);
            },
            function () {
                return promise.reject();
            }
        );
    };
};
