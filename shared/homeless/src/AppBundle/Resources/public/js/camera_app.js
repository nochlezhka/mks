// Initialized at the end
var camera;

var update_stream_stats = function (stats) {
    $("#stream_stats").html(
        "Mean luminance = " + stats.mean +
        "; Standard Deviation = " + stats.std);

    setTimeout(function () {
        camera.get_stats(update_stream_stats);
    }, 1000);
};

var take_snapshots = function (count) {
    var snapshot = camera.capture();

    if (JpegCamera.canvas_supported()) {
        snapshot.get_canvas(add_snapshot);
    }
    else {
        // <canvas> is not supported in this browser.
        // We'll use anonymous graphic instead.
        var image = document.createElement("img");
        image.src = "no_canvas_photo.jpg";
        setTimeout(function () {
            add_snapshot.call(snapshot, image)
        }, 1);
    }

    if (count > 1) {
        setTimeout(function () {
            take_snapshots(count - 1);
        }, 500);
    }
};

var add_snapshot = function (element) {
    $(element).data("snapshot", this).addClass("item");

    var $container = $("#snapshots").append(element);
    var $camera = $("#camera");
    var camera_ratio = $camera.innerWidth() / $camera.innerHeight();

    var height = $container.height()
    element.style.height = "" + height + "px";
    element.style.width = "" + Math.round(camera_ratio * height) + "px";

    var scroll = $container[0].scrollWidth - $container.innerWidth();

    $container.animate({
        scrollLeft: scroll
    }, 200);

    $(".item").removeClass("selected");
    $('.client_photo_input').val('');
    var snapshot = $(element).addClass("selected").data("snapshot");
    $("#discard_snapshot, #upload_snapshot, #api_url").show();
    snapshot.show();
    $("#show_stream").show();
    snapshot.get_canvas(function (a) {
        $('.client_photo_input').val(a.toDataURL());
    });
};

var select_snapshot = function () {
    $(".item").removeClass("selected");
    $('.client_photo_input').val('');
    var snapshot = $(this).addClass("selected").data("snapshot");
    $("#discard_snapshot, #upload_snapshot, #api_url").show();
    snapshot.show();
    $("#show_stream").show();
    snapshot.get_canvas(function (a) {
        $('.client_photo_input').val(a.toDataURL());
    });
};

var clear_upload_data = function () {
    $("#upload_status, #upload_result").html("");
};

var upload_snapshot = function () {
    var api_url = $("#api_url").val();

    if (!api_url.length) {
        $("#upload_status").html("Please provide URL for the upload");
        return;
    }

    clear_upload_data();
    $("#loader").show();
    $("#upload_snapshot").prop("disabled", true);

    var snapshot = $(".item.selected").data("snapshot");
    snapshot.upload({api_url: api_url}).done(upload_done).fail(upload_fail);
};

var upload_done = function (response) {
    $("#upload_snapshot").prop("disabled", false);
    $("#loader").hide();
    $("#upload_status").html("Upload successful");
    $("#upload_result").html(response);
};

var upload_fail = function (code, error, response) {
    $("#upload_snapshot").prop("disabled", false);
    $("#loader").hide();
    $("#upload_status").html(
        "Upload failed with status " + code + " (" + error + ")");
    $("#upload_result").html(response);
};

var discard_snapshot = function () {
    var element = $(".item.selected").removeClass("item selected");

    var next = element.nextAll(".item").first();

    if (!next.size()) {
        next = element.prevAll(".item").first();
    }

    if (next.size()) {
        next.addClass("selected");
        next.data("snapshot").show();
    }
    else {
        hide_snapshot_controls();
    }

    element.data("snapshot").discard();

    element.hide("slow", function () {
        $(this).remove()
    });
};

var show_stream = function () {
    $(this).hide();
    $(".item").removeClass("selected");
    $('.client_photo_input').val('');
    hide_snapshot_controls();
    clear_upload_data();
    camera.show_stream();
};

var hide_snapshot_controls = function () {
    $("#discard_snapshot, #upload_snapshot, #api_url").hide();
    $("#upload_result, #upload_status").html("");
    $("#show_stream").hide();
};

var jpegCameraInit = function () {
    $("#take_snapshots").off();
    $("#snapshots").off();
    $("#upload_snapshot").off();
    $("#discard_snapshot").off();
    $("#show_stream").off();
    $('#snapshots .item').remove();
    $('#snapshots_container').hide();
    $("#show_stream").hide();

    if (window.JpegCamera) {
        $("#take_snapshots").click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $('#snapshots_container').show();
            take_snapshots(1);
            return false;
        });

        $("#snapshots").on("click", ".item", select_snapshot);

        $("#upload_snapshot").click(upload_snapshot);
        $("#discard_snapshot").click(discard_snapshot);

        $("#show_stream").click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            show_stream();
            return false;
        });

        camera = new JpegCamera("#camera", {
            mirror: true
        }).ready(function (info) {
            $("#take_snapshots").show();

            $("#camera_info").html(
                "Camera resolution: " + info.video_width + "x" + info.video_height);

            this.get_stats(update_stream_stats);
        });
    }
};

$(function () {
    $("#do-file-photo").click(function (e) {
        $('.client_photo_input').val('').prop('type', 'file').css('visibility', 'visible').val('');
        $('#user-webcam-photo').hide();
        $('#user-file-photo').show();
        return false;
    });

    $("#do-webcam-photo").click(function (e) {
        $('.client_photo_input').val('').prop('type', 'text').css('visibility', 'hidden').val('');
        $('#user-file-photo').hide();
        $('#user-webcam-photo').show(function () {
            jpegCameraInit();
        });
        return false;
    });

    var btnAddDropDown = $('.fa-plus-circle').parent('.sonata-action-element');
    $(btnAddDropDown).css('font-weight', '600');
    $(btnAddDropDown).css('color', '#00a65a');
});
