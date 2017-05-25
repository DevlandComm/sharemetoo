'use strict';


// registernew.html
clientControllers.controller('RegisterUserController', ['$scope','UtilityFunctions','$stateParams', '$http', '$state', 'UserService', 'EncryptionService', '$cookies','$sce', '$timeout', 'myconfig', function($scope, UtilityFunctions, $stateParams, $http, $state,UserService, EncryptionService, $cookies, $sce, $timeout, myconfig) {

    $scope.txtemail = $stateParams.email;
    $scope.txtPassword = "";
    $scope.txtReTypePassword = "";
    
    var config = { 
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };
    
    
    
    $scope.doRegisterUser = function() {
        //alert('Email: ' + $scope.email + "; Firstname: "+$scope.fname+ " Lastname: "+$scope.lname+"; Role: "+$scope.ddUserRoles.description);
        
        //Let's add this new user and send the invite out.
        if($scope.txtPassword == $scope.txtReTypePassword)
        {
            var vmsg = EncryptionService.hashhmac($scope.txtPassword); 
            var appPostData = $.param({
                action:'register_user',
                email:$scope.txtemail,
                passw:vmsg
            });

            $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
                if(data.status == "400")
                {
                    alert(data.results);
                } else {
                    if(data.status == "200") {
                        //alert("Password updated!");
                        //document.getElementById("alertMsg").textContent = "Password updated!";
                        //document.getElementById("modalAlert").style.height = "180px";

                        //$scope.hideShowModalAlert();
                        
                        setTimeout(function(){ $state.go("login"); }, 3000);
                    }
                }
            });
        } else {
            //alert("You passwords must match. Please try again and re-type your password.");
            //document.getElementById("alertMsg").textContent = "You passwords must match. Please try again and re-type your password.";
            //document.getElementById("modalAlert").style.height = "250px";

            //$scope.hideShowModalAlert();
        }
        
    };
    
    
    ////////////////////////////////////////////////////////////////
    //BEGIN: Toggle visibility of the alert modal dialog box
//    $scope.hideShowModalAlert = function() {
//        var modalAlert = document.getElementById("modalAlert");
//        var modalAlertOverlay = document.getElementById("modalAlertOverlay");
//        modalAlert.classList.toggle("showalert");
//        modalAlertOverlay.classList.toggle("showalert");
//    };
    //END: Toggle visibility of the modal dialog box
    ////////////////////////////////////////////////////////////////
    
}]);