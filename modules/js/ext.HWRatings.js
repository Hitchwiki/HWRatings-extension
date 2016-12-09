$(function() {

  var getRatingLabel = function (rating) {
    if (rating >= 4.5) {
      return 'Very good';
    }
    else if (rating >= 3.5) {
      return 'Good';
    }
    else if (rating >= 2.5 ) {
      return 'Average';
    }
    else if (rating >= 1.5) {
      return 'Bad';
    }
    else if (rating >= 1) {
      return 'Senseless';
    }
    else {
      return 'Unknown';
    }
  };

  $.get( mw.util.wikiScript('api') + '/?action=hwgetcountryratings&format=json', function(data) {
    var values = {};
    if (data.query) {
      var spots = data.query.spots;
      for (var i = 0; i < spots.length; i++) {
        values[spots[i].title.replace(/_/g, ' ')] = spots[i].average_rating;
      }
    }

    $('#hw-ratings-map').vectorMap({
      map: 'world-hitchwiki-custom',
      series: {
        regions: [{
          values: values,
          /**
           * Colors:
           * verygood:   #165E19;
           * good:       #547A2F;
           * average:    #A09800;
           * bad:        #E57800;
           * senseless:  #BC2C00;
           * unknown:    #7F7F7F;
           * sea:        #9dd1d3
           * unknown:    #657778
           */
          scale: ['#BC2C00', '#E57800', '#A09800', '#547A2F', '#165E19'],
          min: 1,
          max: 5,
          normalizeFunction: 'polynomial'
        }]
      },
      regionStyle:{
        initial: {
          fill: '#657778'
        }
      },
      backgroundColor: '#9dd1d3',
      onRegionTipShow: function(e, el, name){
        var rounded, label;
        if (values[name]) {
          var label = getRatingLabel(values[name]);
          var rounded = Math.round(values[name]*100)/100;
          el.html('Average Rating of ' + name + '<br />' + label + ' (' + rounded + ')');
        } else {
          el.html('No ratings for ' + name);
        }
      }
    });

  });
});
