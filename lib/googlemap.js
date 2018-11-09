var defaultCenter = {lat: 3.048143, lng: 101.642842};
var defaultZoom = 11;
var geocoder;
var map;
var markers = {'pinTypeVenue':[], 'pinTypeAmbulance':[], 'pinTypeResponder':[], 'pinTypeHotel':[], 'pinTypeResponder':[], 'pinTypeSearch':[]};
var markerCluster = null;

function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
		disableDefaultUI: true,
		zoom: defaultZoom,
		center: defaultCenter,
		gestureHandling: 'greedy'
		//styles: [{"stylers": [{ "saturation": -100 }]}]
	});
	
	map.addListener("rightclick", function(event) {
		getAddressByCoordinate(event.latLng, function(address) {
			addCallcard(event.latLng.lat().toFixed(6)+','+event.latLng.lng().toFixed(6), address);
		});
	});
	
	geocoder = new google.maps.Geocoder();
	
	initSearchBox();
	initCustomOverlay();
	
	//Plotting 1CC markers
	//=======================================================
	plotVenueOnMap();
	plotAmbulanceOnMap();
	getActiveResponders('reset');
	setInterval(function(){ getActiveResponders('retain'); }, 10000);
	//=======================================================
}

function addMarker(data) {
	var marker = new google.maps.Marker({
		position: {lat: data.lat, lng: data.lng},
		map: map,
		icon: {
			url: 'img/pinTypeNull.svg',
			size: new google.maps.Size(64, 84),
			scaledSize: new google.maps.Size(64, 84),
			anchor: new google.maps.Point(32, 75)
		},
		data: data,
		customOverlay: new customOverlay(data)
	});
		
	markers[data.type].push(marker);
}

function getAddressByCoordinate(latLng, callback) {
	geocoder.geocode({
		'latLng': latLng
	}, function(results, status) {
		if(status == google.maps.GeocoderStatus.OK) {
			callback(results[0].formatted_address);
		}
		else {
			callback('Cannot detect');
		}
	});
}

function initSearchBox() {
	var input = document.getElementById('mapsearch');
	var searchBox = new google.maps.places.SearchBox(input);
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
	});

	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if(places.length==0) return;

		markers.pinTypeSearch.forEach(function(marker) {
			marker.setMap(null);
		});
		markers.pinTypeSearch = [];
		
		if(!places[0].geometry) return;
		addMarker({
			'lat': places[0].geometry.location.lat(),
			'lng': places[0].geometry.location.lng(),
			'type': 'pinTypeSearch',
			'address': places[0].name+', '+places[0].formatted_address.replace(/,,/,','),
			'icon': 'img/pinTypeSearch.svg'
		});

		map.setZoom(18);
		map.panTo(places[0].geometry.location);
	});

}

function resetMap() {
	map.setZoom(defaultZoom);
	map.panTo(defaultCenter);
	getActiveResponders('reset');
}

//================================================================================================ Overlay Start
function customOverlay(data) {
	this.data = data;
	this.setMap(map);
	this.addClass = function(c){ this.data.div.addClass(c); }
	this.removeClass = function(c){ this.data.div.removeClass(c); }
}

function initCustomOverlay() {
	customOverlay.prototype = new google.maps.OverlayView();

	customOverlay.prototype.draw = function () {
		if (!this.data.div) { //Check if the div has been created
			this.data.div = $('<div class="'+this.data.type+'">'+(this.data.name?'<div class="pinInfoName">'+this.data.name+'</div>':'')+(this.data.callcard?'<div class="pinInfoCallcard">'+this.data.callcard+'</div>':'')+'</div>');
			$(this.getPanes().overlayImage).append(this.data.div);
			
			if(this.data.type=='pinTypeVenue' || this.data.type=='pinTypeHotel' || this.data.type=='pinTypeSearch') {
				var pData = this.data;
				this.data.div.on('click', function() {
					addCallcard(pData.lat+','+pData.lng, pData.address, pData.type.replace("pinType",""));
				});
			}
		}
		
		var point = this.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(this.data.lat, this.data.lng));
		if (point) {
			this.data.div.css({
				'background': 'url('+this.data.icon+')',
				'background-size': '100% 100%',
				'top': point.y + 'px',
				'left': point.x + 'px',
				'z-index': Math.round(point.y)
			});
		}
	};
}
//================================================================================================ Overlay End