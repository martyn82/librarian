angular
    .module('librarian', ['ngRoute', 'ngCookies'])

    .config(['$locationProvider', LocationProvider])
    .config(['$routeProvider', 'routes', 'templatePath', RouteProvider])

    .service('queryParser', [QueryParser])
    .service('gitHubClient', ['$http', '$q', 'gitHub', 'queryParser', GitHubClient])
    .service('auth', ['$cookies', '$q', 'gitHubClient', Auth])
    .service('librarianClient', ['$http', '$q', 'librarian', LibrarianClient])

    .controller('HomeController', ['$scope', 'librarianClient', HomeController])
    .controller('LoginController', ['$scope', 'gitHub', LoginController])
    .controller('LogOutController', ['$rootScope', 'auth', LogOutController])
    .controller('AuthController', ['$location', '$rootScope', 'auth', 'routes', 'librarian', AuthController])

    .run(['$rootScope', '$location', 'auth', 'routes', Librarian])
;
