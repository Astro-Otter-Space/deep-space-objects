import Celestial from 'd3-celestial';

/**
 * BIG QUESTION :
 * get data from URL (Symfony JsonResponse) or passed with data attributes ?
 */
export default class Map {

  constructor(jsonConstellation, jsonData)
  {
    this.jsonConstellation = jsonConstellation;
    this.jsonData = jsonData;
  }

  /**
   *
   * @returns {{}}
   */
  get buildConfig()
  {
    let config = {
      width: 0,
      projection: "aitoff",
      transform: "equatorial",
      center: null, // TODO : Fix it to const ID
      adaptable: true,
      interactive: true,
      form: false,
      location: false,
      controls: false,
      container: "map",
      stars: {
        colors: false,
        names: false,
        style: { fill: "#000", opacity:1 },
        limit: 6,
        size:5
      },
      dsos: {

      },
      constellations: {

      },
      mw: {
        show: false,
      },
      lines: {

      },
      background: {

      },
      horizon: {  //Show horizon marker, if location is set and map projection is all-sky
        show: false,
        stroke: "#000099", // Line
        width: 1.0,
        fill: "#000000",   // Area below horizon
        opacity: 0.5
      }
    };

    return config;
  }

  /**
   *
   * @param jsonConstellation
   * @param jsonData
   */
  get buildMap()
  {
    let config = this.buildConfig();
    let jsonConstellation = this.jsonConstellation;
    let jsonDso = this.jsonData;

    // Add constellation
    Celestial.add({type: "line", callback: function(jsonConstellation, err) {
        if (error) return console.warn(error);
        let constellation = Celestial.getData(jsonConstellation, config.transform);


        Celestial.redraw();
      }, redraw: function() {

      }
    });

    // Add Item
    Celestial.add({type: "dso", callback: function (jsonDso, err) {
        if (error) return console.warn(error);
        let dso = Celestial.getData(jsonDso, config.transform);
      }, redraw: function() {

      }
    });

    // Display map
    Celestial.display(config);
  }

};
