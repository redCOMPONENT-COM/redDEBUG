var gulp = require('gulp');

// Load config
var config = require('../../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var minifyCSS   = require('gulp-minify-css');
var rename      = require('gulp-rename');
var del         = require('del');
var zip         = require('gulp-zip');
var uglify      = require('gulp-uglify');

var baseTask  = 'plugins.system.reddebug';
var extPath   = './redCORE/plugins/system/reddebug';
var wwwPluginPath = config.wwwDir + '/plugins/system/reddebug';

// Clean
gulp.task('clean:' + baseTask, function(cb) {
	return del(wwwPluginPath, {force : true});
});

// Copy
gulp.task('copy:' + baseTask, ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(wwwPluginPath));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin'
	],
	function() {
});

// Watch: plugin
gulp.task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(extPath + '/**', ['copy:' + baseTask]);
});
