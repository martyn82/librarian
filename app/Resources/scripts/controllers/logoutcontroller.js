/**
 * @param $rootScope
 * @param auth
 */
var LogOutController = function ($rootScope, auth) {
    auth.revokeAccess();
    delete $rootScope.user;
    $rootScope.$emit('userSignedOut');
};
