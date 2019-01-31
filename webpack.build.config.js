const webpack = require('webpack');
const getBundleData = require('./webpack/build/tools.js')
const path = require('path')
const bundlePaths = require(path.join(process.cwd(), './web/js/require-paths'))
const sourceDirectory = process.cwd()
const bundleData = getBundleData(bundlePaths, sourceDirectory);

console.log(getBundleData)

module.exports = {
    target: 'web',
    entry: ['babel-polyfill', path.resolve(sourceDirectory, './web/bundles/pimui/js/index.js')],
    output: {
      path: path.resolve('./web/dist/'),
      publicPath: '/dist/',
      filename: '[name].min.js',
      chunkFilename: '[name].bundle.js',
    },
    devtool: 'source-map',
    resolve: {
      symlinks: false,
      alias: {},
      modules: [path.resolve('./web/bundles'), path.resolve('./node_modules')],
      extensions: ['.js', '.json', '.ts', '.tsx'],
    },
}
