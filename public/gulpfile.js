'use strict';

var gulp = require('gulp'),
  compilerGulp = require('can-compile/gulp.js');
var stealTools = require('steal-tools');

// Build the App
gulp.task('steal-build', function() {
  stealTools.build({
    main: 'main',
    bundlesPath: '../assets',
    config: 'main/stealconfig.js'
  });
});
gulp.watch('main/**/*.*', ['steal-build']);

// CanJS TEMPLATES
var options = {
  src: ['**/*.stache'],
  out: 'assets/main.stache.js',
  version: '2.1.3'
};
compilerGulp.task('main-views', options, gulp);
compilerGulp.watch('main-views', options, gulp);


// The default task (called when you run `gulp` from cli)
gulp.task('default', [
  'steal-build',
  'main-views',
  'main-views-watch',
]);