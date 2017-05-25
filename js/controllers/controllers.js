var clientControllers = angular.module('clientControllers', []);

clientControllers.filter('unsafe', function($sce) {
    return function(val) {
        return $sce.trustAsHtml(val);
    };
});


//assets/nav.html
clientControllers.controller('NavController', ['$scope', '$http', '$state', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'UserSessionService', function($scope, $http, $state, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, UserSessionService) {

    
  // Event handler for the logout button.    
  $scope.logMeOut = function() { 
      if(loginService.logMeOut()) 
        $state.go("login");
  };  
    
  if( loginService.islogged() == false ) {      
      $state.go('login');
  } else {
        
  }
    
}]);



//assets/topnav.html
clientControllers.controller('TopNavController', ['$scope', '$http', '$state', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'UserSessionService', function($scope, $http, $state, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, UserSessionService) {

    
  // Event handler for the logout button.    
  $scope.logMeOut = function() { 
      if(loginService.logMeOut()) 
        $state.go("login");
  };    
    
    if($state.current.name == "shares") {
        
    } 
    
    
}]);








clientControllers.controller('MyInfoController', ['$scope', '$http', '$state', 'fileUpload', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'SweetAlert', 'UserSessionService', function($scope, $http, $state, fileUpload, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, SweetAlert, UserSessionService) {
  
    if( loginService.islogged() == false ) {
        $state.go('login');
    }
    // Set variables.
    
    //Setup post headers
    var config = { 
        headers : {'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'}
    };

    $scope.displayName = UserSessionService.getDisplayName();
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get Past Timesheet
    $scope.resetPassword = function() {
        
        if($scope.newPass == $scope.newPassRe)
        {
            var vmsg = EncryptionService.hashhmac($scope.newPass); 
            var vmsgOld = EncryptionService.hashhmac($scope.oldPass); 
            var timeSheet = $.param({
                action:'reset_user_password_w_old_passw',
                uname:UserSessionService.getUname(),
                passw:vmsg,
                oldpassw:vmsgOld
            });
            $http.post('server/users.php',timeSheet,config).success(function(data,status,headers,config) {

                if(data.status == "200")
                {
                    swal({   
                        title: "Password Reset Successfuylly!",   
                        text: "",   
                        type: "success",   
                        showCancelButton: false,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "OK",   
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function(isConfirm){
                        if (isConfirm) {
                            
                        } 
                    });
                }

            }).error(function(data,status,headers,config) {
                alert("Error : " + status);
            });    
        } else {
            swal({   
                title: "Password does not match!",   
                text: "",   
                type: "success",   
                showCancelButton: false,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "OK",   
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                } 
            });
        }
        
    };
    // END: Get Past Timesheet
    ////////////////////////////////////////////////////////////////



}]);




clientControllers.controller('SupervisorHomeController', ['$scope', '$http', '$state', 'fileUpload', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'SweetAlert', 'UserSessionService', function($scope, $http, $state, fileUpload, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, SweetAlert, UserSessionService) {
  
    if( loginService.islogged() == false ) {
        $state.go('login');
    }
    // Set variables.
    
    //Setup post headers
    var config = { 
        headers : {'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'}
    };

    $scope.displayName = UserSessionService.getDisplayName();
    
    

}]);























