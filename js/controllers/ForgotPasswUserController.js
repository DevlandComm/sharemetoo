'use strict';

// forgotpassw.html
clientControllers.controller('ForgotPasswUserController', ['$scope','UtilityFunctions','$stateParams', '$http', '$state', 'UserService', 'EncryptionService', '$cookies','$sce', '$timeout', 'myconfig', function($scope, UtilityFunctions, $stateParams, $http, $state,UserService, EncryptionService, $cookies, $sce, $timeout, myconfig) {

    $scope.txtemail = $stateParams.email;
    $scope.txtPassword = "";
    $scope.txtReTypePassword = "";
    
    var config = { 
        headers : {
            'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
        }
    };
    
    
    
    // BEGIN: Send new password
    $scope.doSendNewPassword = function() {
        
        //Let's add this new user and send the invite out.
        if($scope.txtPassword == $scope.txtReTypePassword)
        {
            var vmsg = EncryptionService.hashhmac($scope.txtPassword); 
            var appPostData = $.param({
                action:'reset_user_password',
                uname:$scope.txtemail,
                passw:vmsg
            });

            $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
                if(data.status == "400")
                {
                    alert(data.results);
                } else {
                    if(data.status == "200") {
                        //alert("Password updated!");
                        swal("Password updated!", "Your password has been updated!", "success")
                        setTimeout(function(){ $state.go("login"); }, 3000);
                    }
                }
            });
        } else {
            //alert("You passwords must match. Please try again and re-type your password.");
        }
        
    }
    // END: Send new password
    
    
    
    
    
    // BEGIN: Request to reset password.
    $scope.doRequestPasswordReset = function() {
        
        var reportOutline = $.param({
            uname:$scope.txtemail
         });

         $http.post('server/resetpassword.php',reportOutline,config).success(function(data,status,headers,config) {
            
             if(data.status == "400")
             {
                 alert(data.results);
             } else {
                 
                //alert("We have received your password reset request. Please check your email momentarily for confirmation.");
                swal("Request password change sent!", "We have received your password reset request. Please check your email momentarily for confirmation.", "success") 
                setTimeout(function(){ $state.go("login"); }, 4000);
                 
                 angular.forEach($scope.reportoutlinemain, function(item){
                    // Handler code goes here ...
                 });
             }    
             
         }).error(function(data,status,headers,config) {
                alert("Error loading data: " + status);
         });
    };
    // END: Request to reset password.
    
    
    $scope.doRegisterUser = function() {
        //alert('Email: ' + $scope.email + "; Firstname: "+$scope.fname+ " Lastname: "+$scope.lname+"; Role: "+$scope.ddUserRoles.description);
        
        //Let's add this new user and send the invite out.
        if($scope.txtPassword == $scope.txtReTypePassword)
        {
            var appPostData = $.param({
                action:'register_user',
                email:$scope.txtemail,
                passw:$scope.txtPassword,
                passw2:$scope.txtReTypePassword
            });

            $http.post('users.php',appPostData,config).success(function(data, status, headers, config) {
                if(data.status == "400")
                {
                    alert(data.results);
                } else {
                    if(data.status == "200") {
                        //alert("Password updated!");
                        document.getElementById("alertMsg").textContent = "Password updated!";
                        document.getElementById("modalAlert").style.height = "180px";

                        $scope.hideShowModalAlert();
                        setTimeout(function(){ $state.go("login"); }, 4000);
                    }
                }
            });
        } else {
            //alert("You passwords must match. Please try again and re-type your password.");
            document.getElementById("alertMsg").textContent = "You passwords must match. Please try again and re-type your password.";
            document.getElementById("modalAlert").style.height = "250px";

            $scope.hideShowModalAlert();
        }
        
    };
    
    
    
    
    
    ////////////////////////////////////////////////////////////////
    //BEGIN: Toggle visibility of the alert modal dialog box
    $scope.hideShowModalAlert = function() {
        var modalAlert = document.getElementById("modalAlert");
        var modalAlertOverlay = document.getElementById("modalAlertOverlay");
        modalAlert.classList.toggle("showalert");
        modalAlertOverlay.classList.toggle("showalert");
    };
    //END: Toggle visibility of the modal dialog box
    ////////////////////////////////////////////////////////////////
    
    
}]);