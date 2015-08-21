QUnit.module('HomeController', {
    beforeEach: function () {
        var serviceLocator = angular.injector(['ng']);
        var $q = serviceLocator.get('$q');

        this.$scope = {};
        this.books = [];

        var books = this.books;
        this.librarianClient = {
            getAllBooks: function () {
                return $q.resolve(books);
            }
        };
    }
});

QUnit.test('HomeController retrieves all books.', function (assert) {
    HomeController(this.$scope, this.librarianClient);

    var done = assert.async();
    var $scope = this.$scope;
    var books = this.books;

    setTimeout(function () {
        assert.equal($scope.books, books);
        done();
    });
});
