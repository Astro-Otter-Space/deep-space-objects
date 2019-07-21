import * as d3 from './../../node_modules/d3-celestial/lib/d3.min.js'
import geo from './../../node_modules/d3-celestial/lib/d3.geo.projection.min';
import { color } from './legendSkyMap';
import Celestial from 'd3-celestial/celestial.min';

var MAP_MODULE = (function(c, constId, color) {

  let PROXIMITY_LIMIT = 20;

  let starFile = 'stars.' + constId + '.json';
  let config = {
    width: 0,
    projection: "aitoff",
    transform: "equatorial",
    center: JSON.parse(document.getElementById("geojson").dataset.center),
    adaptable: true,
    interactive: true,
    form: false,
    location: false,
    controls: false,
    container: "map",
    datapath: "/build/data/",
    // STARS
    stars: {
      colors: true,
      names: true,
      proper: true,
      style: { fill: "#ffffff", opacity: 1 },
      namelimit: 2,
      limit: 8,
      size: 5,
      data: starFile
    },
    // DEEP SKY OBJECTS
    dsos: {
      show: true,
      data: "nodata.json",
      //size: 10
      names: true,
      limit: 1000,
      namelimit: 1000,
      // size: null,
      // exponent: 1.4,
      symbols: {  //DSO symbol styles, 'stroke'-parameter present = outline
        // Galaxies
        gg: { shape: "circle", fill: color.gc }, // Galaxy cluster
        g: { shape: "ellipse", fill: color.g },  // Generic galaxy
        s: { shape: "ellipse", fill: color.s },  // Spiral galaxy
        s0: { shape: "ellipse", fill: color.s0 }, // Lenticular galaxy
        sd: { shape: "ellipse", fill: color.sd },  // Dwarf galaxy
        e: { shape: "ellipse", fill: color.e }, // Elliptical galaxy
        i: { shape: "ellipse", fill: color.i }, // Irregular galaxy
        // Cluster
        oc: { shape: "circle", fill: color.oc, stroke: color.oc, width: 1.5 },
        gc: { shape: "circle", fill: color.gc, stroke: color.gc, width: 2  },
        // Nebula
        en: { shape: "square", fill: color.en },
        rn: { shape: "square", fill: color.rn },
        pn: { shape: "diamond", fill: color.pn },
        snr: { shape: "diamond", fill: color.snr },
        sfr: { shape: "square", fill: color.sfr, stroke: color.sfr, width: 2 },

        bn: { shape: "square", fill: color.bn, stroke: color.bn, width: 2 }, // Generic bright nebula
        dn: { shape: "square", fill: color.dn, stroke: color.dn, width: 2 },               // Dark nebula grey
        // TODO : bipolar nebula
        // Other
        pos: { shape: "marker", fill: "#cccccc", stroke: "#cccccc", width: 1.5 }              // Generic marker
       }
    },
    // CONSTELLATIONS
    constellations: {
      show: true,    // Show constellations
      names: true,   // Show constellation names
      desig: true,   // Show short constellation names (3 letter designations)
      namestyle: {
        fill: "#cccc99", align: "center", baseline: "middle",
        font: ["14px Helvetica, Arial, sans-serif",  // Style for constellations
          "12px Helvetica, Arial, sans-serif",  // Different fonts for diff.
          "11px Helvetica, Arial, sans-serif"]
      },// ranked constellations
      lines: true,   // Show constellation lines, style below
      linestyle: { stroke: "#cccccc", width: 1, opacity: 0.6 },
      bounds: true, // Show constellation boundaries, style below
      boundstyle: { stroke: "#cccc00", width: 0.5, opacity: 0.8, dash: [2, 4] }
    },
    // MILKY WAY
    mw: {
      show: true,
      data: 'mw.json',
      style: { fill: "#ffffff", opacity: 0.15 }
    },
    planets: {
      show: false,
      data: ""
    },
    // LINES
    lines: {
      graticule: {
        show: true, stroke: "#cccccc", width: 0.6, opacity: 0.8,
        // grid values: "outline", "center", or [lat,...] specific position
        lon: { pos: [""], fill: "#eee", font: "10px Helvetica, Arial, sans-serif" },
        // grid values: "outline", "center", or [lon,...] specific position
        lat: { pos: [""], fill: "#eee", font: "10px Helvetica, Arial, sans-serif" }
      },
      equatorial: { show: false, stroke: "#aaaaaa", width: 1.3, opacity: 0.7 },
    },
    // BACKGROUND
    background: {
      fill: "#171717",   // Area fill
      opacity: 1,
      stroke: "#2B2A34", // Outline
      width: 1.5
    },
    // HORIZON
    horizon: {  //Show horizon marker, if location is set and map projection is all-sky
      show: false,
      stroke: "#000099", // Line
      width: 1.0,
      fill: "#000000",   // Area below horizon
      opacity: 0.5
    }
  };

  /**
   * Mesure a distance between two points
   * @param p1
   * @param p2
   * @returns {number}
   */
  function distance(p1, p2) {
    var d1 = p2[0] - p1[0],
      d2 = p2[1] - p1[1];
    return Math.sqrt(d1 * d1 + d2 * d2);
  }

  /**
   *
   * @param jsonConstellation
   * @param jsonDso
   */
  function buildMap(zoom, jsonDso) {

    if (jsonDso !== undefined && "" !== jsonDso) {
      var pointStyle = {
          stroke: "rgba(255, 0, 204, 1)",
          fill: "rgba(255, 0, 204, 0.15)"
        },
        textStyle = {
          fill:"rgba(255, 0, 204, 1)",
          font: "normal bold 15px Helvetica, Arial, sans-serif",
          align: "left",
          baseline: "bottom"
        };

      c.add({type: "raw", callback: function(error, json) {
        if (error) return console.warn("WARNING CELESTAL : " + error.message);

        var dso = c.getData(jsonDso, config.transform);
        c.container.selectAll(".dsos")
          .data(dso.features)
          .enter().append("path")
          .attr("class", "dso");
        c.redraw();
        },
        redraw: function() {
          //var m = c.metrics(),
          //quadtree = d3.geom.quadtree().extent([[-1, -1], [m.width + 1, m. height + 1]])([]);

          c.container.selectAll(".dsos").each(function(d) {
            if (c.clip(d.geometry.coordinates)) {
              // get point coordinates
              var pt = c.mapProjection(d.geometry.coordinates);
              // object radius in pixel, could be varable depending on e.g. magnitude
              var r = Math.pow(20 - d.properties.mag, 0.7);

              c.setStyle(pointStyle);
              // Start the drawing path
              c.context.beginPath();
              // Thats a circle in html5 canvas
              c.context.arc(pt[0], pt[1], r, 0, 2 * Math.PI);
              // Finish the drawing path
              c.context.closePath();
              // Draw a line along the path with the prevoiusly set stroke color and line width
              c.context.stroke();
              // Fill the object path with the prevoiusly set fill color
              c.context.fill();

              // Set text styles
              //c.setTextStyle(textStyle);
              // and draw text on canvas
              //c.context.fillText(d.properties.name, pt[0] + r - 1, pt[1] - r + 1);

              // Find nearest neighbor
              var nearest = quadtree.find(pt);

              // If neigbor exists, check distance limit
              if (!nearest || distance(nearest, pt) > PROXIMITY_LIMIT) {
                // Nothing too close, add it and go on
                quadtree.add(pt)
                // Set text styles
                c.setTextStyle(textStyle);
                // and draw text on canvas with offset
                c.context.fillText(d.properties.name, pt[0] + r + 2, pt[1] + r + 2);
              }
            }
          });
        }
      });
      c.display(config);
      c.zoomBy(zoom);
    } else {
      c.display(config);
    }

  }

  return {
    map: buildMap
  };
})(Celestial, document.getElementById("geojson").dataset.const, color);

let jsonDso = JSON.parse(document.getElementById("geojson").dataset.dso);
let zoom = document.getElementById("geojson").dataset.zoom;
MAP_MODULE.map(zoom, jsonDso);