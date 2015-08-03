/**
 * @param $scope
 * @param $location
 * @param gitHub
 */
var LoginController = function ($scope, $location, gitHub) {
    $scope.gitHubClientId = gitHub.clientId;
    $scope.gitHubScope = gitHub.scope;
};