const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .enablePostCssLoader()
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/app.js')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = '3.23';
  })
  .copyFiles({
    from: './node_modules/material-symbols',
    to: 'material-icons/[name].[ext]',
    pattern: /\.(css|woff|woff2)$/
  })
  .copyFiles({
    from: './assets/img',
    to: 'img/[name].[ext]',
    pattern: /\.(svg|png|jpg|ico)$/
  })
  .copyFiles({
    from: './assets',
    pattern: /jquery.slim.min.js$/
  })

  .copyFiles([
    {
      from: './node_modules/ckeditor4/',
      to: 'ckeditor/[path][name].[ext]',
      pattern: /\.(js|css)$/,
      includeSubdirectories: false
    },
    { from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]' },
    { from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]' },
    { from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]' },
    { from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]' },
    { from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]' }
  ]);

module.exports = Encore.getWebpackConfig();
