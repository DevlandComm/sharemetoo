'use strict';

//myApp.factory('loginService', function($http,$state,UserSessionService,EncryptionService,SweetAlert, permittingArrays, ProjectDataService) {
//myApp.factory('loginService', function($http,$state,UserSessionService,EncryptionService,SweetAlert) {
myApp.factory('loginService', function($http,$state,UserSessionService,EncryptionService,SweetAlert, permittingArrays) {
    return {
        /**************************************************
        * Login() : function. This is called when the user
        *   clicks on the login button in the login screen.
        ***************************************************/
        login: function(user) {
            
            var config = { 
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
            };
            
            
            var vmsg = EncryptionService.hashhmac(user.userPassword); 
            var applistData = $.param({
                action:'authenticate_user',
                email: user.userName,
                passw: vmsg
            });


            $http.post('server/users.php',applistData,config).success(function(data,status,headers,config) {
                
                if(data.status == "400") // If error
                {
                    SweetAlert.swal("Error!", data.results, "error");
                } else {
                    
                    angular.forEach(data.results, function(item) {
                        UserSessionService.setUname(item.email);
                        UserSessionService.setUidentification(item.userid); 
                        UserSessionService.setPicture(item.imgurl);
                    });

                    if(UserSessionService.getUname() != undefined ) {
                        var applistData = $.param({action:'user_assignment',email:UserSessionService.getUname()}); 
                        
                        $http.post('server/users.php',applistData,config).success(function(data,status,headers,config) {
                            if(data.status == "400")
                            {
                                SweetAlert.swal("Error!", data.results, "error");
                                
                            } else {

                                angular.forEach(data.results, function(item) {
                                    UserSessionService.setRoleDescription(item.description);
                                    UserSessionService.setFname(item.firstname);
                                    UserSessionService.setLname(item.lastname);
                                    UserSessionService.setDisplayName(item.displayas);
                                    UserSessionService.setPicture(item.imgurl);
                                });
                                
                                
                                $state.go("shares");
                            }

                        }).error(function(data,status,headers,config) {
                            SweetAlert.swal("Error!", status, "error");
                        });
                    }
                }
                
            }).error(function(data,status,headers,config) {
                SweetAlert.swal("Error!", status, "error");
            });
            
        },
        
        /**************************************************
        * logMeOut() : function is called when the logout button 
        *   is clicked.
        ***************************************************/
        logMeOut: function() {
            
            UserSessionService.destroyUname();
            UserSessionService.destroyUidentification();
            UserSessionService.destroyRoleDescription();
            UserSessionService.destroyFname();
            UserSessionService.destroyLname();
            UserSessionService.destroyDisplayName();
            UserSessionService.destroyPicture();
            
            
            if(UserSessionService.getUname() == undefined)
                return true;
            else
                return false;
            
        },
        islogged: function() {
            if(UserSessionService.getUname() != undefined) {
                return true;
            } else {
                return false;
            }
        }
    }
});