
$(function(){
	map(lat,lon);
});
function map(lat,lon){
	mapCenter=[lat,lon];
	var map = L.map('map', {
            center: mapCenter,
            zoom: 15
        });

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);

	L.marker([lat, lon]).addTo(map)
	    .bindTooltip(place).openTooltip();
}