$(document).ready(function () {
    const $stationId = $('#station_id');
    const $quickForm = $('#quickForm');

    $stationId.on('blur', function () {
        fetchStationDetails($(this).val());
    });

    $quickForm.on('submit', function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});

function fetchStationDetails(station_id) {
    $.post('get_station_details.php', {
        station_id
    }, function (response) {
        const {
            success,
            station_name = '',
            station_type = '',
            province = '',
        } = response;
        $('#station_name').val(station_name);
        $('#station_type').val(station_type);
        $('#province').val(province);
    }, 'json');
}

function showSuggestions(str) {
    if (str === "") {
        $("#suggestion_dropdown").empty();
        return;
    }
    $.get("get_suggestions.php", {
        q: str
    }, function (response) {
        $("#suggestion_dropdown").html(response);
    });
}

function selectSuggestion(station_id) {
    $("#station_id").val(station_id).blur();
    $("#suggestion_dropdown").empty();
}