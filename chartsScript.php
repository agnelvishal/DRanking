<div id="chart-container" style="height: 600px; min-width: 310px; max-width: 900px;"></div>

<script>
Highcharts.chart('chart-container', {

   chart: {
     type: 'bubble',
     plotBorderWidth: 1,
     zoomType: 'xy'
   },

   legend: {
     enabled: false
   },

   title: {
     text: 'Click the bubble to view article. Bigger the bubble, more the popular'
   },

   subtitle: {
     text: '   '
   },

   xAxis: {
     gridLineWidth: 1,
type:'datetime',
     title: {
       text: "Date"
     }
     },

   yAxis: {
     startOnTick: false,
     endOnTick: false,
     title: {
       text: 'Popularity'
     },
     labels: {
       format: '{value}'
     },
     maxPadding: .2,

   },
   zAxis: {
     startOnTick: false,
     endOnTick: false,
     title: {
       text: 'z axis'
     },
     labels: {
       format: '{value}'
     },
     maxPadding: .2,

   },
   tooltip: {
     useHTML: true,
     headerFormat: '<table>',
     pointFormat: '<tr>{point.heading}</tr>' +
       '<tr><th>Date:</th><td><?php if($_GET["x"]=="date") echo "{point.x:%d.%m.%Y}";else echo "{point.x}";?></td></tr>' +
       '<tr><th>Y axis :</th><td>{point.y}</td></tr>' +
       '<tr><th>Z axis :</th><td>{point.z}</td></tr>',
     footerFormat: '</table>',
     followPointer: true
   },

   plotOptions: {
     series: {
       cursor: 'pointer',
       point: {
         events: {
           click: function() {
          //   location.href = this.url;
          window.open(this.url, '_blank');

           }
         }
       },
       dataLabels: {
         enabled: false,
         format: '{point.name}'
       }
     }
   },
   series: [{
     data: [

 
