var EF = {};

EF.loggedIn = function (response) {
    FB.api("/me", function (response) {
        EF.api.post("/user", response, function (response) {
            console.log(response);
        });
    });
};
EF.getLoginStatus = function () {
    FB.getLoginStatus(function (response) {
        if (response.authResponse) {
            EF.loggedIn(response);
        } else {
            $("a[name=connect-facebook]").click(function () {
                EF.login();
            });
        }
    });
};

EF.login = function () {
    FB.login(function (response) {
        EF.loggedIn(response);
    }, {scope: "email, publish_actions" });
};


EF.domain = {
    api: "/api"
};

EF.api = {
};

EF.api.post = function (path, data, callback) {
    var path = EF.domain.api + path + ".php";
    $.ajax({
        url: path,
        data: data,
        type: "post",
        success: function (response) {
            callback && callback(response);
        },
        error: function () {
            callback && callback(arguments);
        }
    });
};
