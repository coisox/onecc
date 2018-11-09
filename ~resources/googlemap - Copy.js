var allMarkers = [];
var markerCluster = null;

function CustomMarker(latlng, address, map, imageSrc, id, pinType, callcard) {
	this.latlng = latlng;
	this.address = address;
	this.imageSrc = imageSrc;
	this.callcard = callcard;
	this.setMap(map);
	this.pinType = pinType;
	if(pinType=='Venue') this.venue_id = id;
	else this.resource_nric_or_reg = id;
}

CustomMarker.prototype = new google.maps.OverlayView();

CustomMarker.prototype.draw = function () {
	// Check if the div has been created.
	var div = this.div_;
	if (!div) {
		div = this.div_ = document.createElement('div');
		div.className = "pinType"+this.pinType;
		div.setAttribute("coordinate", this.latlng.lat().toFixed(6)+','+this.latlng.lng().toFixed(6));
		if(this.pinType=='Venue') div.setAttribute("address", this.address);
		if(this.callcard!='') div.setAttribute("callcard", this.callcard);
		if(this.pinType=='Venue') div.setAttribute("pin_venue_id", this.venue_id);
		else div.setAttribute("pin_resource_nric_or_reg", this.resource_nric_or_reg);

		var img = document.createElement("img");
		img.src = this.imageSrc;
		div.appendChild(img);
		
		if(this.pinType=='Venue') {
			google.maps.event.addDomListener(div, "click", function (event) {
				if(profile.user_roles.indexOf('admin')>-1 || profile.user_roles.indexOf('gdu')>-1) addCallcard(div.getAttribute('coordinate'), div.getAttribute('address'), 'venue'); //Access Control
			});
		}

		var panes = this.getPanes();
		panes.overlayImage.appendChild(div);
	}

	// Position the overlay 
	var point = this.getProjection().fromLatLngToDivPixel(this.latlng);
	if (point) {
		div.style.left = point.x + 'px';
		div.style.top = point.y + 'px';
		div.style.zIndex = Math.round(point.y);
	}
};

CustomMarker.prototype.remove = function () {
	// Check if the overlay was on the map and needs to be removed.
	if (this.div_) {
		this.div_.parentNode.removeChild(this.div_);
		this.div_ = null;
	}
};

CustomMarker.prototype.getPosition = function () {
	return this.latlng;
};

var map = new google.maps.Map(document.getElementById("map"), {
	disableDefaultUI: true,
	mapTypeId: google.maps.MapTypeId.ROADMAP
});
	
var bounds = new google.maps.LatLngBounds();


//==================================================================== Google Map Search Box Start
// Create the search box and link it to the UI element.
var input = document.getElementById('mapsearch');
var searchBox = new google.maps.places.SearchBox(input);
map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);


// Bias the SearchBox results towards current map's viewport.
map.addListener('bounds_changed', function() {
	searchBox.setBounds(map.getBounds());
});

var search_markers = [];
searchBox.addListener('places_changed', function() {
	var places = searchBox.getPlaces();

	if (places.length == 0) return;

	// Clear out the old markers.
	search_markers.forEach(function(marker) {
		marker.setMap(null);
	});
	search_markers = [];
	
	// For each place, get the icon, name and location.
	var search_marker;
	places.forEach(function(place) {
		if (!place.geometry) {
			console.log("Returned place contains no geometry");
			return;
		}
		var icon = {
			url: place.icon,
			size: new google.maps.Size(71, 71),
			origin: new google.maps.Point(0, 0),
			anchor: new google.maps.Point(17, 34),
			scaledSize: new google.maps.Size(25, 25)
		};

		// Create a marker for each place.
		var image = {
			url: 'img/pinTypeSearch.svg',
			scaledSize: new google.maps.Size(70, 70)
		};
		search_marker = new google.maps.Marker({
			map: map,
			icon: image,
			title: place.name,
			position: place.geometry.location
		});
		search_markers.push(search_marker);
		
		google.maps.event.addDomListener(search_marker, "click", function (event) {
			if(profile.user_roles.indexOf('admin')>-1 || profile.user_roles.indexOf('gdu')>-1) addCallcard(search_marker.position.lat().toFixed(6)+','+search_marker.position.lng().toFixed(6), $('#mapsearch').val()); //Access Control
		});

		/*
		if (place.geometry.viewport) {
			// Only geocodes have viewport.
			bounds.union(place.geometry.viewport);
		} else {
			bounds.extend(place.geometry.location);
		}
		*/
	});
	
	//map.fitBounds(bounds);
	//zoom to marker
	map.setZoom(17);
	map.panTo(search_marker.getPosition());	
});

function resetMap() {
	map.fitBounds(bounds);
}
//==================================================================== Google Map Search Box End