var gulp = require('gulp');

var config = require('../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask           = 'packages.reddebug';
var extPath            = '../';
var wwwManifestsFolder = config.wwwDir + '/administrator/manifests/packages';
var manifestFile       = 'pkg_reddebug.xml';

// Clean
gulp.task('clean:' + baseTask,['clean:' + baseTask + ':manifest'],
	function() {
		return true;
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function(cb) {
	return del(wwwManifestsFolder + '/' + manifestFile, {force : true});
});

// Copy
gulp.task('copy:' + baseTask, [
		'copy:' + baseTask + ':manifest'
	],function() {
		return true;
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/' + manifestFile)
		.pipe(gulp.dest(wwwManifestsFolder));
});

// Watch
gulp.task('watch:' + baseTask,[
		'watch:' + baseTask + ':manifest'
	],
	function() {
		return true;
});

// Watch: manifest
gulp.task('watch:' + baseTask + ':manifest', function() {
	gulp.watch(extPath + '/' + manifestFile, ['copy:' + baseTask + ':manifest']);
});
