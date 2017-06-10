var schools;

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
    if(schools.length == 0){
        return;
    }

    locations = [];
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
    });

    map.location({ lon: schools[0].lon, lat: schools[0].lat });
}

function renderHtml() {
    $('#school-list').html('');

    if(schools.length == 0){
        $('#school-list').append(`<div class="item"><h3>No school</h3></div>`);
        return;
    }

    schools.forEach(function(e) {
        $('#school-list').append(`
            <div class="item" onclick="clickSchool(` + e.gid + `);">
                <div class="right floated content">
                    <button class="ui teal button">
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

function getSchools(){
    $.ajax(SERVICE_URL, {
        dataType: 'json',
    })
    .done(function(data) {
        schools = data.data;
        renderMap();
        renderHtml();
    });
}

function deleteSchool(id){
    for (var i = 0; i < schools.length; i++) {
        school = schools[i];
        if (school.gid == id) {
            $('#delete-school-content').html('Delete ' + school.name + " ?");
            $('#delete-school-content').attr('school-id',id);
            break;
        }
    }

    $('.ui.basic.modal').modal({
        closable: false,
        onDeny : function(){
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

getSchools();