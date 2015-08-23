angular
    .module('librarian', ['ngRoute', 'ngCookies'])

    .config(['$locationProvider', LocationProvider])
    .config(['$routeProvider', 'routes', 'templatePath', RouteProvider])

    .service('queryParser', [QueryParser])
    .service('gitHubClient', ['$http', '$q', 'gitHub', 'queryParser', GitHubClient])
    .service('users', ['$http', '$q', 'usersConfig', Users])
    .service('auth', ['$cookies', '$q', 'gitHubClient', 'users', 'librarian', Auth])

    .controller('HomeController', ['$scope', HomeController])
    .controller('LoginController', ['$scope', 'gitHub', LoginController])
    .controller('LogOutController', ['$rootScope', 'auth', LogOutController])
    .controller('AuthController', ['$location', '$rootScope', 'auth', 'routes', AuthController])

    .run(['$rootScope', '$location', 'auth', 'routes', Librarian])
;
