var EF = {};

EF.loggedIn = function (response) {
    console.log(response);
    EF.user.access_token = response.authResponse.accessToken;
    FB.api("/me", function (response) {
        console.log(response);
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


EF.domain = {
    api: "/api"
};

EF.user = {};

EF.api = {};

EF.api.post = function (path, data, callback) {
    path = EF.domain.api + path + ".php?access_token=" + EF.user.access_token;

    data = data || {};

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
