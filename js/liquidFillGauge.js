// /*!
//  * @license Open source under BSD 2-clause (http://choosealicense.com/licenses/bsd-2-clause/)
//  * Copyright (c) 2015, Curtis Bratton
//  * All rights reserved.
//  */
// function liquidFillGaugeDefaultSettings(){
//     return {
//         minValue: 0,
//         maxValue: 100,
//         circleThickness: 0.05,
//         circleFillGap: 0.05,
//         circleColor: "#178BCA",
//         waveHeight: 0.05,
//         waveCount: 1,
//         waveRiseTime: 1000,
//         waveAnimateTime: 18000,
//         waveRise: true,
//         waveHeightScaling: true,
//         waveAnimate: true,
//         waveColor: "#178BCA",
//         waveOffset: 0,
//         textVertPosition: .5,
//         textSize: 1,
//         valueCountUp: true,
//         displayPercent: true,
//         textColor: "#045681",
//         waveTextColor: "#A4DBf8"
//     };
// }

// function loadLiquidFillGauge(elementId, value, config) {
//     if(config == null) config = liquidFillGaugeDefaultSettings();

//     var gauge = d3.select("#" + elementId);
//     var radius = Math.min(parseInt(gauge.style("width")), parseInt(gauge.style("height")))/2;
//     var locationX = parseInt(gauge.style("width"))/2 - radius;
//     var locationY = parseInt(gauge.style("height"))/2 - radius;
//     var fillPercent = Math.max(config.minValue, Math.min(config.maxValue, value))/config.maxValue;

//     var waveHeightScale;
//     if(config.waveHeightScaling){
//         waveHeightScale = d3.scale.linear()
//             .range([0,config.waveHeight,0])
//             .domain([0,50,100]);
//     } else {
//         waveHeightScale = d3.scale.linear()
//             .range([config.waveHeight,config.waveHeight])
//             .domain([0,100]);
//     }

//     var textPixels = (config.textSize*radius/2);
//     var textFinalValue = parseFloat(value).toFixed(2);
//     var textStartValue = config.valueCountUp?config.minValue:textFinalValue;
//     var percentText = config.displayPercent?"%":"";
//     var circleThickness = config.circleThickness * radius;
//     var circleFillGap = config.circleFillGap * radius;
//     var fillCircleMargin = circleThickness + circleFillGap;
//     var fillCircleRadius = radius - fillCircleMargin;
//     var waveHeight = fillCircleRadius*waveHeightScale(fillPercent*100);

//     var waveLength = fillCircleRadius*2/config.waveCount;
//     var waveClipCount = 1+config.waveCount;
//     var waveClipWidth = waveLength*waveClipCount;

//     var textRounder = function(value){ return Math.round(value); };
//     if(parseFloat(textFinalValue) != parseFloat(textRounder(textFinalValue))){
//         textRounder = function(value){ return parseFloat(value).toFixed(1); };
//     }
//     if(parseFloat(textFinalValue) != parseFloat(textRounder(textFinalValue))){
//         textRounder = function(value){ return parseFloat(value).toFixed(2); };
//     }

//     var data = [];
//     for(var i = 0; i <= 40*waveClipCount; i++){
//         data.push({x: i/(40*waveClipCount), y: (i/(40))});
//     }

//     var waveScaleX = d3.scale.linear().range([0,waveClipWidth]).domain([0,1]);
//     var waveScaleY = d3.scale.linear().range([0,waveHeight]).domain([0,1]);
//     var waveRiseScale = d3.scale.linear()
//         .range([(fillCircleMargin+fillCircleRadius*2+waveHeight),(fillCircleMargin-waveHeight)])
//         .domain([0,1]);
//     var waveAnimateScale = d3.scale.linear()
//         .range([0, waveClipWidth-fillCircleRadius*2])
//         .domain([0,1]);

//     var textRiseScaleY = d3.scale.linear()
//         .range([fillCircleMargin+fillCircleRadius*2,(fillCircleMargin+textPixels*0.7)])
//         .domain([0,1]);

//     var gaugeGroup = gauge.append("g")
//         .attr('transform','translate('+locationX+','+locationY+')');

//     var gaugeCircleArc = d3.svg.arc()
//         .startAngle(0)
//         .endAngle(2*Math.PI)
//         .outerRadius(radius)
//         .innerRadius(radius - circleThickness);

//     gaugeGroup.append("path")
//         .attr("d", gaugeCircleArc)
//         .style("fill", config.circleColor)
//         .attr('transform','translate('+radius+','+radius+')');

//     var text1 = gaugeGroup.append("text")
//         .text(textRounder(textStartValue) + percentText)
//         .attr("class", "liquidFillGaugeText")
//         .attr("text-anchor", "middle")
//         .attr("font-size", textPixels + "px")
//         .style("fill", config.textColor)
//         .attr('transform','translate('+radius+','+textRiseScaleY(config.textVertPosition)+')');

//     var clipArea = d3.svg.area()
//         .x(function(d) { return waveScaleX(d.x); } )
//         .y0(function(d) { return waveScaleY(Math.sin(Math.PI*2*config.waveOffset*-1 + Math.PI*2*(1-config.waveCount) + d.y*2*Math.PI));} )
//         .y1(function(d) { return (fillCircleRadius*2 + waveHeight); } );
    
//     var waveGroup = gaugeGroup.append("defs")
//         .append("clipPath")
//         .attr("id", "clipWave" + elementId);
    
//     var wave = waveGroup.append("path")
//         .datum(data)
//         .attr("d", clipArea)
//         .attr("T", 0);

//     var fillCircleGroup = gaugeGroup.append("g")
//         .attr("clip-path", "url(#clipWave" + elementId + ")");

//     fillCircleGroup.append("circle")
//         .attr("cx", radius)
//         .attr("cy", radius)
//         .attr("r", fillCircleRadius)
//         .style("fill", config.waveColor);

//     var text2 = fillCircleGroup.append("text")
//         .text(textRounder(textStartValue) + percentText)
//         .attr("class", "liquidFillGaugeText")
//         .attr("text-anchor", "middle")
//         .attr("font-size", textPixels + "px")
//         .style("fill", config.waveTextColor)
//         .attr('transform','translate('+radius+','+textRiseScaleY(config.textVertPosition)+')');

//     if(config.valueCountUp){
//         var textTween = function(){
//             var i = d3.interpolate(this.textContent, textFinalValue);
//             return function(t) { this.textContent = textRounder(i(t)) + percentText; }
//         };
//         text1.transition().duration(config.waveRiseTime).tween("text", textTween);
//         text2.transition().duration(config.waveRiseTime).tween("text", textTween);
//     }

//     var waveGroupXPosition = fillCircleMargin+fillCircleRadius*2-waveClipWidth;
//     if(config.waveAnimate){
//         var animateWave = function(){
//             wave.transition()
//                 .duration(config.waveAnimateTime)
//                 .ease("linear")
//                 .attr('transform','translate('+waveAnimateScale(1)+',0)')
//                 .each("end", function(){
//                     wave.attr('transform','translate('+waveAnimateScale(0)+',0)');
//                     animateWave();
//                 });
//         }
//         animateWave();
//     }

//     var gaugeUpdater = function(newValue){
//          // Fix: Check if newValue is valid, if not use 0
//         if(newValue === undefined || newValue === null) newValue = 0;
        
//         var fillPercent = Math.max(config.minValue, Math.min(config.maxValue, newValue))/config.maxValue;
//         var waveHeight = fillCircleRadius*waveHeightScale(fillPercent*100);
//         var waveRiseScale = d3.scale.linear()
//             .range([(fillCircleMargin+fillCircleRadius*2+waveHeight),(fillCircleMargin-waveHeight)])
//             .domain([0,1]);
//         var newHeight = waveRiseScale(fillPercent);
//         var waveScaleX = d3.scale.linear().range([0,waveClipWidth]).domain([0,1]);
//         var waveScaleY = d3.scale.linear().range([0,waveHeight]).domain([0,1]);
//         var newWavePosition = config.waveAnimate?waveAnimateScale(1):0;

//         wave.transition()
//             .duration(0)
//             .transition()
//             .duration(config.waveRiseTime*(1-waveRiseScale(fillPercent)))
//             .ease("linear")
//             .attr('d', d3.svg.area()
//                 .x(function(d) { return waveScaleX(d.x); } )
//                 .y0(function(d) { return waveScaleY(Math.sin(Math.PI*2*config.waveOffset*-1 + Math.PI*2*(1-config.waveCount) + d.y*2*Math.PI));} )
//                 .y1(function(d) { return (fillCircleRadius*2 + waveHeight); } )
//             )
//             .attr('transform','translate('+newWavePosition+','+newHeight+')')
//             .each("end", function(){
//                 if(config.waveAnimate){
//                     wave.attr('transform','translate('+waveAnimateScale(0)+','+newHeight+')');
//                     animateWave();
//                 }
//             });
            
//         var textTween = function(){
//             var i = d3.interpolate(this.textContent, parseFloat(newValue).toFixed(2));
//             return function(t) { this.textContent = textRounder(i(t)) + percentText; }
//         };
//         text1.transition().duration(config.waveRiseTime).tween("text", textTween);
//         text2.transition().duration(config.waveRiseTime).tween("text", textTween);
//     };

//     return gaugeUpdater;
// }