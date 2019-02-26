var path = require('path');
var config = {
  entry: './Resources/public/js/AlertHandler.js',
  mode: "production",
  output: {
    filename: 'AlertHandler.js',
    path: path.resolve('./Resources/public/build/')
  },
  devtool: "source-map",
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: [{
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