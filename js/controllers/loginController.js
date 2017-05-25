'use strict';

clientControllers.controller('LoginController', ['$scope', '$http', '$state', 'UserSessionService', 'EncryptionService','$cookies', 'loginService', function($scope, $http, $state, UserSessionService, EncryptionService, $cookies, loginService) {
    
    var config = { 
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };
   
    $scope.login = function(user) {
        // Call login service 
        loginService.login(user);
    }
    
    
    
    
}]);