angular
    .module('librarian', ['ngRoute', 'ngCookies'])

    .config(['$locationProvider', function ($locationProvider) {
        $locationProvider.html5Mode(true);
    }])

    .config(['$routeProvider', 'routes', 'templatePath', function ($routeProvider, routes, templatePath) {
        $routeProvider
            .when(routes.home, {
                controller: 'HomeController',
                templateUrl: templatePath + '/default/home.html'
            })
            .when(routes.login, {
                controller: 'LoginController',
                templateUrl: templatePath + '/default/login.html'
            })
            .when(routes.logout, {
                controller: 'LogOutController',
                templateUrl: templatePath + '/default/login.html'
            })
            .when(routes.authenticate, {
                controller: 'AuthController',
                templateUrl: templatePath + '/default/login.html'
            })
            .otherwise({
                redirectTo: routes.home
            });
    }])

    .service('queryParser', [QueryParser])
    .service('gitHubClient', ['$http', '$q', 'gitHub', 'queryParser', GitHubClient])
    .service('auth', ['$cookies', '$q', 'gitHubClient', Auth])
    .service('librarianClient', ['$http', '$q', 'librarian', LibrarianClient])

    .controller('HomeController', ['$scope', 'librarianClient', HomeController])
    .controller('LoginController', ['$scope', '$location', 'gitHub', LoginController])
    .controller('LogOutController', ['$rootScope', 'auth', LogOutController])
    .controller('AuthController', ['$location', '$rootScope', 'auth', 'routes', AuthController])

    .run(['$rootScope', '$location', 'auth', 'routes', function ($rootScope, $location, auth, routes) {
        $rootScope.$on('$routeChangeStart', function (event, next) {
            if (
                !auth.authorized()
                && next.$$route.originalPath != routes.login
                && next.$$route.originalPath != routes.authenticate
                && next.$$route.originalPath != routes.logout
            ) {
                $location.path(routes.login);
            } else if (auth.authorized()) {
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
    }])
;
