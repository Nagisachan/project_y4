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

function init() {
    locations = [];
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

init();