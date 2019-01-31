const webpack = require('webpack');
const getBundleData = require('./webpack/build/tools.js')
const path = require('path')
const bundlePaths = require(path.join(process.cwd(), './web/js/require-paths'))
const sourceDirectory = process.cwd()
const bundleData = getBundleData(bundlePaths, sourceDirectory);
const _ = require('lodash')

module.exports = {
    target: 'web',
    entry: ['babel-polyfill', path.resolve(sourceDirectory, './web/bundles/pimui/js/index.js')],
    output: {
      path: path.resolve('./web/dist/'),
      publicPath: '/dist/',
      filename: '[name].min.js',
      chunkFilename: '[name].bundle.js',
    },
    module: {
        rules: [
            {
                test: /\.html$/,
                exclude: /node_modules|spec/,
                use: [
                  {
                    loader: 'raw-loader',
                    options: {},
                  },
                ],
              },
            {
            test: /\.tsx?$/,
            use: [
              {
                loader: 'ts-loader',
                options: {
                  configFile: path.resolve(__dirname, 'tsconfig.json'),
                  context: path.resolve(sourceDirectory),
                },
              },
            //   {
            //     loader: path.resolve(__dirname, 'webpack/config-loader'),
            //     options: {
            //       configMap: config,
            //     },
            //   },
            ],
            include: /(web\/bundles)/,
            exclude: /lib|node_modules|vendor|tests|src|packages/,
          }]
    },
    devtool: 'source-map',
    resolve: {
      symlinks: false,
      alias: Object.assign(
          {
              'pimui/js/fos-routing-wrapper': path.resolve(sourceDirectory, './vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js'),
              'fos-routing-base': path.resolve(sourceDirectory, './vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js'),
              'require-polyfill': path.resolve('vendor/akeneo/pim-community-dev/webpack/require-polyfill.js'),
            'require-context': path.resolve('vendor/akeneo/pim-community-dev/webpack/require-context.js'),
            'module-registry': path.resolve(sourceDirectory, './web/js/module-registry.js'),
            routes: path.resolve(sourceDirectory, './web/js/routes.js'),
          },
          _.mapKeys(bundleData.aliases, (path, key) => `${key}$`)
      ),
      modules: [path.resolve(sourceDirectory, './web/bundles'), path.resolve(sourceDirectory, './node_modules')],
      extensions: ['.js', '.json', '.ts', '.tsx'],
    },
}
