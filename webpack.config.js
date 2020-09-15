var Encore = require('@symfony/webpack-encore');
Encore
    .setOutputPath('public/build')
    .setPublicPath('/build')
    .enableSourceMaps(!Encore.isProduction())
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/css/app.scss')
    .copyFiles({
         from: './assets/images',
    })
    .enableSassLoader()
    .autoProvidejQuery();

// export the final config
module.exports = Encore.getWebpackConfig();
