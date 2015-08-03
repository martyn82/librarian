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
    .service('gitHubClient', ['$http', '$cookies', 'gitHub', 'queryParser', GitHubClient])
    .service('librarianClient', ['$http', 'librarian', LibrarianClient])

    .controller('HomeController', ['$scope', 'librarianClient', HomeController])
    .controller('LoginController', ['$scope', '$location', 'gitHub', LoginController])
    .controller('LogOutController', ['$rootScope', 'gitHubClient', LogOutController])
    .controller('AuthController', ['$location', '$rootScope', 'gitHubClient', 'routes', AuthController])

    .run(['$rootScope', '$location', 'gitHubClient', 'routes', function ($rootScope, $location, gitHubClient, routes) {
        $rootScope.$on('$routeChangeStart', function (event, next) {
            if (
                !gitHubClient.authenticated()
                && next.$$route.originalPath != routes.login
                && next.$$route.originalPath != routes.authenticate
                && next.$$route.originalPath != routes.logout
            ) {
                $location.path(routes.login);
            } else if (gitHubClient.authenticated()) {
                gitHubClient.getUser().then(
                    function (user) {
                        $rootScope.user = user;
                    }
                );
            }
        });

        $rootScope.$on('userAuthenticated', function (event, user) {
            $rootScope.user = user;
            $location.path(routes.home);
        });

        $rootScope.$on('userSignedOut', function (event) {
            $location.path(routes.login);
        });
    }])
;
