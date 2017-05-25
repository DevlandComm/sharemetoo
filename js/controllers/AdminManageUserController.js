'use strict';


clientControllers.controller('AdminManageUserController', ['$scope', '$http', '$state', 'fileUpload', 'loginService', 'UtilityFunctions', 'EncryptionService', '$cookies', '$log', '$timeout', '$sce', 'myconfig', 'SweetAlert', 'UserSessionService', function($scope, $http, $state, fileUpload, loginService, UtilityFunctions, EncryptionService, $cookies, $log, $timeout, $sce, myconfig, SweetAlert, UserSessionService) {
  
    if( loginService.islogged() == false ) {
        $state.go('login');
    } else {
        document.getElementById("dreditroles").disabled  = true;
        document.getElementById("btnEditToRight").style.display = "none";
        document.getElementById("btnEditToLeft").style.display = "none";
        //Check level of access
        if((UserSessionService.getRoleDescription() == "supervisor") || (UserSessionService.getRoleDescription() == "admin") || (UserSessionService.getRoleDescription() == "superadmin")) {
            //Let's make the add user and edit user buttons only to the appropriate roles
            var spans = document.getElementsByClassName("admincontrol");
            for(var i = 0; i < spans.length; i++) {
                spans[i].style.display = "block";
            }
            document.getElementById("dreditroles").disabled  = false;
            document.getElementById("btnEditToRight").style.display = "block";
            document.getElementById("btnEditToLeft").style.display = "block";
        } else if((UserSessionService.getRoleDescription() == "readonly") || (UserSessionService.getRoleDescription() == "coordinator")) { 
            var spans = document.getElementsByClassName("admincontrol");
            for(var i = 0; i < spans.length; i++) {
                spans[i].style.display = "none";
            }
        } else {
            var spans = document.getElementsByClassName("admincontrol");
            for(var i = 0; i < spans.length; i++) {
                spans[i].style.display = "none";
            }
            $state.go('home');
        }
    }
    
    // Set variables.
    $scope.userroles = []
    $scope.users     = [];
    $scope.fname     = "";
    $scope.lname     = "";
    $scope.email     = "";
    
    $scope.selectedDivisions = [];
    $scope.selectedDivisions2 = [];
    
    
    var errEmail = document.getElementById("errEmail");
    var errEmail2 = document.getElementById("errEmail2");
    var errInvalidEmail = document.getElementById("errInvalidEmail");
    var errInvalidEmail2 = document.getElementById("errInvalidEmail2");
    
    
    $scope.showErrLabel = function() {
        var retVal = false;
        
        if($scope.email != undefined) {
            if($scope.email.length>0)
                errInvalidEmail.style.display = ($scope.email.indexOf('@') < 0)? "block" : "none";
            //errEmail.style.display  = ($scope.email.length <=0)? "block" : "none";
            retVal = true;
        }
        
        if(($scope.email.length<=0) || ($scope.fname.length<=0) || ($scope.lname.length<=0) || ($scope.drroles == undefined)) {
            errEmail.style.display  = "block";
            retVal = true;
        } else {
            errEmail.style.display  = "none";
            retVal = false;
        }
        
        return retVal;
    }
    
    
    
    $scope.showErrLabel2 = function() {
        var retVal = false;
        
        if((document.getElementById("editfname").value.length<=0) || (document.getElementById("editlname").value.length<=0) || ($scope.dreditroles == undefined)) {
            errEmail2.style.display  = "block";
            retVal = true;
        } else {
            errEmail2.style.display  = "none";
            retVal = false;
        }
        
        return retVal;
    }
    
    
    $scope.onEmailChange = function() {
        errEmail.style.display = "none";
        $scope.showErrLabel();
    };
    
    
    
    $scope.onEmailChange = function() {
        errEmail.style.display = "none";
        $scope.showErrLabel();
    };
    
    
    
    //Setup post headers
    var config = { 
        headers : {'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'}
    };

    $scope.displayName = UserSessionService.getDisplayName();
    
    
    
    
    //////////////////////////////////////////////////////////////
    // BEGIN: Initialize Modal View
    var modalresetpassword = new RModal(document.getElementById('modalresetpassword'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    
    window.modalresetpassword = modalresetpassword;
    
    
    var modaladduser = new RModal(document.getElementById('modaladduser'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    
    window.modaladduser = modaladduser;
    
    
    var modaledituser = new RModal(document.getElementById('modaledituser'), {
        beforeOpen: function(next) {
            console.log('beforeOpen');
            next();
        }
        , afterOpen: function() {
            console.log('opened');
        }

        , beforeClose: function(next) {
            console.log('beforeClose');
            next();
        }
        , afterClose: function() {
            console.log('closed');
        }
    });
    
    window.modaledituser = modaledituser;
    
    // END: Initialize Modal View
    //////////////////////////////////////////////////////////////
    
    
    
    $scope.showResetPassword = function() 
    {
        modalresetpassword.open();
    };
    
    
    $scope.showAddUser = function() {
        modaladduser.open();
    };
    
    
    
    $scope.showEditUser = function() {
        $scope.getAllUsers();
        modaledituser.open();
    };
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get All Divisions
    $scope.getAllDivisions = function() {
        
        document.getElementById("divProgress").classList.add("show");
        
        var divisions = $.param({
            action:'get_all_divisions',
            userid:UserSessionService.getUidentification()
        });
        $http.post('server/application.php',divisions,config).success(function(data,status,headers,config) {

            if(data.status == "200")
            {
                $scope.divisions = data.results;
                
            } else {
                var err = "Error : " + data.results;
                swal(err);
            }
            document.getElementById("divProgress").classList.remove("show");
        }).error(function(data,status,headers,config) {
            swal('Error : ' + status);
        });
        
    };
    // END: Get All Divisions
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Move Dvision to Right
    $scope.moveRight = function() {
        var action_list = document.getElementById("actions_list");
        var destination_list = document.getElementById("destination_list");

        // Remember selected items.
        var is_selected = [];
        var selected_items = [];
        var selected_items_ids = [];
        for (var i = 0; i < action_list.options.length; i++)
        {
            is_selected[i] = action_list.options[i].selected;
            if(action_list.options[i].selected) {
                selected_items.push(action_list.options[i].text); 
                selected_items_ids.push(action_list.options[i].value); 
            }
        }

        // Remove selected items.
        i = action_list.options.length;
        while (i--)
        {
            if (is_selected[i])
            {
                //destination_list.add(action_list.options[i]);
                action_list.remove(i);
            }
        }
        
        for (var i = 0; i < selected_items.length; i++)
        {
            var opOption = new Option(selected_items[i], selected_items_ids[i]);
            destination_list.appendChild(opOption);
        }
        
    };
    // END: Move Dvision to Right
    ////////////////////////////////////////////////////////////////
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Move Dvision to Right (Edit Mode)
    $scope.moveRightEdit = function() {
        var action_list = document.getElementById("actions_list2");
        var destination_list = document.getElementById("destination_list2");

        // Remember selected items.
        var is_selected = [];
        var selected_items = [];
        var selected_items_ids = [];
        for (var i = 0; i < action_list.options.length; i++)
        {
            is_selected[i] = action_list.options[i].selected;
            if(action_list.options[i].selected) {
                selected_items.push(action_list.options[i].text); 
                selected_items_ids.push(action_list.options[i].value); 
            }
        }

        // Remove selected items.
        i = action_list.options.length;
        while (i--)
        {
            if (is_selected[i])
            {
                //destination_list.add(action_list.options[i]);
                action_list.remove(i);
            }
        }
        
        for (var i = 0; i < selected_items.length; i++)
        {
            var opOption = new Option(selected_items[i], selected_items_ids[i]);
            destination_list.appendChild(opOption);
        }
        
    };
    // END: Move Dvision to Right (Edit Mode)
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Move Dvision to Left
    $scope.moveLeft = function() {
        var left_list = document.getElementById("actions_list");
        var right_list = document.getElementById("destination_list");

        // Remember selected items.
        var is_selected = [];
        var selected_items = [];
        for (var i = 0; i < right_list.options.length; i++)
        {
            is_selected[i] = right_list.options[i].selected;
            if(right_list.options[i].selected) 
                selected_items.push(right_list.options[i].text); 
        }

        // Remove selected items.
        i = right_list.options.length;
        while (i--)
        {
            if (is_selected[i])
            {
                //destination_list.add(action_list.options[i]);
                right_list.remove(i);
            }
        }
        
        for (var i = 0; i < selected_items.length; i++)
        {
            var opOption = new Option(selected_items[i], selected_items[i]);
            left_list.appendChild(opOption);
        }

        
    };
    // END: Move Dvision to Left
    ////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Move Dvision to Left
    $scope.moveLeftEdit = function() {
        var left_list = document.getElementById("actions_list2");
        var right_list = document.getElementById("destination_list2");

        // Remember selected items.
        var is_selected = [];
        var selected_items = [];
        for (var i = 0; i < right_list.options.length; i++)
        {
            is_selected[i] = right_list.options[i].selected;
            if(right_list.options[i].selected) 
                selected_items.push(right_list.options[i].text); 
        }

        // Remove selected items.
        i = right_list.options.length;
        while (i--)
        {
            if (is_selected[i])
            {
                //destination_list.add(action_list.options[i]);
                right_list.remove(i);
            }
        }
        
        for (var i = 0; i < selected_items.length; i++)
        {
            var opOption = new Option(selected_items[i], selected_items[i]);
            left_list.appendChild(opOption);
        }

        
    };
    // END: Move Dvision to Left
    ////////////////////////////////////////////////////////////////
    
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: User Roles
    $scope.getUserRoles = function() {
        
        
        var appPostData = $.param({
            action:'get_user_roles'
        });

        $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
            if(data.status == "400")
            {
                swal(data.results);

            } else {
                if(data.status == "200") {
                    $scope.userroles = data.results;
                }
            }
        });
        
    };
    // END: User Roles
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get All Users
    $scope.getAllUsers = function() {
        
        
        var appPostData = $.param({
            action:'get_user_info_list'
        });

        $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
            if(data.status == "400")
            {
                swal(data.results);

            } else {
                if(data.status == "200") {
                    
                    // If you're not an admin, let's just restrict it to yours
                    if((UserSessionService.getRoleDescription() == "readonly") || (UserSessionService.getRoleDescription() == "coordinator")) { 

                        angular.forEach(data.results, function(item) {
                            console.log("item.userid: "+item.userid+";  UserSessionService.getUidentification(): "+UserSessionService.getUidentification())
                            if(item.userid == UserSessionService.getUidentification())
                                $scope.users.push(item);
                        });
                        
                    } else {
                         $scope.users = data.results;
                    }
                }
            }
        });
        
    };
    // END: Get All Users
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Get All Users
    $scope.loadUserDetail = function() {
        
        var destination_list2 = document.getElementById("destination_list2");
        var action_list = document.getElementById("actions_list2");
        
        //Let's refresh the original list
        action_list.options.length = 0;
        angular.forEach($scope.divisions, function(item) {
            var opOption = new Option(item.name, item.id);
            action_list.appendChild(opOption);
        });
        
        // Let's load the selected user 
        angular.forEach($scope.users, function(item) {
            if(item.userid == $scope.selecteduser)
            {
                //document.getElementById("editfname").value = item.FirstName;
                //document.getElementById("editlname").value = item.LastName;
                $scope.editfname = item.FirstName;
                $scope.editlname = item.LastName;
                $scope.dreditroles = item.iduserroles;
            }
        });
        
        var appPostData = $.param({
            action:'get_assigned_divisions',
            userid:$scope.selecteduser
        });

        $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
            if(data.status == "400")
            {
                swal(data.results);

            } else {
                if(data.status == "200") {
                    
                    //Let's clear the list box first then load the divisions
                    destination_list2.options.length = 0;
                    
                    // Load the divisions assigned to this user
                    angular.forEach(data.results, function(item) {
                        var opOption = new Option(item.name, item.id);
                        destination_list2.appendChild(opOption);
                        
                        //Let's remove the divisions from the main list
                        for (var i = 0; i < action_list.options.length; i++) {
                            if(action_list.options[i].value == item.id) {
                                //action_list.options[i] = null;
                                action_list.remove(i);
                            }
                        }
                    });
                    
                    
                    
                }
            }
        });
        
    };
    // END: Get All Users
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Send Invite
    $scope.sendInvite = function() {

        var destination_list = document.getElementById("destination_list");
        
        if(($scope.email.length>0) && ($scope.fname.length>0) && ($scope.lname.length>0))
        {
            console.log("good");
            
            if($scope.email.indexOf('@') < 0) {
                errInvalidEmail.style.display = "block";    
            } else {
                errInvalidEmail.style.display = "none";    
                if(!$scope.showErrLabel()) {
                    // If there's no error, let's submit
                    console.log("very good!");
                    
                    //Let's select all the divisions that have been selected by the user.
                    $scope.selectedDivisions = [];
                    for(var i=0; i<destination_list.length;i++) 
                        $scope.selectedDivisions.push(destination_list.options[i].value); 
                    
                    
                    var appPostData = $.param({
                        email:$scope.email,
                        fname:$scope.fname,
                        lname:$scope.lname,
                        userroleid:$scope.drroles,
                        division:$scope.selectedDivisions
                    });
                    
                    
                    $http.post('server/inviteuser.php',appPostData,config).success(function(data, status, headers, config) {
                        if(data.status == "400")
                        {
                            swal(data.results);

                        } else {
                            if(data.status == "200") {
                                //alert("User added to the database. Invitation sent!");
                                swal("Invitation sent!", "User added to the database. Invitation sent!", "success") 
                                $scope.email = "";
                                $scope.fname = "";
                                $scope.lname = "";
                                modaladduser.close();
                            }
                        }
                    });
                    
                    
                }
                
            }
            
        } else {
            console.log("bad");
            $scope.showErrLabel();    
        }
        
    };
    // END: Send Invite
    ////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Save New Password
    $scope.saveNewPassword = function() {
        var strErr = "";
        if(document.getElementById("txtOldPassword").value.length <= 0)
        {
            strErr = strErr + "You need to provide your current password.\n\n";
        }
        
        
        if(document.getElementById("txtNewPassword").value != document.getElementById("txtConfirmPassword").value)
        {
            strErr = strErr + "Your new password does not match.\n\n";
        }
        
        
        
        if(strErr.length <= 0) {
            
            var psw1 = EncryptionService.hashhmac(document.getElementById("txtOldPassword").value); 
            var psw2 = EncryptionService.hashhmac(document.getElementById("txtNewPassword").value); 
            
            var chpassw = $.param({
                action:'reset_user_password_w_old_passw',
                uname:UserSessionService.getUname(),
                passw:psw2,
                oldpassw:psw1
            });
            $http.post('server/users.php',chpassw,config).success(function(data,status,headers,config) {

                if(data.status == "200")
                {
                    swal('Your password has been updated!');
                    modalresetpassword.close();
                    
                } else {
                    var err = "Error: " + data.results;
                    err = err + "\n\nMake sure you've entered the correct current password."
                    swal(err);
                }

            }).error(function(data,status,headers,config) {
                swal('Error : ' + status);
            });
            
        } else {
            swal(strErr);
        }
            
    };
    // END: Save New Password
    ////////////////////////////////////////////////////////////////
    
    
    
    
    ////////////////////////////////////////////////////////////////
    // BEGIN: Update User Information
    $scope.updateUser = function() {
        
        var destination_list2 = document.getElementById("destination_list2");
        
        if(($scope.selecteduser != undefined) && (document.getElementById("editfname").value.length>0) && (document.getElementById("editlname").value.length>0))
        {
            errInvalidEmail2.style.display = "none";    
            if(!$scope.showErrLabel2()) {
                // If there's no error, let's submit
                console.log("very good!");

                
                swal({   
                    title: "Update this user?",   
                    text: "Are you sure you want to update this user?",   
                    type: "warning",   
                    showCancelButton: true,   
                    confirmButtonColor: "#DD6B55",   
                    confirmButtonText: "Yes",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {
                        
                        //Let's select all the divisions that have been selected by the user.
                        $scope.selectedDivisions2 = [];
                        for(var i=0; i<destination_list2.length;i++) 
                            $scope.selectedDivisions2.push(destination_list2.options[i].value); 


                        var appPostData = $.param({
                            action:"update_user_info",
                            userid:$scope.selecteduser,
                            fname:$scope.editfname,
                            lname:$scope.editlname,
                            userroleid:$scope.dreditroles,
                            division:$scope.selectedDivisions2
                        });


                        $http.post('server/users.php',appPostData,config).success(function(data, status, headers, config) {
                            if(data.status == "400")
                            {
                                swal(data.results);

                            } else {
                                if(data.status == "200") {
                                    //alert("User added to the database. Invitation sent!");
                                    swal("User information updated!", "", "success");
                                    $scope.editfname = "";
                                    $scope.editlname = "";
                                    $scope.selecteduser = -1;
                                    $scope.dreditroles = -1;
                                    destination_list2.options.length = 0;
                                    modaledituser.close();
                                }
                            }
                        });

                    } 
                });
                
            }
            
        } else {
            console.log("bad");
            $scope.showErrLabel2();    
        }
            
    };
    // END: Update User Information
    ////////////////////////////////////////////////////////////////
    
    
    $scope.getAllDivisions();
    $scope.getUserRoles();
        
    

}]);