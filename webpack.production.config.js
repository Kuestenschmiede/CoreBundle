/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

var path = require('path');
var config = {
  entry: {
    'AlertHandler': './src/Resources/public/vendor/js/AlertHandler.js'
  },
  mode: "production",
  output: {
    filename: '[name].js',
    path: path.resolve('./src/Resources/public/dist/js'),
    chunkFilename: '[name].bundle.[contenthash].js',
    publicPath: "bundles/con4giscore/dist/js/"
  },
  resolve: {
    modules: ['node_modules', 'src/Resources/public/vendor/js'],
    extensions: ['.js']
  },
  module: {
    rules: [
      {
        include: [
          path.resolve('.'),
          path.resolve('./src/Resources/public/vendor/js/')
        ],
      }
    ]
  },
  optimization: {
    minimize: true,
    removeAvailableModules: true,
    flagIncludedChunks: true,
    usedExports: true,
    concatenateModules: true,
    sideEffects: false,
    chunkIds: "named",
    moduleIds: "named"
  }
};

module.exports = config;