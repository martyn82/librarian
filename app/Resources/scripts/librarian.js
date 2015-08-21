/**
 * @param $rootScope
 * @param $location
 * @param auth
 * @param routes
 */
var Librarian = function ($rootScope, $location, auth, routes) {
    $rootScope.$on('$routeChangeStart', function (_, next) {
        if (
            !auth.authorized()
            && next.$$route.originalPath !== routes.login
            && next.$$route.originalPath !== routes.authenticate
            && next.$$route.originalPath !== routes.logout
        ) {
            $location.path(routes.login);
            return;
        }

        if (auth.authorized() && !$rootScope.user) {
            auth.getUser().then(
                function (user) {
                    $rootScope.user = user;
                }
            );
        }
    });

    $rootScope.$on('userSignedIn', function (_, user) {
        $rootScope.user = user;
        $location.path(routes.home);
    });

    $rootScope.$on('userSignedOut', function () {
        $location.path(routes.login);
    });
};
