const pkg = require('./package.json')

const path = require('path')
const glob = require('glob')
const config = require('config')
const webpack = require('webpack')

const CleanWebpackPlugin = require('clean-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const WebpackPwaManifest = require('webpack-pwa-manifest')
const WorkboxPlugin = require('workbox-webpack-plugin')
const PurifyCSSPlugin = require('purifycss-webpack')
const HtmlReplaceWebpackPlugin = require('html-replace-webpack-plugin')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin

const isDev = process.argv.indexOf('development') > 0

const webpackConfig = {
  entry: {
    app: path.resolve(__dirname, 'src/app/app.js')
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'app/[name].[hash].js',
    publicPath: './',
    sourceMapFilename: '[file].map'
  },
  devtool: isDev ? 'inline-source-map' : false,
  module: {
    rules: [
      {
        test: /\.(html|php)$/,
        use: [
          {
            loader: 'html-loader',
            options: { minimize: true }
          }
        ]
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: 'css-loader',
            options: { sourceMap: true, minimize: true }
          },
          {
            loader: 'postcss-loader'
          },
          {
            loader: 'sass-loader',
            options: { sourceMap: true }
          }
        ],
        include: [path.resolve(__dirname, 'src/assets/styles')]
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: 'css-loader',
            options: { sourceMap: true, minimize: true }
          }
        ],
        include: [path.resolve(__dirname, 'src/assets/styles')]
      },
      {
        test: /\.(png|jpg|gif)$/i,
        use: [
          {
            loader: 'file-loader',
            options: { name: 'assets/images/[name].[ext]' }
          }
        ]
      },
      {
        test: /\.(woff(2)?|ttf|eot|svg)$/,
        use: [{
          loader: 'file-loader',
          options: { name: 'assets/fonts/[name].[ext]' }
        }]
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin(['dist']),
    new HtmlWebpackPlugin({
      filename: 'index.php',
      template: './src/index.template.php',
      inject: 'head',
      favicon: path.resolve(__dirname, 'src/assets/favicons/favicon.ico'),
      minify: {
        minifyCSS: true,
        minifyJS: true,
        collapseWhitespace: true,
        collapseInlineTagWhitespace: true,
        preserveLineBreaks: false,
        removeAttributeQuotes: true,
        removeComments: true
      }
    }),
    new WebpackPwaManifest({
      name: 'Průjezd vozidel - Plzeňský kraj',
      short_name: 'PVPK',
      description: 'Zobrazení dat o průjezdu vozidel pro Plzeňský kraj',
      background_color: '#ffffff',
      theme_color: '#ffffff',
      icons: [
        {
          src: path.resolve(__dirname, 'src/assets/favicons/favicon.png'),
          sizes: [24, 32, 48, 192, 512],
          destination: path.join('assets', 'favicons')
        }
      ],
      ios: false,
      inject: true,
      fingerprints: false
    }),
    new WorkboxPlugin.GenerateSW({
      swDest: 'sw.js',
      importWorkboxFrom: 'local',
      clientsClaim: true,
      skipWaiting: true,
      runtimeCaching: [
        {
          urlPattern: /\.(?:png|gif|jpg|jpeg|svg|ico|woff(2)?|ttf|eot)$/,
          handler: 'cacheFirst'
        },
        {
          urlPattern: /\.(?:css|js)$/,
          handler: 'staleWhileRevalidate'
        },
        {
          urlPattern: new RegExp('^' + config.API_URL + '.*$', 'i'),
          handler: 'staleWhileRevalidate'
        }
      ]
    }),
    new MiniCssExtractPlugin({
      filename: 'assets/css/[name].[contenthash].css'
    }),
    new PurifyCSSPlugin({
      paths: glob.sync(path.join(__dirname, 'src/**/*'))
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jquery: 'jquery',
      jQuery: 'jquery',
      'window.$': 'jquery',
      'window.jQuery': 'jquery',
      moment: 'moment',
      'window.moment': 'moment'
    }),
    new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
    new webpack.DefinePlugin({
      __VERSION__: JSON.stringify(pkg.version),
      __API_URL__: JSON.stringify(config.API_URL),
      __TOKEN_GENERATOR_PATH__: JSON.stringify(config.TOKEN_GENERATOR_PATH)
    }),
    new HtmlReplaceWebpackPlugin([
      {
        pattern: '__TOKEN_GENERATOR_PATH__',
        replacement: JSON.stringify(config.TOKEN_GENERATOR_PATH)
      }
    ]),

    //new BundleAnalyzerPlugin()
  ],
  optimization: {
    minimize: !isDev,
    minimizer: [
      new UglifyJsPlugin({
        uglifyOptions: {
          output: {
            comments: false,
            beautify: false
          }
        }
      }),
      new OptimizeCSSAssetsPlugin({})
    ],
    splitChunks: {
      cacheGroups: {
        commons: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendor',
          chunks: 'all'
        }
      }
    }
  }
}

module.exports = webpackConfig
