var path = require('path');
var config = {
  entry: './Resources/public/js/AlertHandler.js',
  mode: "development",
  output: {
    filename: 'AlertHandler.js',
    path: path.resolve('./Resources/public/build/')
  },
  devtool: "inline-source-map",
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: [{
          loader: "echo-loader",
        }, {
          loader: "babel-loader",
        }],
        include: [
          path.resolve('.'),
        ],
      }
    ]
  }
};

module.exports = config;