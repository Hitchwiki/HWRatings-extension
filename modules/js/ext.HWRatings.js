/*/ Rating widget
@hw-color-verygood:   #165E19;
@hw-color-good:       #547A2F;
@hw-color-average:    #A09800;
@hw-color-bad:        #E57800;
@hw-color-senseless:  #BC2C00;
@hw-color-unknown:    #7F7F7F;
*/

$(function(){
  var apiRoot = mw.config.get("wgServer") + mw.config.get("wgScriptPath");

  var getRatingLabel = function (rating) {
    if(rating >= 4.5) {
      return "Very good";
    }
    else if(rating >= 3.5) {
      return "Good";
    }
    else if(rating >= 2.5 ) {
      return "Average";
    }
    else if(rating >= 1.5) {
      return "Bad";
    }
    else if(rating >= 1) {
      return "Senseless";
    }
    else {
      return "Unknown";
    }
  };

  $.get( apiRoot + "/api.php?action=hwgetcountryratings&format=json", function( data ) {
    var values = {};
    if(data.query){
      var spots = data.query.spots;
      for(var i = 0; i < spots.length; i++) {
        values[spots[i].title.replace(/_/g, " ")] = spots[i].average_rating;
      }
    }

    $('#hw-ratings-map').vectorMap({
      map: 'world-hitchwiki-custom',
      series: {
        regions: [{
          values: values,
          scale: ['#BC2C00', '#E57800', '#A09800', '#547A2F', '#165E19'],
          min: 1,
          max: 5,
          normalizeFunction: 'polynomial'
        }]
      },
      onRegionTipShow: function(e, el, name){
        var rounded, label;
        if(values[name]) {
          var label = getRatingLabel(values[name]);
          var rounded = Math.round(values[name]*100)/100;
          el.html('Average Rating of ' + name + '<br />' + label + ' (' + rounded + ')');
        }
        else {
          el.html('No ratings for ' + name);
        }
      }
    });

  });
});


