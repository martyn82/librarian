/**
 * @param $rootScope
 * @param gitHubClient
 */
var LogOutController = function ($rootScope, gitHubClient) {
    gitHubClient.revoke();
    delete $rootScope.user;
    $rootScope.$emit('userSignedOut');
};