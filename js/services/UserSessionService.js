'use strict';

myApp.factory('UserSessionService', function($http) {
    return {
        setUname: function(value) { //email
            return sessionStorage.setItem('email',value);
        },
        getUname:function() { //email
            return sessionStorage.getItem('email');
        },
        destroyUname:function() { //email
            return sessionStorage.removeItem('email');
        },
        setUidentification: function(value) { 
            return sessionStorage.setItem('userid',value);
        },
        getUidentification:function() { //userid
            return sessionStorage.getItem('userid');
        },
        destroyUidentification:function() { //userid
            return sessionStorage.removeItem('userid');
        },
        setFname: function(value) { //email
            return sessionStorage.setItem('fname',value);
        },
        getFname:function() { //email
            return sessionStorage.getItem('fname');
        },
        destroyFname:function() { //email
            return sessionStorage.removeItem('fname');
        },
        setLname: function(value) { //email
            return sessionStorage.setItem('lname',value);
        },
        getLname:function() { //email
            return sessionStorage.getItem('lname');
        },
        destroyLname:function() { //email
            return sessionStorage.removeItem('lname');
        },
        setRoleDescription: function(value) { //email
            return sessionStorage.setItem('roledescription',value);
        },
        getRoleDescription:function() { //email
            return sessionStorage.getItem('roledescription');
        },
        destroyRoleDescription:function() { //userid
            return sessionStorage.removeItem('roledescription');
        },
        setDisplayName: function(value) { //email
            return sessionStorage.setItem('displayname',value);
        },
        getDisplayName:function() { //email
            return sessionStorage.getItem('displayname');
        },
        destroyDisplayName:function() { //userid
            return sessionStorage.removeItem('displayname');
        },
        setEID: function(value) { //email
            return sessionStorage.setItem('eid',value);
        },
        getEID:function() { //email
            return sessionStorage.getItem('eid');
        },
        destroyEID:function() { //userid
            return sessionStorage.removeItem('eid');
        },
        setSupName: function(value) { //email
            return sessionStorage.setItem('supname',value);
        },
        getSupName:function() { //email
            return sessionStorage.getItem('supname');
        },
        destroySupName:function() { //userid
            return sessionStorage.removeItem('supname');
        },
        setPicture: function(value) { //email
            return sessionStorage.setItem('picture',value);
        },
        getPicture:function() { //email
            return sessionStorage.getItem('picture');
        },
        destroyPicture:function() { //userid
            return sessionStorage.removeItem('picture');
        },
        
        assignedtoorg: ''
    }
});