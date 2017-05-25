'use strict';


// adminadduser.html
clientControllers.controller('AdminAddUserController', ['$scope','UtilityFunctions','$stateParams', '$http', '$state', 'UserService', 'EncryptionService', '$cookies','$sce', '$timeout', 'myconfig', function($scope, UtilityFunctions, $stateParams, $http, $state,UserService, EncryptionService, $cookies, $sce, $timeout, myconfig) {

    $scope.fname = "";
    $scope.lname = "";
    $scope.email = "";
    $scope.ddUserRoles = {"iduserroles":"","description":"","assignedtoorg":""};
    
    var errEmail = document.getElementById("errEmail");
    var errInvalidEmail = document.getElementById("errInvalidEmail");
    
    
    var config = { 
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };
    
    
    $scope.showErrLabel = function() {
        errInvalidEmail.style.display = ($scope.email.indexOf('@') < 0)? "block" : "none";
        errEmail.style.display  = ($scope.email.length <=0)? "block" : "none";
    }
    
    
    $scope.onEmailChange = function() {
        errEmail.style.display = "none";
    };
    
  
    
    $scope.sendInvite = function() {

        
        if(($scope.email.length>0) && ($scope.fname.length>0) && ($scope.lname.length>0))
        {
            if($scope.email.indexOf('@') < 0) {
                errInvalidEmail.style.display = "block";    
            } else {
                errInvalidEmail.style.display = "none";    
                            
                var appPostData = $.param({
                    email:$scope.email,
                    fname:$scope.fname,
                    lname:$scope.lname
                });

                $http.post('server/inviteuser.php',appPostData,config).success(function(data, status, headers, config) {
                    if(data.status == "400")
                    {
                        //alert(data.results);

                    } else {
                        if(data.status == "200") {
                            //alert("User added to the database. Invitation sent!");
                            swal("Invitation sent!", "User added to the database. Invitation sent!", "success") 
                            $scope.email = "";
                            $scope.fname = "";
                            $scope.lname = "";
                            
                        }
                    }
                });
            }
            
        } else {
            
            $scope.showErrLabel();    
        }
        
    };
    
    
    
    
}]);