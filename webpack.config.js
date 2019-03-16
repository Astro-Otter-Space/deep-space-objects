var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
  // directory where compiled assets will be stored
  .setOutputPath('public/build/')
  // public path used by the web server to access the output path
  .setPublicPath('/build')

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
   */
  .addEntry('app', './assets/js/app.js')
  .addEntry('home', './assets/js/home.js')
  .addEntry('dso', './assets/js/dso.js')
  .addEntry('const', './assets/js/constellation.js')
  .addEntry('const_list', './assets/js/list_constellation.js')
  .addEntry('catalog', './assets/js/catalog.js')
  .addEntry('notfound', './assets/js/notfound.js')

  //D3-celestial
  // .addEntry('celestial', './node_modules/d3-celestial/celestial.js')
  // .addEntry('d3', './node_modules/d3-celestial/lib/d3.js')
  // .addStyleEntry('celestial', './node_modules/d3-celestial/celestial.css')

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()
  // .enableBuildNotifications()

  // enables Sass/SCSS support
  .enableSassLoader()

  // uncomment if you're having problems with a jQuery plugin
  //.autoProvidejQuery()

  .addPlugin(new CopyWebpackPlugin([
    { from: './assets/images', to: 'images' },
    { from: './node_modules/d3-celestial/data', to: 'data'}
  ]))

  .enableVueLoader();
;

module.exports = Encore.getWebpackConfig();
