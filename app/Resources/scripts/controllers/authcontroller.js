/**
 * @param $location
 * @param $rootScope
 * @param auth
 * @param routes
 */
var AuthController = function ($location, $rootScope, auth, routes) {
    var authRequestToken = $location.search().code;
    $location.search('code', null);

    auth.authorize(authRequestToken).then(
        function (user) {
            $rootScope.$emit('userSignedIn', user);
        },
        function () {
            $location.path(routes.login);
        }
    );
};
