// SPDX-License-Identifier: BSD-3-Clause

import './bootstrap.js';
import './css/lightbox.css'
import './css/style.css'

import 'script-loader!lightbox2/dist/js/lightbox.min'
import 'script-loader!jquery-slimscroll/jquery.slimscroll.min'

import '@yoobic/jpeg-camera-es6/lib/swfobject.min'
import 'script-loader!@yoobic/jpeg-camera-es6/lib/canvas-to-blob.min'

import JpegCamera from '@yoobic/jpeg-camera-es6'

// Initialized at the end
let camera;
const photoInput = $('.client_photo_file')[0];

const update_stream_stats = function (stats) {
    $("#stream_stats").html(
        "Mean luminance = " + stats.mean +
        "; Standard Deviation = " + stats.std);

    setTimeout(function () {
        camera.getStats(update_stream_stats);
    }, 1000);
};

const take_snapshots = function (count) {
    const snapshot = camera.capture();

    if (camera.canvasSupported()) {
        snapshot.getCanvas(add_snapshot);
    } else {
        // <canvas> is not supported in this browser.
        // We'll use anonymous graphic instead.
        let image = document.createElement("img");
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

const add_snapshot = function (element) {
    $(element).data("snapshot", this).addClass("item");

    const $camera = $("#camera");
    const $container = $("#snapshots").append(element);
    const camera_ratio = $camera.innerWidth() / $camera.innerHeight();

    const height = $container.height()
    element.style.height = "" + height + "px";
    element.style.width = "" + Math.round(camera_ratio * height) + "px";

    const scroll = $container[0].scrollWidth - $container.innerWidth();

    $container.animate({
        scrollLeft: scroll
    }, 200);

    $(".item").removeClass("selected");
    photoInput.files = (new DataTransfer()).files;

    const snapshot = $(element).addClass("selected").data("snapshot");
    $("#discard_snapshot, #upload_snapshot, #api_url").show();
    snapshot.show();
    $("#show_stream").show();
    snapshot.getCanvas(canvas => {
        canvas.toBlob(blob => {
            const dt = new DataTransfer()
            dt.items.add(new File([blob], "photo.png", {type: "image/png"}))
            photoInput.files = dt.files
        })
    });
};

const select_snapshot = function () {
    $(".item").removeClass("selected");
    photoInput.files = (new DataTransfer()).files;

    const snapshot = $(this).addClass("selected").data("snapshot");
    $("#discard_snapshot, #upload_snapshot, #api_url").show();
    snapshot.show();
    $("#show_stream").show();
    snapshot.getCanvas(canvas => {
        canvas.toBlob(blob => {
            const dt = new DataTransfer()
            dt.items.add(new File([blob], "photo.png", {type: "image/png"}))
            photoInput.files = dt.files
        })
    });
};

const clear_upload_data = function () {
    $("#upload_status, #upload_result").html("");
};

const upload_snapshot = function () {
    const api_url = $("#api_url").val();
    if (!api_url.length) {
        $("#upload_status").html("Please provide URL for the upload");
        return;
    }

    clear_upload_data();
    $("#loader").show();
    $("#upload_snapshot").prop("disabled", true);

    const snapshot = $(".item.selected").data("snapshot");
    snapshot.upload({api_url: api_url}).done(upload_done).fail(upload_fail);
};

const upload_done = function (response) {
    $("#upload_snapshot").prop("disabled", false);
    $("#loader").hide();
    $("#upload_status").html("Upload successful");
    $("#upload_result").html(response);
};

const upload_fail = function (code, error, response) {
    $("#upload_snapshot").prop("disabled", false);
    $("#loader").hide();
    $("#upload_status").html(
        "Upload failed with status " + code + " (" + error + ")");
    $("#upload_result").html(response);
};

const discard_snapshot = function () {
    const element = $(".item.selected").removeClass("item selected");

    let next = element.nextAll(".item").first();

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

const show_stream = function () {
    $(this).hide();
    $(".item").removeClass("selected");
    photoInput.files = (new DataTransfer()).files;
    hide_snapshot_controls();
    clear_upload_data();
    camera.showStream();
};

const hide_snapshot_controls = function () {
    $("#discard_snapshot, #upload_snapshot, #api_url").hide();
    $("#upload_result, #upload_status").html("");
    $("#show_stream").hide();
};

const jpegCameraInit = function () {
    const $takeSnapshots = $("#take_snapshots");
    const $snapshots = $("#snapshots");
    const $uploadSnapshot = $("#upload_snapshot");
    const $discardSnapshot = $("#discard_snapshot");
    const $showStream = $("#show_stream");

    $takeSnapshots.off();
    $snapshots.off();
    $uploadSnapshot.off();
    $discardSnapshot.off();
    $showStream.off();
    $('#snapshots .item').remove();
    $('#snapshots_container').hide();
    $showStream.hide();

    $takeSnapshots.click(function (e) {
        e.stopPropagation();
        e.preventDefault();
        $('#snapshots_container').show();
        take_snapshots(1);
        return false;
    });

    $snapshots.on("click", ".item", select_snapshot);

    $uploadSnapshot.click(upload_snapshot);
    $discardSnapshot.click(discard_snapshot);

    $showStream.click(function (e) {
        e.stopPropagation();
        e.preventDefault();
        show_stream();
        return false;
    });

    JpegCamera(document.getElementById('camera'), {
        mirror: true,
        onInit: (webcam) => {
            camera = webcam;
        },
        onReady: (info) => {
            $takeSnapshots.show();
            $("#camera_info").html(`Camera resolution: ${info.videoWidth}x${info.videoHeight}`);
            camera.getStats(update_stream_stats);
        },
    });
};

$(function () {
    $("#do-file-photo").click(function (e) {
        $('.client_photo_file').val('').css('visibility', 'visible');
        $('#user-webcam-photo').hide();
        $('#user-file-photo').show();
        return false;
    });

    $("#do-webcam-photo").click(function (e) {
        $('.client_photo_file').val('').css('visibility', 'hidden');
        $('#user-file-photo').hide();
        $('#user-webcam-photo').show(jpegCameraInit);
        return false;
    });

    const btnAddDropDown = $('.fa-plus-circle').parent('.sonata-action-element');
    $(btnAddDropDown).css('font-weight', '600');
    $(btnAddDropDown).css('color', '#00a65a');
});
