function loadArticles(event) {

    document.querySelector("#loading").style.display = "";
    // document.querySelector("#loading").style.animation = "";
    document.querySelector("#loaded").style.display = "None";
    document.querySelector("#button").style.visibility = "hidden";

    var request = $.ajax({
        type: "GET",
        url: "db.php",

        data: $("#req").serialize(),
        dataType: "html"
    });

    $.when(request).done(function (html) {
        // document.querySelector("#loading").style.animation = "fadein 5s";
        document.querySelector("#loading").style.display = "None";
        document.querySelector("#loaded").style.display = "";

        $("#iframe-container").html(html);
    }).fail(function (response) {
        $("#iframe-container").html("<p>Failed to load articles.Blame agnelvishal@gmail.com</p>");
    });
}

$(document).ready(function () {
    $("input").bind('keyup mouseup', function () {
        loadArticles();
    });
    $("select").change(function () {
        if (document.querySelector("#site").value != "others") {
            loadArticles();
        }
    });
    $("#button").click(function () {
        loadArticles();
    });
    loadArticles();
    var num = document.querySelectorAll('input[type="selector"]');
    for (var i = 0; i < num.length; i++) { num[i].addEventListener('focus', function () { document.querySelector("#button").style.visibility = 'visible'; }); }

});

