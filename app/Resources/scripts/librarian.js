/**
 * @param $rootScope
 * @param $location
 * @param auth
 * @param users
 * @param routes
 */
var Librarian = function ($rootScope, $location, auth, users, routes) {
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
                    users.getByUserName(user.login).then(
                        function (user) {
                            $rootScope.user = user;
                        },
                        function () {
                            $location.path(routes.login);
                        }
                    );
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
