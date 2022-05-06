var Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('web/_assets/js/')
    .setPublicPath('/_assets/js')
    .addEntry('app', './assets/js/app.js')
    .disableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

module.exports = Encore.getWebpackConfig();