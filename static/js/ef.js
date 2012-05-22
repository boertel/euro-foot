var EF = {};

EF.loggedIn = function (response) {
    console.log(response);
};
EF.getLoginStatus = function () {
    FB.getLoginStatus(function (response) {
        if (response.authResponse) {
            alert("OK");
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
