var gulp = require('gulp');

var config = require('../../gulp-config.json');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var minifyCSS   = require('gulp-minify-css');
var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');
var zip         = require('gulp-zip');

var baseTask     = 'libraries.reddebug';
var extPath      = './extensions/libraries/reddebug';
var mediaPath    = extPath + '/media/reddebug';
var wwwMediaPath = config.wwwDir + '/media/reddebug';

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':library',
		'clean:' + baseTask + ':manifest',
		'clean:' + baseTask + ':media'
	],
	function() {
});

// Clean: library
gulp.task('clean:' + baseTask + ':library', function(cb) {
	return del(wwwMediaPath, {force : true});
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function(cb) {
	return del(config.wwwDir + '/administrator/manifests/libraries/reddebug.xml', {force : true}, cb);
});

// Clean: media
gulp.task('clean:' + baseTask + ':media', function(cb) {
	return del(wwwMediaPath, {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':library',
		'copy:' + baseTask + ':manifest',
		'copy:' + baseTask + ':media'
	],
	function() {
});

// Copy: library
gulp.task('copy:' + baseTask + ':library',
	['clean:' + baseTask + ':library'], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/reddebug.xml',
		'!' + extPath + '/**/docs',
		'!' + extPath + '/**/docs/**',
		'!' + extPath + '/vendor/**/sample',
		'!' + extPath + '/vendor/**/sample/**',
		'!' + extPath + '/vendor/**/tests',
		'!' + extPath + '/vendor/**/tests/**',
		'!' + extPath + '/vendor/**/composer.json',
		'!' + extPath + '/vendor/**/*.md',
		'!' + extPath + '/vendor/**/*.sh',
		'!' + extPath + '/vendor/**/build.xml',
		'!' + extPath + '/vendor/**/phpunit*.xml',
		'!' + extPath + '/vendor/**/.*.yml',
		'!' + extPath + '/vendor/**/.editorconfig',
		'!' + mediaPath,
		'!' + mediaPath + '/**'

	])
	.pipe(gulp.dest(wwwMediaPath));
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/reddebug.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/manifests/libraries'));
});

// Copy: media
gulp.task('copy:' + baseTask + ':media', ['clean:' + baseTask + ':media'], function() {
	return gulp.src(mediaPath + '/**')
		.pipe(gulp.dest(wwwMediaPath));
});


// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':library',
		'watch:' + baseTask + ':manifest'
	],
	function() {
});

// Watch: library
gulp.task('watch:' +  baseTask + ':library', function() {
	gulp.watch([
			extPath + '/**',
			'!' + extPath + '/mipayway.xml',
			'!' + mediaPath,
			'!' + mediaPath + '/**'
		], ['copy:' + baseTask + ':library', browserSync.reload]);
});

// Watch: manifest
gulp.task('watch:' +  baseTask + ':manifest', function() {
	gulp.watch(extPath + '/reddebug.xml', ['copy:' + baseTask + ':manifest', browserSync.reload]);
});
