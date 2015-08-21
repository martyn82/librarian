/**
 * @param $location
 * @param $rootScope
 * @param auth
 * @param routes
 * @param config
 */
var AuthController = function ($location, $rootScope, auth, routes, config) {
    var authRequestToken = $location.search().code;
    $location.search('code', null);

    var allowedCompanies = config.allowedUserCompanies;

    var isAllowed = function (user) {
        for (var i in user.organizations) {
            if (allowedCompanies.indexOf(user.organizations[i].login) > -1) {
                return true;
            }
        }

        return false;
    };

    auth.authorize(authRequestToken).then(
        function (user) {
            if (!isAllowed(user)) {
                $location.path(routes.login);
                return;
            }

            $rootScope.$emit('userSignedIn', user);
        },
        function () {
            $location.path(routes.login);
        }
    );
};
