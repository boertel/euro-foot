var EF = {};

EF.loggedIn = function (response) {
    FB.api("/me", function (response) {
    });
};
EF.getLoginStatus = function () {
    FB.getLoginStatus(function (response) {
        if (response.authResponse) {
        } else {
            $("a[name=connect-facebook]").click(function () {
                EF.fbConnect();
            });
        }
    });
};

EF.login = function () {
    FB.login(function (response) {
        EF.loggedIn(response);
    }, {scope: "email, publish_actions" });
};
