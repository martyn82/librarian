/**
 * @param $location
 * @param $rootScope
 * @param auth
 * @param routes
 */
var AuthController = function ($location, $rootScope, auth, routes) {
    var authRequestToken = $location.search().code;
    $location.search('code', null);

    auth.authorize(authRequestToken).then(
        function (user) {
            var companyConstraint = false;

            for (var i in user.organizations) {
                var org = user.organizations[i];

                if (org.login == 'Vnumedia') {
                    companyConstraint = true;
                    break;
                }
            }

            if (!companyConstraint) {
                $location.path(routes.login);
            }

            $rootScope.$emit('userSignedIn', user);
        },
        function () {
            $location.path(routes.login);
        }
    );
};
