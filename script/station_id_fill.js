$(document).ready(function () {
    $('#station_id').blur(function () {
        var station_id = $(this).val();
        fetchStationDetails(station_id);
    });

    $('#quickForm').on('submit', function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});

function fetchStationDetails(station_id) {
    $.ajax({
        url: 'get_station_details.php',
        type: 'POST',
        data: {
            station_id: station_id
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#station_name').val(response.station_name);
                $('#station_type').val(response.station_type);
            } else {
                $('#station_name').val('');
                $('#station_type').val('');
            }
        }
    });
}

function showSuggestions(str) {
    if (str == "") {
        document.getElementById("suggestion_dropdown").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("suggestion_dropdown").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "get_suggestions.php?q=" + str, true);
        xmlhttp.send();
    }
}

function selectSuggestion(station_id) {
    document.getElementById("station_id").value = station_id;
    document.getElementById("suggestion_dropdown").innerHTML = "";
    $('#station_id').blur();
}