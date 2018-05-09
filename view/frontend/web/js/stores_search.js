define( [
		'jquery',
		'stores_countries',
		'stores_search'
	],
	function ( $, country_list ) {
		
		return {
			geocoderObject: function () {
				return new google.maps.Geocoder();
			},
			address: function () {
				return $( "#store-search-term" ).val()
			},
			getCountryCode: function () {
				name = this.address();
				for ( var i = 0, len = country_list.length; i < len; i++ ) {
					if ( country_list[i].name.toUpperCase() == name.toUpperCase() ) {
						return country_list[i].code
					}
				}
			},
			autocomplete: function ( map, config ) {
				var input = document.getElementById('store-search-term');
				var autocomplete = new google.maps.places.Autocomplete( input );
				autocomplete.bindTo( 'bounds', map );
				
				var infoWindow = new google.maps.InfoWindow();
				var infoWindowContent = document.getElementById('infowindow-content');
				infoWindow.setContent(infoWindowContent);
				var marker = new google.maps.Marker({
					map: map,
					anchorPoint: new google.maps.Point(0, -29)
				});
				
				autocomplete.addListener('place_changed', function (  ) {
					infoWindow.close();
					marker.setVisible(false);
					var place = autocomplete.getPlace();
					if (!place.geometry)
						return;
					if (place.geometry.viewport)
						map.fitBounds(place.geometry.viewport);
					else {
						map.setCenter(place.geometry.location);
						map.setZoom(17);
					}
					marker.setPosition(place.geometry.location);
					marker.setVisible(true);
					
					var address = '';
					if (place.address_components) {
						address = [
							(place.address_components[0] && place.address_components[0].short_name || ''),
							(place.address_components[1] && place.address_components[1].short_name || ''),
							(place.address_components[2] && place.address_components[2].short_name || '')
						].join(' ');
					}
					
					infoWindowContent.children['place-icon'].src = place.icon;
					infoWindowContent.children['place-name'].textContent = place.name;
					infoWindowContent.children['place-address'].textContent = address;
					infoWindow.open(map, marker);
				});
			},
			search: function ( map, config ) {
				
				var geocoder     = this.geocoderObject();
				$( ".stores-results" ).empty();
				$( ".stores-results" ).append( "<span class='results-word'>Results for <span class='italic'>" + this.address() + ":</span></span><br />" );
				
				var code_country = this.getCountryCode();
				geocoder.geocode(
					{ 'address': this.address() },
					function ( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK ) {
							if ( results[0] ) {
								if ( results[0]["types"][0] == "country" ) {
									map.setZoom( 17 );
									map.setCenter( results[0].geometry.location );
									var marker = new google.maps.Marker( {
										map: map,
										position: results[0].geometry.location
									} );
									for ( i = 0; i < markers.length; i++ ) {
										if ( markers[i].global_country == code_country ) {
											if ( config.unit == "default" ) {
												var store_distance = parseFloat( distance * 0.001 ).toFixed( 2 );
												var unitOfLength   = "kilometres";
											} else if ( config.unit == "miles" ) {
												var store_distance = parseFloat( distance * 0.000621371192 ).toFixed( 2 );
												var unitOfLength   = "miles";
											}
											var contentToAppend = "<div class='results-content' data-miles='" + store_distance + "' data-marker='" + markers[i].record_id + "'><p class='results-title'>" + markers[i].global_name + "</p>";
											if ( markers[i].global_address ) {
												contentToAppend += "<p class='results-address'>" + markers[i].global_address + "</p>";
											}
											if ( markers[i].global_city ) {
												contentToAppend += "<p class='data-phone'>" + markers[i].global_city + " " + markers[i].global_postcode + "</p>";
											}
											contentToAppend += "</div>";
											$( ".stores-results" ).append( contentToAppend );
										}
									}
								}
								else {
									map.setZoom( 9 );
									map.setCenter( results[0].geometry.location );
									var marker = new google.maps.Marker( {
										map: map,
										position: results[0].geometry.location
									} );
									var circle = new google.maps.Circle( {
										map: map,
										radius: config.radius,    // value from admin settings
										fillColor: config.fillColor,
										fillOpacity: config.fillOpacity,
										strokeColor: config.strokeColor,
										strokeOpacity: config.strokeOpacity,
										strokeWeight: config.strokeWeight
									} );
									circle.bindTo( 'center', marker, 'position' );
									for ( i = 0; i < markers.length; i++ ) {
										var distance = google.maps.geometry.spherical.computeDistanceBetween( marker.position, markers[i].position );
										if ( distance < config.radius ) {
											if ( config.unit == "default" ) {
												var store_distance = parseFloat( distance * 0.001 ).toFixed( 2 );
												var unitOfLength   = "kilometres";
											} else if ( config.unit == "miles" ) {
												var store_distance = parseFloat( distance * 0.000621371192 ).toFixed( 2 );
												var unitOfLength   = "miles";
											}
											var contentToAppend = "<div class='results-content' data-miles='" + store_distance + "' data-marker='" + markers[i].record_id + "'><p class='results-title'>" + markers[i].global_name + "</p>";
											if ( markers[i].global_address ) {
												contentToAppend += "<p class='results-address'>" + markers[i].global_address + "</p>";
											}
											if ( markers[i].global_city ) {
												contentToAppend += "<p class='data-phone'>" + markers[i].global_city + " " + markers[i].global_postcode + "</p>";
											}
											contentToAppend += "<p class='data-miles'>" + store_distance + " " + unitOfLength + "</p></div>";
											$( ".stores-results" ).append( contentToAppend );
										}
									}
									var $wrapper = $( '.stores-results' );
									
									//sort the result by distance
									$wrapper.find( '.results-content' ).sort( function ( a, b ) {
										return +a.dataset.miles - +b.dataset.miles;
									} )
										.appendTo( $wrapper );
								}
							}
						}
						else {
							alert( "No stores near your location." );
						}
					}
				);
			}
			
		}
		
	}
);
