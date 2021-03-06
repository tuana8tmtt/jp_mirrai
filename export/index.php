<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/offline-exporting.js"></script>

<div id="buttonrow">
  <button id="export-png">Export to PNG</button>
  <button id="export-pdf">Export to PDF</button>
</div>
<div id="a" class="a"></div>
<div id="b" class="b"></div>

<div id="buttonrow">
  <button id="export-png">Export to PNG</button>
  <button id="export-pdf">Export to PDF</button>
</div>
<script>
    /**
 * Create a global getSVG method that takes an array of charts as an argument. The SVG is returned as an argument in the callback.
 */
Highcharts.getSVG = function(charts, options, callback) {
  
    exportChart = function(i) {
      
      charts[i].getSVGForLocalExport(options, {}, function(e) {
        console.log("Failed to get SVG");
      }, function(svg) {
        addSVG(svg);
        return exportChart(i + 1); // Export next only when this SVG is received
      });
    };
    
  exportChart(0);
};

/**
 * Create a global exportCharts method that takes an array of charts as an argument,
 * and exporting options as the second argument
 */
Highcharts.exportCharts = function(charts, options) {
  options = Highcharts.merge(Highcharts.getOptions().exporting, options);

  // Get SVG asynchronously and then download the resulting SVG
  Highcharts.getSVG(charts, options, function(svg) {
    Highcharts.downloadSVGLocal(svg, options, function() {
      console.log("Failed to export on client side");
    });
  });
};

// Set global default options for all charts
Highcharts.setOptions({
  exporting: {
    fallbackToExportServer: false // Ensure the export happens on the client side or not at all
  }
});

// Create the charts
var chart1 = Highcharts.chart('a', {

  chart: {
    height: 200,
    width: 300,
    type: 'pie'
  },

  title: {
    text: 'First Chart'
  },

  credits: {
    enabled: false
  },

  series: [{
    data: [
      ['Apples', 5],
      ['Pears', 9],
      ['Oranges', 2]
    ]
  }],

  exporting: {
    enabled: false // hide button
  }

});
var chart2 = Highcharts.chart('b', {

  chart: {
    type: 'column',
    height: 200,
    width: 300
  },

  title: {
    text: 'Second Chart'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ]
  },

  series: [{
    data: [176.0, 135.6, 148.5, 216.4, 194.1, 95.6,
      54.4, 29.9, 71.5, 106.4, 129.2, 144.0
    ],
    colorByPoint: true,
    showInLegend: false
  }],

  exporting: {
    enabled: false // hide button
  }
});

// function that convert image from url to blob
function toDataURL(url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.onload = function() {
    var reader = new FileReader();
    reader.onloadend = function() {
      callback(reader.result);
    }
    reader.readAsDataURL(xhr.response);
  };
  xhr.open('GET', url);
  xhr.responseType = 'blob';
  xhr.send();
}


var svgImg = document.createElementNS('http://www.w3.org/2000/svg','svg');
svgImg.setAttribute('xmlns:xlink','http://www.w3.org/1999/xlink');
svgImg.setAttribute('height','400');
svgImg.setAttribute('width','600');
svgImg.setAttribute('id','test');

var svgimg = document.createElementNS('http://www.w3.org/2000/svg','image');
svgimg.setAttribute('height','400');
svgimg.setAttribute('width','600');
svgimg.setAttribute('id','testimg');

// convert image and add to svg image object
toDataURL('https://www.highcharts.com/samples/graphics/skies.jpg', function(dataUrl) {
  svgimg.setAttributeNS('http://www.w3.org/1999/xlink', 'href', dataUrl);
});

svgimg.setAttribute('x','0');
svgimg.setAttribute('y','0');
svgImg.appendChild(svgimg);

// add svg with image to DOM
document.querySelector('#container3').appendChild(svgImg);


$('#export-png').click(function() {
  Highcharts.exportCharts([chart1, chart2]);
});

$('#export-pdf').click(function() {
  Highcharts.exportCharts([chart1, chart2], {
    type: 'application/pdf'
  });
});

</script>