/**
 * @param $scope
 * @param librarianClient
 */
var HomeController = function ($scope, librarianClient) {
    librarianClient.getAllBooks().then(
        function (books) {
            $scope.books = books;
        }
    );
};