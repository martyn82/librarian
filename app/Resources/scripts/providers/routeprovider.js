/**
 * @param $routeProvider
 * @param routes
 * @param templatePath
 */
var RouteProvider = function ($routeProvider, routes, templatePath) {
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
};
