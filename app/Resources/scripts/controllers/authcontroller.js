/**
 * @param $location
 * @param $rootScope
 * @param gitHubClient
 * @param routes
 */
var AuthController = function ($location, $rootScope, gitHubClient, routes) {
    var authRequestToken = $location.search().code;
    $location.search('code', null);

    gitHubClient.authenticate(authRequestToken)
        .then(function (user) {
            $rootScope.$emit('userAuthenticated', user);
        }, function (error) {
            $location.path(routes.login);
        });
};