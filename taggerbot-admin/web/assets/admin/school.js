var schools;
var editMap;

map = new longdo.Map({
    placeholder: document.getElementById('map'),
});

function clickSchool(id) {
    for (var i = 0; i < schools.length; i++) {
        school = schools[i];
        if (school.gid == id) {
            map.location({ lat: school.lat, lon: school.lon });
            if (map.zoom() < 12) {
                map.zoom(12);
            }

            return;
        }
    }
}

function renderMap() {
    if (schools.length == 0) {
        return;
    }

    locations = [];
    var minLat = 999,
        minLon = 999,
        maxLat = -999,
        maxLon = -999;

    map.Overlays.clear();
    schools.forEach(function(e) {
        var pin = new longdo.Marker({ lon: e.lon, lat: e.lat }, {
            title: e.name,
            icon: {
                url: SCHOOL_PIN,
                offset: { x: 16, y: 32 }
            },
            detail: e.location,
        });

        map.Overlays.add(pin);
        locations.push({ lon: e.lon, lat: e.lat });

        maxLon = Math.max(e.lon, maxLon);
        maxLat = Math.max(e.lat, maxLat);
        minLon = Math.min(e.lon, minLon);
        minLat = Math.min(e.lat, minLat);
    });

    map.bound({
        minLon: minLon,
        minLat: minLat,
        maxLat: maxLat,
        maxLon: maxLon,
    });
}

function renderHtml() {
    $('#school-list').html('');

    if (schools.length == 0) {
        $('#school-list').append(`<div class="item"><h3>No school</h3></div>`);
        return;
    }

    schools.forEach(function(e) {
        $('#school-list').append(`
            <div class="item" onclick="clickSchool(` + e.gid + `);">
                <div class="right floated content">
                    <button class="ui teal button" onclick="editSchool(` + e.gid + `);">
                        <i class="edit icon"></i>
                    </button>
                    <button class="ui red button" onclick="deleteSchool(` + e.gid + `)">
                        <i class="remove icon"></i>
                    </button>
                </div>
                <div class="header">` + e.name + `</div>
                ` + e.location + `
            </div>
        `);
    });
}

function getSchools() {
    $('#add-school').addClass('loading');
    $.ajax(SERVICE_URL, {
            dataType: 'json',
        })
        .done(function(data) {
            schools = data.data;
            renderMap();
            renderHtml();
            $('#add-school').removeClass('loading');
        });
}

function deleteSchool(id) {
    for (var i = 0; i < schools.length; i++) {
        school = schools[i];
        if (school.gid == id) {
            $('#delete-school-content').html('Delete ' + school.name + " ?");
            $('#delete-school-content').attr('school-id', id);
            break;
        }
    }

    $('#delete-school-modal').modal({
        closable: false,
        onDeny: function() {
            return true;
        },
        onApprove: function() {
            var schoolId = $('#delete-school-content').attr('school-id');
            $.ajax(SERVICE_DELETE_URL.replace('SCHOOLID', schoolId), {
                    dataType: 'json',
                })
                .done(function(data) {
                    getSchools();
                });
        }
    }).modal('show');
}

function editSchool(id) {
    let school = false;
    for (var i = 0; i < schools.length; i++) {
        school = schools[i];
        if (school.gid == id) {
            break;
        }
    }

    $('#edit-school-modal').modal({
        onApprove: function() {
            var id = $('#input-school-id').attr('school-id');
            var name = $('#input-school-name').val();
            var tel = $('#input-school-tel').val();
            var website = $('#input-school-website').val();
            var loc = $('#input-school-location').val();
            var detail = $('#input-school-detail').val();

            $.ajax(SERVICE_UPDATE_URL.replace('SCHOOLID', id), {
                    dataType: 'json',
                    method: 'post',
                    data: {
                        name: name,
                        tel: tel,
                        website: website,
                        location: loc,
                        information: detail,
                        lat: editMap.location().lat,
                        lon: editMap.location().lon,
                    }
                })
                .done(function(data) {
                    getSchools();
                });
        }
    }).modal('show');

    editMap = new longdo.Map({
        placeholder: document.getElementById('edit-map'),
    });

    editMap.location({ lat: school.lat, lon: school.lon })
    editMap.zoom(18);

    // var schoolPin = new longdo.Marker({ lon: school.lon, lat: school.lat }, {
    //     title: school.name,
    //     icon: {
    //         url: SCHOOL_PIN,
    //         offset: { x: 16, y: 32 }
    //     },
    //     detail: school.location,
    // });

    // editMap.Overlays.add(schoolPin);
    // editMap.location(schoolPin.location());

    $('#input-school-id span').html('Update');
    $('#edit-school-name').html(school.name);
    $('#input-school-id').attr('school-id', school.gid);
    $('#input-school-name').val(school.name);
    $('#input-school-tel').val(school.tel);
    $('#input-school-website').val(school.website);
    $('#input-school-location').val(school.location);
    $('#input-school-detail').val(school.information);
}

function addSchool() {
    $('#input-school-id span').html('Add');
    $('#edit-school-name').html('New school');
    $('#input-school-id').attr('school-id', '');
    $('#input-school-name').val('');
    $('#input-school-tel').val('');
    $('#input-school-website').val('');
    $('#input-school-location').val('');
    $('#input-school-detail').val('');

    $('#edit-school-modal').modal({
        onApprove: function() {
            var name = $('#input-school-name').val();
            var tel = $('#input-school-tel').val();
            var website = $('#input-school-website').val();
            var loc = $('#input-school-location').val();
            var detail = $('#input-school-detail').val();

            $.ajax(SERVICE_ADD_URL, {
                    dataType: 'json',
                    method: 'post',
                    data: {
                        name: name,
                        tel: tel,
                        website: website,
                        location: loc,
                        information: detail,
                        lat: editMap.location().lat,
                        lon: editMap.location().lon,
                    }
                })
                .done(function(data) {
                    getSchools();
                });
        }
    }).modal('show');

    editMap = new longdo.Map({
        placeholder: document.getElementById('edit-map'),
    });
}

getSchools();