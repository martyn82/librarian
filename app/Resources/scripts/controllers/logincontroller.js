/**
 * @param $scope
 * @param gitHub
 */
var LoginController = function ($scope, gitHub) {
    $scope.gitHubClientId = gitHub.clientId;
    $scope.gitHubScope = gitHub.scope;
};